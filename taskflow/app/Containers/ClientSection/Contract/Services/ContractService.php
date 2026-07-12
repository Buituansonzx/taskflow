<?php

namespace App\Containers\ClientSection\Contract\Services;

use App\Containers\ClientSection\Contract\Models\Contract;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\PhpWord;
use Carbon\Carbon;

class ContractService
{
    /**
     * Import Excel data into the contracts table.
     * Returns the number of imported rows.
     */
    public function importFromExcel(bool $fresh = false, ?string $filePath = null): int
    {
        if ($fresh) {
            Contract::truncate();
        }

        $filePath = $filePath ?? storage_path('app/tep_hop_dong.xlsx');
        $reader = IOFactory::createReader('Xlsx');

        // Suppress XML parser warnings from malformed metadata in some Excel files
        $previousErrorReporting = error_reporting();
        error_reporting($previousErrorReporting & ~E_WARNING);
        $spreadsheet = $reader->load($filePath);
        error_reporting($previousErrorReporting);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $imported = 0;

        for ($row = 1; $row <= $highestRow; $row++) {
            $afValue = trim((string) $sheet->getCell('AF' . $row)->getValue());

            // Check lỏng: chỉ cần chứa "đề xuất" hoặc "HĐ/NT"
            if (!$this->isProposedContract($afValue)) {
                continue;
            }

            // Skip if already imported
            if (!$fresh && Contract::where('excel_row', $row)->exists()) {
                continue;
            }

            // Parse date from column A (Excel serial date)
            $contractDateValue = $sheet->getCell('A' . $row)->getValue();
            $contractDate = null;
            if ($contractDateValue) {
                try {
                    $contractDate = ExcelDate::excelToDateTimeObject($contractDateValue)->format('Y-m-d');
                } catch (\Exception $e) {
                    $contractDate = null;
                }
            }

            $publishDateValue = $sheet->getCell('C' . $row)->getValue();
            $publishDate = null;
            if ($publishDateValue) {
                try {
                    $publishDate = ExcelDate::excelToDateTimeObject($publishDateValue)->format('Y-m-d');
                } catch (\Exception $e) {
                    $publishDate = null;
                }
            }

            // Parse personal info from column AA
            $personalInfoRaw = (string) $sheet->getCell('AA' . $row)->getValue();
            $personalInfo = $this->parsePersonalInfo($personalInfoRaw);

            $batch = (string) $sheet->getCell('B' . $row)->getValue();
            $tiktokUsername = (string) $sheet->getCell('E' . $row)->getValue();
            $product = (string) $sheet->getCell('N' . $row)->getValue();

            Contract::updateOrCreate(
                [
                    'tiktok_username' => $tiktokUsername,
                    'product' => $product,
                    'batch' => $batch,
                ],
                [
                    'excel_row' => $row,
                    'contract_date' => $contractDate,
                    'publish_date' => $publishDate,
                    'batch' => $sheet->getCell('B' . $row)->getValue(),
                    'pic' => $sheet->getCell('D' . $row)->getValue(),
                    'tiktok_username' => $sheet->getCell('E' . $row)->getValue(),
                    'tiktok_url' => $sheet->getCell('F' . $row)->getValue(),
                    'tiktok_video_url' => $sheet->getCell('X' . $row)->getValue(),
                    'amount_raw' => (string) $sheet->getCell('G' . $row)->getValue(),
                    'category' => $sheet->getCell('M' . $row)->getValue(),
                    'product' => $sheet->getCell('N' . $row)->getValue(),
                    'num_posts' => (int) $sheet->getCell('H' . $row)->getValue() ?: 1,
                    'contract_number' => $sheet->getCell('Y' . $row)->getValue(),
                    'personal_info_raw' => $personalInfoRaw,
                    'status_af' => $afValue,
                    'koc_name' => $sheet->getCell('AK' . $row)->getValue(),
                    'full_name' => $personalInfo['full_name'],
                    'cccd' => $personalInfo['cccd'],
                    'cccd_date' => $personalInfo['cccd_date'],
                    'cccd_place' => $personalInfo['cccd_place'],
                    'tax_code' => $personalInfo['tax_code'],
                    'address' => $personalInfo['address'],
                    'phone' => $personalInfo['phone'],
                    'email' => $personalInfo['email'],
                    'bank_account' => $personalInfo['bank_account'],
                    'bank_name' => $personalInfo['bank_name'],
                    'account_holder' => $personalInfo['account_holder'],
                ]
            );

            $imported++;
        }

        return $imported;
    }

    /**
     * Check lỏng: cột AF chứa "Đề xuất" hoặc "HĐ/NT"
     */
    public function isProposedContract(string $afValue): bool
    {
        $lower = mb_strtolower($afValue);
        return mb_strpos($lower, 'đề xuất') !== false
            || mb_strpos($lower, 'hđ/nt') !== false;
    }

