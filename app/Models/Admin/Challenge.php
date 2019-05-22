<?php

namespace App\Models\Admin;

use App\Models\Base\Tag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'poster',
        'points',
        'basic_points',
        'flag',
        'bank',
    ];

    protected $hidden = [
        'delete_at'
    ];

    public function getCreatedAtAttribute($createdAt)
    {
        return Carbon::parse($createdAt)->diffForHumans();
    }

    public function getUpdatedAtAttribute($updatedAt)
    {
        return Carbon::parse($updatedAt)->diffForHumans();
    }
}
