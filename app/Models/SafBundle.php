<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafBundle extends Model
{
    protected $fillable = [
        'saf_ref_id',
        'saf_desc',
        'saf_status',
        'saf_created_value',
        'msisdn',
        'product_name',
        'product_id',
        'subscription_date',
        'txn_date',
        'txn_status',
        'amount',
        'payload',
        'expiry_date',
        'saf_request_id',
        'saf_tran_ref'
    ];

    protected $casts = [
        'payload' => 'array',
        'expiry_date' => 'datetime',
        'txn_date' => 'datetime',
        'subscription_date' => 'datetime',
    ];
}