    /**
     * Generate the BBNT DOCX file for the given contract.
     * Returns the absolute path to the generated file.
     */
    public function generateAcceptanceRecord(Contract $contract): string
    {
        $templatePath = storage_path('app/BBNT.docx');
        
        if (!file_exists($templatePath)) {
            throw new \Exception("BBNT template file not found at " . $templatePath);
        }

        $data = $this->prepareContractData($contract);
        
        $filename = 'BBNT_' . $contract->tiktok_username . '_' . date('YmdHis') . '.docx';
        $savePath = storage_path('app/contracts/' . $filename);
        
        if (!is_dir(storage_path('app/contracts'))) {
            mkdir(storage_path('app/contracts'), 0755, true);
        }
        
        copy($templatePath, $savePath);
        
        $zip = new \ZipArchive();
        if ($zip->open($savePath) === true) {
            $xmlString = $zip->getFromName('word/document.xml');
            
            // Replace TikTok Video Link (which might not be red text)
            $videoUrl = $data['tiktok_video_url'] ?? '';
            $xmlString = str_replace('https://vt.tiktok.com/ZSQqpmqtd/', htmlspecialchars($videoUrl), $xmlString);
            
            $dom = new \DOMDocument();
            $dom->loadXML($xmlString);
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            
            // Find all text runs that are styled with red color
            $runs = $xpath->query('//w:r[w:rPr/w:color[@w:val="ff0000"] or w:rPr/w:color[@w:val="FF0000"]]');
            
            $bbntContext = ''; // Track context across runs
            
            foreach ($runs as $run) {
                $tNodes = $xpath->query('.//w:t', $run);
                foreach ($tNodes as $tNode) {
                    $originalText = $tNode->nodeValue;
                    $newText = $this->mapBBNTRedTextToValue($originalText, $data, $bbntContext);
                    if ($newText !== null) {
                        $tNode->nodeValue = htmlspecialchars($newText);
                    }
                }
            }
            
            $zip->addFromString('word/document.xml', $dom->saveXML());
            $zip->close();
        }
        
        return $savePath;
    }

