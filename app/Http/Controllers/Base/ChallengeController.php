<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\Base\Challenge;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    protected $challenges;

    public function __construct(Challenge $challenges)
    {
        $this->challenges = $challenges;
    }

    public function index(Request $request)
    {
        return view('base.challenge.index', [
            'bank' => $request->query('bank', 1)
        ]);
    }

    public function list(Request $request)
    {
        $bank = $request->query('bank', 1);
        $page = $request->query('page', 1);
        $page_size = $request->query('pageSize', 20);
        $result = $this->challenges->list($bank, $page, min($page_size, 30));

        $data = &$result['data'];
        foreach($data as &$challenge) {
            $tags = [];
            foreach($challenge['tags'] as &$tag) {
                $tags[] = $tag['name'];
            }
            $challenge['tags'] = $tags;
            $challenge['description'] = str_limit($challenge['description'], 40);
        }

        $result['status'] = 200;
        $result['success'] = true;
        unset($result['total']);

        return [
            'status' => 200,
            'success' => true,
            'data' => $result['data'],
            'page' => array_only($result, [
                'current_page', 'first_page_url', 'from', 'last_page', 'last_page_url', 'next_page_url', 'path',
                'per_page', 'prev_page_url', 'to'
            ])
        ];
    }
}
