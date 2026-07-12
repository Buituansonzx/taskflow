<?php

use App\Containers\ClientSection\Contract\UI\WEB\Controllers\ContractController;
use Illuminate\Support\Facades\Route;

Route::post('contracts/import', [ContractController::class, 'import'])->name('contracts.import');