    /**
     * Parse personal info from column AA text (various formats).
     */
    private function parsePersonalInfo(string $rawText): array
    {
        $info = [
            'full_name' => '',
            'cccd' => '',
            'cccd_date' => '',
            'cccd_place' => '',
            'tax_code' => null,
            'address' => '',
            'phone' => '',
            'email' => '',
            'bank_account' => '',
            'bank_name' => '',
            'account_holder' => '',
        ];

        if (empty(trim($rawText))) {
            return $info;
        }

        // Normalize text to composed form (NFC) to handle decomposed accents (like SỐ in row 255)
        if (class_exists('Normalizer')) {
            $rawText = \Normalizer::normalize($rawText, \Normalizer::FORM_C);
        }

        // Clean unicode full-width digits and bold chars (like 𝟑𝟖𝟏𝟒𝟎𝟐𝟎𝟏𝟐𝟑𝟒)
        $rawText = str_replace(
            ['０','１','２','３','４','５','６','７','８','９','０','𝟏','𝟐','𝟑','𝟒','𝟓','𝟔','𝟕','𝟖','𝟗'],
            ['0','1','2','3','4','5','6','7','8','9','0','1','2','3','4','5','6','7','8','9'],
            $rawText
        );
        $rawText = preg_replace_callback('/[𝐀-𝐙𝐚-𝐳]/u', function ($matches) {
            $char = $matches[0];
            $ord = mb_ord($char);
            if ($ord >= 0x1D400 && $ord <= 0x1D419) return chr($ord - 0x1D400 + 65); // Bold A-Z
            if ($ord >= 0x1D41A && $ord <= 0x1D433) return chr($ord - 0x1D41A + 97); // Bold a-z
            return $char;
        }, $rawText);
        $rawText = str_replace(['TÀI KHOẢN', 'SỐ TÀI KHOẢN', 'NGÂN HÀNG'], ['TÀI KHOẢN', 'SỐ TÀI KHOẢN', 'NGÂN HÀNG'], $rawText);

        if (empty(trim($rawText))) {
            return $info;
        }

        // Special case: Unlabelled data separated by newlines (Row 627)
        $lines = array_values(array_filter(array_map('trim', explode("\n", $rawText))));
        $hasLabels = false;
        foreach ($lines as $line) {
            // Only check for colon as a strict label indicator
            if (preg_match('/:/', $line)) {
                $hasLabels = true;
                break;
            }
        }
        if (!$hasLabels && count($lines) >= 10) {
            // Looks like pure data in order: Name, CCCD, Date, Place, MST, Address, Phone, Email, STK, Holder, Bank
            $info['full_name'] = $lines[0];
            $info['cccd'] = $lines[1];
            $info['cccd_date'] = $lines[2];
            $info['cccd_place'] = $lines[3];
            $info['address'] = $lines[5];
            $info['phone'] = $lines[6];
            $info['email'] = $lines[7];
            $info['bank_account'] = $lines[8];
            $info['account_holder'] = $lines[9];
            $info['bank_name'] = $lines[10] ?? '';
            return $info;
        }

        // Full name
        if (preg_match('/(?:Họ\s*(?:và|&|\/)?\s*[Tt]ên(?:.*?)|Đại\s*diện\s*|Tên\s*:)\s*[:\-\s\.\–]+\s*([A-ZĐa-zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹ\s]+)(?:\n|$)/ui', $rawText, $m)) {
            $info['full_name'] = trim($m[1]);
        } elseif (preg_match('/^([A-ZĐ][a-zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]+(?:\s+[A-ZĐ][a-zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]+)+)$/um', $rawText, $m)) {
            // First line or a line with just a name
            $info['full_name'] = trim($m[1]);
        }

        // CCCD/CMT
        if (preg_match('/(?:CCCD|CMT|Căn\s*cước(?:.*?)|Số\s*CCCD(?:.*?)|CCCD\/CMT(?:.*?)|CCCD\s*số|Can\s*cuoc|Số\s*CMND\/CCCD\/[^\:]+)\s*[:\-\s]+\s*([\d]+)/ui', $rawText, $m)) {
            $info['cccd'] = trim($m[1]);
        } elseif (preg_match('/(?:cccd\s*(?:và|&|\+)?\s*(?:mst|thuế))\s*[:\-\s]+\s*([\d]+)/ui', $rawText, $m)) {
            $info['cccd'] = trim($m[1]);
        } elseif (preg_match('/^([\d]{9,12})$/um', $rawText, $m)) {
             // Fallback for lines with just numbers that look like CCCD
             if (empty($info['cccd']) && strlen(trim($m[1])) >= 9 && strlen(trim($m[1])) <= 12) {
                 $info['cccd'] = trim($m[1]);
             }
        }

        // CCCD issue date
        if (preg_match('/(?:Ngày\s*cấp|Cấp\s*ngày)\s*[:\-\s]+\s*(.+?)(?:\n|$)/ui', $rawText, $m)) {
            $info['cccd_date'] = trim($m[1]);
        }

        // CCCD issue place
        if (preg_match('/(?:Nơi\s*cấp|Tại)\s*[:\-\s]+\s*(.+?)(?:\n|$)/ui', $rawText, $m)) {
            $info['cccd_place'] = trim($m[1]);
        }

        // Tax code (MST)
        if (preg_match('/(?:MST|Mã\s*số\s*thuế)\s*[:\-\s]+\s*([\d\-]+)/ui', $rawText, $m)) {
            $info['tax_code'] = trim($m[1]);
        }

        // Address
        if (preg_match('/(?:Hộ\s*khẩu\s*TT|Địa\s*chỉ\s*thường\s*trú|Địa\s*chỉ(?:.*?)|Quê\s*quán)\s*[:\-\s]+\s*([^\n]+)/ui', $rawText, $m)) {
            $info['address'] = trim($m[1]);
        }

        // Phone
        if (preg_match('/(?:SĐT|Số\s*điện\s*thoại|Điện\s*thoại|Sđt|Di\s*động)\s*[:\-\s]+\s*([\d\s\.]+)/ui', $rawText, $m)) {
            $info['phone'] = trim($m[1]);
        } elseif (preg_match('/^([\d\s\.]{10,12})$/um', $rawText, $m) && empty($info['phone'])) {
            $info['phone'] = trim($m[1]);
        }

        // Email
        if (preg_match('/(?:Email|E-mail|E\s*mail|Gmail)\s*[:\-\s]+\s*([\w\.\-\+]+@[\w\.\-]+\.\w+)/ui', $rawText, $m)) {
            $info['email'] = trim($m[1]);
        } elseif (preg_match('/([\w\.\-\+]+@[\w\.\-]+\.\w+)/ui', $rawText, $m) && empty($info['email'])) {
            $info['email'] = trim($m[1]);
        }

        // STK/Số TK/Số tài khoản: 1234567890
        if (preg_match('/(?:^|\n|\|)\s*(?:-|\*|❥)?\s*(?:STK|Số\s*TK|Tk|Số\s*tài\s*khoản(?:\/[^\:]+)?|STK\s*\+\s*Tên\s*ngân\s*hàng)(?:.*?)\s*[:\-\s\–]\s*([^\n]+)/uim', $rawText, $m)) {
            $bankLine = trim($m[1]);
            if (preg_match('/([\d]+)/', $bankLine, $numMatch)) {
                $info['bank_account'] = trim($numMatch[1]);
            }
            if (preg_match('/(Vietcombank|Viettinbank|Vietinbank|VPBank|VP\s*BANK|Techcombank|MB\s*Bank|MBBank|MB|ACB|BIDV|Agribank|Argibank|TPBank|Sacombank|HDBank|VIB|SHB|OCB|SeABank|Kienlongbank|LienVietPostBank|NamABank|BaoVietBank|PGBank|SCB)/ui', $bankLine, $bankMatch)) {
                $info['bank_name'] = trim($bankMatch[1]);
            }
            // Try extracting account holder from the end of the line (after dash/hyphen)
            if (empty($info['account_holder']) && preg_match('/(?:-|\–)\s*([A-ZĐa-zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹ\s]+)$/u', $bankLine, $holderMatch)) {
                $holder = trim($holderMatch[1]);
                if (mb_strlen($holder) > 2) {
                    $info['account_holder'] = $holder;
                }
            }
        } elseif (preg_match('/(?:^|\n|\|)\s*(?:Chủ\s*tài\s*khoản|Tên\s*ctk|Ctk)(?:.*?)\s*[:\-\–]\s*(.+?)(?:\n|$)/ui', $rawText, $m)) {
            $bankLine = trim($m[1]);
            if (preg_match('/([\d]{6,})/', $bankLine, $numMatch)) {
                $info['bank_account'] = trim($numMatch[1]);
            }
            if (preg_match('/(Vietcombank|Viettinbank|Vietinbank|VPBank|VP\s*BANK|Techcombank|MB\s*Bank|MBBank|MB|ACB|BIDV|Agribank|Argibank|TPBank|Sacombank|HDBank|VIB|SHB|OCB|SeABank|Kienlongbank|LienVietPostBank|NamABank|BaoVietBank|PGBank|SCB)/ui', $bankLine, $bankMatch)) {
                $info['bank_name'] = trim($bankMatch[1]);
            }
        }

        // Try extracting bank account if only a standalone number exists that hasn't been matched
        if (empty($info['bank_account']) && preg_match('/^([\d]{6,15})$/um', $rawText, $m)) {
            if ($m[1] !== $info['cccd'] && $m[1] !== $info['phone']) {
                $info['bank_account'] = trim($m[1]);
            }
        }

        // Bank name (separate line)
        if (empty($info['bank_name'])) {
            if (preg_match('/(?:^|\n|\|)\s*(?:-|\*|❥)?\s*(?:Ngân\s*[Hh]àng|Tại\s*ngân\s*hàng|Tại|NGÂN HÀNG)\s*[:\-\s]\s*([^\n]+)/uim', $rawText, $m)) {
                $info['bank_name'] = trim($m[1]);
            }
            // Try finding bank name anywhere in text
            if (empty($info['bank_name']) && preg_match('/(Vietcombank|Viettinbank|Vietinbank|VPBank|VP\s*BANK|Techcombank|MB\s*Bank|MBBank|MB|ACB|BIDV|Agribank|Argibank|TPBank|Sacombank|HDBank|VIB|SHB|OCB|SeABank|Kienlongbank|LienVietPostBank|NamABank|BaoVietBank|PGBank|SCB)/ui', $rawText, $m)) {
                $info['bank_name'] = trim($m[1]);
            }
        }

        // Account holder name
        if (empty($info['account_holder'])) {
            if (preg_match('/(?:Chủ\s*tài\s*khoản(?:\/[^\:]+)?|Tên\s*(?:ctk|CTK|chủ\s*TK)|Tên\s*TK|Tài\s*Khoản)\s*(?:.*?)\s*[:\-\s]\s*([^\n]+)/ui', $rawText, $m)) {
                $holder = trim($m[1]);
                $holder = preg_replace('/\-.*$/', '', $holder); // strip anything after a dash if on same line
                if (preg_match('/([A-ZĐa-zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹ\s]+)/u', $holder, $hMatch)) {
                    $info['account_holder'] = trim($hMatch[1]);
                } else {
                    $info['account_holder'] = trim($holder);
                }
            } else {
                // If it looks like uppercase name standalone
                if (preg_match('/^([A-ZĐ][A-ZĐ\s]+)$/um', str_replace(['❥', ' '], ['', ' '], $rawText), $m)) {
                    $info['account_holder'] = trim($m[1]);
                } else {
                    $info['account_holder'] = $info['full_name'];
                }
            }
        }
        
        // Fallback for Row 183 (CHÂU THỊ THANH THƯ)
        if (empty($info['account_holder']) && preg_match('/VIB\s*(?:-|\–|\p{Pd})\s*([A-ZĐ][A-ZĐ\s]+)$/uim', $rawText, $m)) {
            $info['account_holder'] = trim($m[1]);
        }
        
        // Fallback for Row 247 (Nguyễn Thị Huyền)
        if (empty($info['bank_account']) && preg_match('/Stk\s*[:\-]\s*([\d]+)/ui', $rawText, $m)) {
            $info['bank_account'] = trim($m[1]);
        }
        if ((empty($info['account_holder']) || str_contains(strtolower($info['account_holder']), 'ngân hàng')) && preg_match('/Ctk\s*[:\-]\s*([^\n]+)/ui', $rawText, $m)) {
            $info['account_holder'] = trim($m[1]);
        }
        
        // Final fallback: If holder is still missing, use the full name
        if (empty($info['account_holder'])) {
            $info['account_holder'] = mb_strtoupper($info['full_name']);
        }

        return $info;
    }

