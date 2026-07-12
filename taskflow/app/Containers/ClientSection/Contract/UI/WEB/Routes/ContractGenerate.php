<?php

use App\Containers\ClientSection\Contract\UI\WEB\Controllers\ContractController;
use Illuminate\Support\Facades\Route;

Route::get('/contracts/{id}/generate', [ContractController::class, 'generate'])->name('contracts.generate');
