<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->integer('excel_row')->unique();
            $table->date('contract_date')->nullable();
            $table->string('batch')->nullable();
            $table->string('pic')->nullable();
            $table->string('tiktok_username')->nullable();
            $table->string('tiktok_url', 500)->nullable();
            $table->string('amount_raw')->nullable();
            $table->string('category')->nullable();
            $table->string('product')->nullable();
            $table->integer('num_posts')->default(1);
            $table->string('contract_number')->nullable();
            $table->text('personal_info_raw')->nullable();
            $table->string('status_af')->nullable();
            $table->string('koc_name')->nullable();

            // Parsed fields from column AA
            $table->string('full_name')->nullable();
            $table->string('cccd')->nullable();
            $table->string('cccd_date')->nullable();
            $table->string('cccd_place')->nullable();
            $table->string('tax_code')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_holder')->nullable();

            $table->boolean('is_generated')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