    /**
     * Generate a DOCX contract for the given Contract model.
     * Returns the file path to the generated DOCX.
     */
    public function generateContract(Contract $contract): string
    {
        $templatePath = storage_path('app/hop_dong_mau.docx');
        
        $outputDir = storage_path('app/contracts');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $filename = 'HopDong_' . ($contract->tiktok_username ?? $contract->excel_row) . '_' . now()->format('YmdHis') . '.docx';
        $outputPath = $outputDir . '/' . $filename;
        
        // Copy the pristine template to output path first
        copy($templatePath, $outputPath);

        // Prepare replacement data
        $data = $this->prepareContractData($contract);

        // Use ZipArchive to modify the internal document.xml to prevent corruption caused by PHPWord
        $zip = new \ZipArchive();
        if ($zip->open($outputPath) === true) {
            $xmlString = $zip->getFromName('word/document.xml');
            
            $dom = new \DOMDocument();
            // Suppress warnings from invalid XML namespace bindings
            libxml_use_internal_errors(true);
            $dom->loadXML($xmlString);
            libxml_clear_errors();
            
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            
            // Find all w:r elements that contain w:color ff0000 or FF0000
            $runs = $xpath->query('//w:r[w:rPr/w:color[@w:val="ff0000" or @w:val="FF0000"]]');
            
            foreach ($runs as $run) {
                $tNodes = $xpath->query('.//w:t', $run);
                foreach ($tNodes as $tNode) {
                    $text = $tNode->nodeValue;
                    $newText = $this->mapRedTextToValue($text, $data);
                    
                    if ($newText !== null && $newText !== $text) {
                        // Use htmlspecialchars to safely escape the replacement XML text
                        $tNode->nodeValue = htmlspecialchars($newText, ENT_XML1, 'UTF-8');
                    }
                }
            }
            
            // Save modified XML back into the archive
            $zip->addFromString('word/document.xml', $dom->saveXML());
            $zip->close();
        }

        // Mark as generated
        $contract->update(['is_generated' => true]);

        return $outputPath;
    }

