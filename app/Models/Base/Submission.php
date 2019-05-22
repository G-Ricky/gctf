<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
	use SoftDeletes;
	
	protected $fillable = [
		'challenge',
		'submitter',
		'content',
        'is_correct'
	];
	
	public function challenge()
    {
		return $this->belongsTo('App\Models\Base\Challenge', 'challenge');
	}
	
	public function submitter()
    {
		return $this->belongsTo('App\User', 'submitter');
	}

    public function correct()
    {
        return $this->where('is_correct', '=', true);
    }

    public static function search($type = null)
    {
        $builder = self::select()
            ->has('challenge')
            ->orderBy('created_at', 'desc')
            ->with([
                'submitter:id,nickname,username',
                'challenge:id,title'
            ]);

        if($type === 'correct') {
            $builder->where('is_correct', '=', true);
        }
        if($type === 'incorrect'){
            $builder->Where('is_correct', '=', false);
        }

        return $builder
            ->paginate(13, ['*'], 'p')
            ->jsonSerialize();
    }
}
