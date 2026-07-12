<?php

namespace App\Containers\ClientSection\Contract\UI\CLI\Commands;

use App\Containers\ClientSection\Contract\Services\ContractService;
use Illuminate\Console\Command;

class ImportContractsCommand extends Command
{
    protected $signature = 'contracts:import {--fresh : Xóa toàn bộ dữ liệu cũ trước khi import}';

    protected $description = 'Import dữ liệu hợp đồng từ file Excel (tep_hop_dong.xlsx) vào database';

    public function handle(ContractService $service): int
    {
        $fresh = $this->option('fresh');

        if ($fresh) {
            $this->warn('⚠ Xóa toàn bộ dữ liệu cũ...');
        }

        $this->info('📖 Đang đọc file Excel...');

        try {
            $count = $service->importFromExcel($fresh);
            $this->info("✅ Import thành công {$count} bản ghi có trạng thái \"Đề xuất làm HĐ/NT\".");
        } catch (\Exception $e) {
            $this->error('❌ Lỗi: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
