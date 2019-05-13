<?php

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->diffForHumans();
    }

	public function add($challengeId, $submitterId, $content, $isCorrect)
    {
        return $this->create([
            'challenge'  => $challengeId,
            'submitter'  => $submitterId,
            'content'    => $content,
            'is_correct' => $isCorrect
        ]);
    }

    public function correct()
    {
        return $this->where('is_correct', '=', true);
    }

    public function notCorrect()
    {
        return $this->where('is_correct', '=', false);
    }

    public function isSolved($submitterId, $challengeId)
    {
        $result = $this
            ->select(
                DB::raw('COUNT(*)')
            )
            ->where('submitter', '=', $submitterId)
            ->where('challenge', '=', $challengeId)
            ->correct()
            ->get()
            ->first();
        if(is_null($result)) {
            return false;
        }else{
            return true;
        }
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