    /**
     * Prepare the data map for replacing in the DOCX template.
     */
    private function prepareContractData(Contract $contract): array
    {
        $amount = $this->parseAmount($contract->amount_raw);
        $amountFormatted = number_format($amount, 0, ',', ',');
        $amountWords = $this->numberToVietnameseWords($amount);

        // Format contract date
        $contractDate = $contract->contract_date;
        $dayMonth = $contractDate
            ? sprintf('%02d tháng %02d ', $contractDate->day, $contractDate->month)
            : '';
        $year = $contractDate ? (string) $contractDate->year : '2026';

        // Product description for the table
        $productDesc = $this->buildProductDescription($contract);
        $tiktokUrl = $contract->tiktok_url ?? '';

        return [
            // Contract number
            'contract_number' => $contract->contract_number ?? '',
            // Date
            'day_month' => $dayMonth,
            'year' => $year,
            // Personal info (Bên B)
            'cccd' => $contract->cccd ?? '',
            'cccd_date' => $contract->cccd_date ?? '',
            'cccd_place' => $contract->cccd_place ?? '',
            'tax_code' => $contract->tax_code ?? ($contract->cccd ?? ''),
            'address' => $contract->address ?? '',
            'phone' => $contract->phone ?? '',
            'email' => $contract->email ?? '',
            'bank_account' => $contract->bank_account ?? '',
            'account_holder' => mb_strtoupper($contract->account_holder ?? $contract->full_name ?? ''),
            'account_holder_title' => $contract->account_holder ?? $contract->full_name ?? '',
            'bank_name' => $contract->bank_name ?? '',
            'full_name' => $contract->full_name ?? '',
            // Amount
            'amount' => $amountFormatted,
            'amount_words' => $amountWords,
            // Product / TikTok
            'product_desc' => $productDesc,
            'tiktok_url' => $tiktokUrl,
            'tiktok_video_url' => $contract->tiktok_video_url ?? '',
            'product_name' => $contract->product ?? 'Sahemul',
            'category' => $contract->category ?? '',
            'num_posts' => $contract->num_posts ?? 1,
            'publish_date' => $contract->publish_date ? $contract->publish_date->format('Y-m-d') : null,
        ];
    }


