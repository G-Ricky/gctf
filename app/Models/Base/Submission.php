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
		return $this->belongsTo('App\Models\Base\User', 'submitter');
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
}
