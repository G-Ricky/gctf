<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\Base\Challenge;
use App\Models\Base\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    protected $challenges;

    public function __construct(Challenge $challenges)
    {
        $this->challenges = $challenges;
    }

    public function index(Request $request)
    {
        $this->authorize('listChallenges');

        return view('base.challenge.index', [
            'bank' => $request->query('bank', 1)
        ]);
    }

    public function detail(Request $request, Challenge $challenges)
    {
        $challengeId = $request->query('id', 1);
        $result = $challenges->detail($challengeId)->toArray();
        if($result) {
            $success = true;
            $data = $result[0];
            $tags = [];
            foreach($data['tags'] as &$tag) {
                $tags[] = $tag['name'];
            }
            $data['tags'] = $tags;
        }else{
            $success = false;
            $data = null;
        }

        return [
            'status'  => 200,
            'success' => $success,
            'data'    => $data
        ];
    }

    public function list(Request $request)
    {
        $this->authorize('listChallenges');

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

    public function submitFlag(Request $request, Challenge $challenges, Submission $submissions)
    {
        $this->validate($request, [
            'challengeId' => 'required|integer',
            'flag'        => 'required'
        ]);
        $challengeId = $request->input('challengeId', '');
        $flag = $request->input('flag', '');

        $challengeModel = $challenges->info($challengeId)->first();

        if(is_null($challengeModel)) {
            return [
                'status' => 200,
                'success' => false,
                'message' => '题目不存在'
            ];
        }

        $challenge = $challengeModel->toArray();

        $isCorrect = $challenge['flag'] === $flag;

        $success = (bool)$submissions->add($challengeId, Auth::user()->id, $flag, $isCorrect);

        return [
            'status' => 200,
            'success' => $success,
            'correct' => $isCorrect,
            'message' => $success ? '提交成功' : '提交失败'
        ];
    }
}
