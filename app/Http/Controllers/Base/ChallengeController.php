<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\Base\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    protected $challenges;

    public function __construct(Challenge $challenges)
    {
        $this->challenges = $challenges;
    }

    public function list(Request $request)
    {
        $user_id = Auth::user()->id;
        $page_size = $request->query('id', 20);
        $page_size = $page_size > 20 ? 20 : $page_size;
        $page_data = $this->challenges->list($user_id, $page_size);

        $data = &$page_data['data'];
        foreach($data as &$challenge) {
            $tags = [];
            foreach($challenge['tags'] as &$tag) {
                $tags[] = $tag['name'];
            }
            $challenge['tags'] = $tags;
            $challenge['description'] = str_limit($challenge['description'], 40);
        }

        $page_data['status'] = 200;
        $page_data['success'] = true;

        return $page_data;
    }

    public function info(Request $request)
    {
        $id = $request->query('id', '0');
        $info = $this->challenges->info($id);

        return [
            'status'  => 200,
            'success' => (bool)$info,
            'data'    => $info
        ];
    }
}