    /**
     * Map a red text fragment from the template to its replacement value.
     */
    private function mapRedTextToValue(string $text, array $data): ?string
    {
        $trimmed = trim($text);

        // Contract number: " SAHKCN010726-274/WE"
        if (preg_match('/SAH.*\/WE/', $trimmed)) {
            return ' ' . $data['contract_number'];
        }

        // Date: "01 tháng 07 "
        if (preg_match('/\d+\s*tháng\s*\d+/', $trimmed)) {
            return $data['day_month'];
        }

        // CCCD label+value: ": 001301019638" after "CCCD" label
        if ($trimmed === 'CCCD' || preg_match('/^CCCD\s/', $trimmed)) {
            return $trimmed; // Keep label as-is
        }

        // Fields with label + colon pattern ": value"
        if (str_starts_with($trimmed, ':')) {
            // Determine context by checking what the value looks like
            return $this->replaceColonValue($text, $trimmed, $data);
        }

        // "Số điện thoại liên lạc  : 097 476 2925"
        if (mb_strpos($trimmed, 'Số điện thoại liên lạc') !== false) {
            return 'Số điện thoại liên lạc  : ' . $data['phone'];
        }

        // Bank info in section 2.3
        if (mb_strpos($trimmed, 'Tên tài khoản') !== false) {
            return '●        Tên tài khoản: ' . $data['account_holder'];
        }
        if (mb_strpos($trimmed, 'Số tài khoản') !== false) {
            return '●        Số tài khoản: ' . $data['bank_account'];
        }
        if (mb_strpos($trimmed, 'Ngân hàng') !== false && mb_strpos($trimmed, '●') !== false) {
            return '●        Ngân hàng : ' . $data['bank_name'];
        }

        // Section headers that are red but should keep their labels
        // "Cấp ngày" label
        if (preg_match('/^Cấp\s*ngày/u', $trimmed)) {
            return $text; // Keep label
        }
        if (preg_match('/^Nơi\s*cấp/u', $trimmed)) {
            return $text; // Keep label
        }
        if (preg_match('/^Mã\s*số\s*thuế/u', $trimmed)) {
            return $text; // Keep label
        }
        if (preg_match('/^Địa\s*chỉ\s*thường\s*trú/u', $trimmed)) {
            return $text; // Keep label
        }
        if (preg_match('/^Email/u', $trimmed)) {
            return $text; // Keep label
        }
        if (preg_match('/^Số\s*TK/u', $trimmed)) {
            return $text; // Keep label
        }
        if (preg_match('/^Tên\s*TK/u', $trimmed)) {
            return $text; // Keep label
        }
        if (preg_match('/^Ngân\s*hàng$/u', $trimmed)) {
            return $text; // Keep label
        }

        // Table: product count "Combo 2 video"
        if (preg_match('/^Combo\s+\d+\s+video/ui', $trimmed)) {
            if ((int)$data['num_posts'] === 1) {
                return '1 video';
            }
            return preg_replace('/(\d+)/', $data['num_posts'], $trimmed);
        }

        // Table: product name "Sahemul "
        if (preg_match('/^(Sahemul|KCN|Kem|Gel|BRM)/u', $trimmed)) {
            return $data['product_name'] . ' ';
        }

        // TikTok URL in table
        if (preg_match('/^https:\/\/www\.tiktok\.com\/@/', $trimmed)) {
            return $data['tiktok_url'];
        }

        // Number of posts "1" under section 1.2
        if ($trimmed === '1') {
            return (string) ($data['num_posts'] ?? '1');
        }

        // Amount in table "550,000"
        if ($trimmed === '550,000' || preg_match('/^550[,.]000$/', $trimmed)) {
            return $data['amount'];
        }

        // Payment percentage "100%"
        if ($trimmed === '100%') {
            return '100%';
        }

        // Signature name
        if ($this->isSimilarName($trimmed, $data['full_name'])) {
            return $data['full_name'];
        }

        return null;
    }

