<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'company',
        'message',
        'accept',
        'processed',
        'processed_at',
        'notes'
    ];

    protected $casts = [
        'accept' => 'boolean',
        'processed' => 'boolean',
        'processed_at' => 'datetime',
    ];
}
