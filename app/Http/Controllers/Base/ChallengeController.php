<?php

namespace App\Http\Controllers\Base;

use App\Models\Base\Challenge;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChallengeController extends Controller
{
    protected $challenges;

    public function __construct(Challenge $challenges)
    {
        $this->challenges = $challenges;
    }

    public function list(Request $request)
    {
        $page_data = $this->challenges->list()->paginate(30)->jsonSerialize();

        $data = &$page_data['data'];
        foreach($data as &$challenge) {
            $challenge['description'] = str_limit($challenge['description'], 40);
        }

        return $page_data;
    }

    public function info()
    {

    }
}