    private function mapBBNTRedTextToValue(string $text, array $data, string &$context = ''): ?string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return null; // Don't modify whitespace-only runs
        }
        
        // Update context based on labels
        $lowerTrimmed = mb_strtolower($trimmed);
        if (str_contains($lowerTrimmed, 'cccd') || str_contains($lowerTrimmed, 'cmt')) $context = 'cccd';
        elseif (str_contains($lowerTrimmed, 'mst') || str_contains($lowerTrimmed, 'thuế')) $context = 'tax';
        elseif (str_contains($lowerTrimmed, 'điện thoại')) $context = 'phone';
        elseif (str_contains($lowerTrimmed, 'email')) $context = 'email';
        elseif (str_contains($lowerTrimmed, 'hộ khẩu') || str_contains($lowerTrimmed, 'địa chỉ')) $context = 'address';
        elseif (str_contains($lowerTrimmed, 'số tk') || str_contains($lowerTrimmed, 'tài khoản') || str_contains($lowerTrimmed, 'ngân hàng')) $context = 'bank';

        // Colon prefixes (CCCD, Tax, Phone, Email, Bank)
        if (str_starts_with($trimmed, ':')) {
            $value = trim(substr($trimmed, 1));
            
            if ($context === 'cccd') {
                return ': ' . $data['cccd'];
            } elseif ($context === 'tax') {
                return ': ' . $data['tax_code'];
            } elseif ($context === 'phone') {
                return ': ' . $data['phone'];
            } elseif ($context === 'email') {
                return ': ' . $data['email'];
            } elseif ($context === 'address') {
                return ':' . $data['address']; // BBNT has no space after colon for address
            } elseif ($context === 'bank') {
                return ': ' . $data['bank_account'] . ' - ' . $data['bank_name'];
            }
            
            return $this->replaceColonValue($text, $trimmed, $data);
        }

        // Contract Number "SAHKCN180526-09/WE"
        if (preg_match('/^SAHKCN/u', $trimmed)) {
            return $data['contract_number'];
        }

        // Date parsing (publish_date or contract_date or today)
        if (empty($data['publish_date'])) {
            if ($trimmed === '08' || $trimmed === '07' || $trimmed === '10/06/2026') {
                return '...';
            }
        } else {
            $bbntDate = \Carbon\Carbon::parse($data['publish_date']);
            
            if ($trimmed === '08') {
                return $bbntDate->format('d');
            }
            if ($trimmed === '07') {
                return $bbntDate->format('m');
            }
            if ($trimmed === '10/06/2026') {
                return $bbntDate->format('d/m/Y');
            }
        }

        // Names
        if ($this->isSimilarName($trimmed, 'Vũ Thị Thu Hà') || $this->isSimilarName($trimmed, $data['full_name'])) {
            return $data['full_name'];
        }

        // Product Name "Kem chống nắng Sahemul"
        if (preg_match('/^(Sahemul|KCN|Kem|Gel|BRM)/u', $trimmed) || str_contains($trimmed, 'Sahemul')) {
            return $data['product'] ?? $data['category'];
        }

        // Amounts "840.000"
        if ($trimmed === '840.000' || preg_match('/^840[,.]000$/', $trimmed)) {
            return $data['amount'];
        }

        // Amount in words "Tám trăm bốn mươi nghìn đồng."
        if (str_starts_with($trimmed, 'Tám trăm bốn mươi nghìn đồng')) {
            $words = $data['amount_words'] ?? '';
            return mb_convert_case(mb_substr($words, 0, 1), MB_CASE_UPPER) . mb_substr($words, 1) . '.';
        }

        // Post count "1 video s"
        if ($trimmed === '1 video s') {
            return ($data['num_posts'] ?? '1') . ' video s';
        }

        // Channel Link "https://www.tiktok.com/@hahayho14"
        if (preg_match('/^https:\/\/www\.tiktok\.com\/@/', $trimmed)) {
            return $data['tiktok_url'];
        }

        return null;
    }

    /**
     * Replace colon-prefixed values based on context tracking.
     */
    private function replaceColonValue(string $originalText, string $trimmed, array $data): string
    {
        // Extract the value part after ":"
        $value = trim(ltrim($trimmed, ':'));
        $prefix = mb_substr($originalText, 0, mb_strpos($originalText, ':') + 1);

        // Match by the existing value pattern
        // CCCD number
        if (preg_match('/^\d{12}$/', $value) && $value === '001301019638') {
            // This is the template CCCD - could be CCCD or MST
            // We need context. For now, use a static tracking approach.
        }

        // Try to identify by position/value in template
        // ": 001301019638" for CCCD
        if ($value === '001301019638') {
            // Could be CCCD or MST - we'll handle both
            static $cccdCount = 0;
            $cccdCount++;
            if ($cccdCount <= 1) {
                return $prefix . ' ' . $data['cccd'];
            }
            return $prefix . ' ' . $data['tax_code'];
        }

        // Date: ": 8/4/2026"
        if (preg_match('/\d+\/\d+\/\d+/', $value)) {
            return $prefix . ' ' . $data['cccd_date'];
        }

        // Place: ": Cục Cảnh sát..."
        if (mb_strpos($value, 'Cục') !== false || mb_strpos($value, 'cảnh sát') !== false) {
            return $prefix . ' ' . $data['cccd_place'];
        }

        // Address: ": Thôn Yên Kiện..."
        if (mb_strpos($value, 'Thôn') !== false || mb_strpos($value, 'phường') !== false || mb_strlen($value) > 20) {
            // Check if this looks like an address
            if (preg_match('/(?:Thôn|Xã|Phường|Quận|Huyện|Tỉnh|TP|Hà Nội|Đông|Tây)/ui', $value)) {
                return $prefix . ' ' . $data['address'];
            }
        }

        // Email: ": dbangbang95@gmail.com"
        if (preg_match('/@/', $value)) {
            return $prefix . ' ' . $data['email'];
        }

        // Bank account: ": 1018121321" (numeric, not CCCD length)
        if (preg_match('/^\d+$/', $value) && strlen($value) < 12 && strlen($value) >= 6) {
            return $prefix . ' ' . $data['bank_account'];
        }

        // Account holder: ": DO THI BANG BANG" (uppercase name)
        if (preg_match('/^[A-Z\s]+$/', $value)) {
            return $prefix . ' ' . $data['account_holder'];
        }

        // Bank name: ": Vietcombank"
        if (preg_match('/(?:Vietcombank|Vietinbank|VPBank|Techcombank|MB|ACB|BIDV|Agribank|TPBank)/i', $value)) {
            return $prefix . ' ' . $data['bank_name'];
        }

        // Phone
        if (preg_match('/^[\d\s]{9,}$/', $value)) {
            return $prefix . ' ' . $data['phone'];
        }

        // Amount text in article 2.1: ": 550,000 VNĐ (Bằng chữ: ...)"
        if (preg_match('/[\d,]+\s*VNĐ.*Bằng\s*chữ/u', $value)) {
            return $prefix . ' ' . $data['amount'] . ' VNĐ (Bằng chữ: ' . $data['amount_words'] . ')';
        }

        return $originalText;
    }

    /**
     * Check if two names are similar (for signature matching).
     */
    private function isSimilarName(string $a, string $b): bool
    {
        if (empty($a) || empty($b)) {
            return false;
        }
        // Template has "Đỗ Thị Băng Băng" - check if it's a proper name (capitalized words)
        return preg_match('/^[A-ZĐ][a-zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]+(\s+[A-ZĐ][a-zàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]+)+$/u', $a);
    }

    /**
     * Parse amount string to integer.
     * "550" -> 550000 (assuming thousands), "5,000,000" -> 5000000, "550,000" -> 550000
     */
    private function parseAmount(string $amountRaw): int
    {
        $clean = str_replace([',', '.', ' '], '', trim($amountRaw));

        if (!is_numeric($clean)) {
            return 0;
        }

        $num = (int) $clean;

        // If the raw value looks like it's already in full (has commas as thousand separators)
        if (strpos($amountRaw, ',') !== false) {
            return $num;
        }

        // Small numbers without commas are likely in thousands (150 = 150,000đ)
        if ($num < 10000) {
            return $num * 1000;
        }

        return $num;
    }

    /**
     * Convert a number to Vietnamese words.
     */
    public function numberToVietnameseWords(int $number): string
    {
        if ($number == 0) {
            return 'Không đồng';
        }

        $ones = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
        $units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'];

        $groups = [];
        while ($number > 0) {
            $groups[] = $number % 1000;
            $number = intdiv($number, 1000);
        }

        $parts = [];
        for ($i = count($groups) - 1; $i >= 0; $i--) {
            $group = $groups[$i];
            if ($group == 0 && $i > 0) {
                continue;
            }

            $groupStr = $this->threeDigitsToWords($group, $i < count($groups) - 1);
            if (!empty(trim($groupStr))) {
                $parts[] = trim($groupStr) . ($units[$i] ? ' ' . $units[$i] : '');
            }
        }

        $result = implode(' ', $parts);
        $result = mb_strtoupper(mb_substr($result, 0, 1)) . mb_substr($result, 1);

        return $result . ' đồng';
    }

    /**
     * Convert a 3-digit group to Vietnamese words.
     */
    private function threeDigitsToWords(int $number, bool $hasHigherGroup = false): string
    {
        $ones = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];

        $hundred = intdiv($number, 100);
        $ten = intdiv($number % 100, 10);
        $unit = $number % 10;

        $result = '';

        if ($hundred > 0) {
            $result .= $ones[$hundred] . ' trăm';
        } elseif ($hasHigherGroup && ($ten > 0 || $unit > 0)) {
            $result .= 'không trăm';
        }

        if ($ten > 1) {
            $result .= ' ' . $ones[$ten] . ' mươi';
        } elseif ($ten == 1) {
            $result .= ' mười';
        } elseif ($ten == 0 && $hundred > 0 && $unit > 0) {
            $result .= ' lẻ';
        }

        if ($unit > 0) {
            if ($unit == 1 && $ten > 1) {
                $result .= ' mốt';
            } elseif ($unit == 5 && $ten > 0) {
                $result .= ' lăm';
            } elseif ($unit == 4 && $ten > 1) {
                $result .= ' tư';
            } else {
                $result .= ' ' . $ones[$unit];
            }
        }

        return $result;
    }

    /**
     * Build product description for the contract table.
     */
    private function buildProductDescription(Contract $contract): string
    {
        return $contract->product ?? 'Sahemul';
    }
}
