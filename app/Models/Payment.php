<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Payment extends Model
{
    use HasFactory;

    protected $casts = [
        'meta' => 'array',
    ];

    protected $fillable = [
        'payment_id',
        'for_id',
        'for_type',
        'meta',
        'payment_mode',
        'amount' ,
        'status',
        'payment_done_by',
    ];

    public function for(): MorphTo
    {
        return $this->morphTo();
    }


}

