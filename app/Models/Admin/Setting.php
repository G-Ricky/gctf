<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'name',
        'value',
        'cast',
        'description',
    ];

    protected $hidden = [
        'created_at'
    ];
}
