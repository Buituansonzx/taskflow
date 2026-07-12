<?php

namespace App\Containers\ClientSection\Contract\UI\WEB\Controllers;

use App\Containers\ClientSection\Contract\Models\Contract;
use App\Containers\ClientSection\Contract\Services\ContractService;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContractController extends WebController
{
    public function index(Request $request)
    {
        $query = Contract::query()->orderBy('excel_row');

        // Simple search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ILIKE', "%{$search}%")
                  ->orWhere('tiktok_username', 'ILIKE', "%{$search}%")
                  ->orWhere('product', 'ILIKE', "%{$search}%")
                  ->orWhere('koc_name', 'ILIKE', "%{$search}%");
            });
        }

        $contracts = $query->get();

        return view('clientSection@contract::index', compact('contracts', 'search'));
    }

    public function generate(int $id, ContractService $service): BinaryFileResponse
    {
        $contract = Contract::findOrFail($id);

        $filePath = $service->generateContract($contract);

        $filename = 'HopDong_' . ($contract->tiktok_username ?? $contract->excel_row) . '.docx';

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }
    public function generateBbnt(int $id, ContractService $service): BinaryFileResponse
    {
        $contract = Contract::findOrFail($id);

        $filePath = $service->generateAcceptanceRecord($contract);

        $filename = 'BBNT_' . ($contract->tiktok_username ?? $contract->excel_row) . '.docx';

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }
}
