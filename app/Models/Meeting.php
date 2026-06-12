<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'action_items' => 'array',
        'decisions' => 'array',
        'issues' => 'array',
        'next_steps' => 'array',
    ];
}
