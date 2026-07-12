<?php

use App\Containers\ClientSection\Contract\UI\WEB\Controllers\ContractController;
use Illuminate\Support\Facades\Route;

Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
