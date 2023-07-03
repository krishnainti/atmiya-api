<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $casts = [
        'meta' => 'array',
    ];

    protected $fillable = [
        'payment_for' ,
        'meta',
        'payment_mode',
        'amount' ,
        'status',
        'payment_done_by',
    ];
}

