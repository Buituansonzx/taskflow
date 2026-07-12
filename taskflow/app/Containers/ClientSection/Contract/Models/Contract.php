<?php

namespace App\Containers\ClientSection\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'excel_row',
        'contract_date',
        'publish_date',
        'batch',
        'pic',
        'tiktok_username',
        'tiktok_url',
        'tiktok_video_url',
        'amount_raw',
        'category',
        'product',
        'num_posts',
        'contract_number',
        'personal_info_raw',
        'status_af',
        'koc_name',
        'full_name',
        'cccd',
        'cccd_date',
        'cccd_place',
        'tax_code',
        'address',
        'phone',
        'email',
        'bank_account',
        'bank_name',
        'account_holder',
        'is_generated',
    ];

    protected function casts(): array
    {
        return [
            'contract_date' => 'date',
            'publish_date' => 'date',
            'is_generated' => 'boolean',
        ];
    }
}
