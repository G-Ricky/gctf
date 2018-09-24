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

    public function submitFlag(Request $request)
    {
        $data = $this->validate($request, [
            'challengeId' => 'required|integer',
            'flag'        => 'required'
        ]);

        $challenge = Challenge
            ::where('id', '=', $data['challengeId'])
            ->firstOrFail()
            ->toArray();

        $flag = Submission
            ::where('submitter', '=', Auth::id())
            ->where('is_correct', '=', true)
            ->first();

        if($flag) {
            return $this->fail('先前已提交过正确的 flag', 200);
        }

        $data = [
            'challenge'  => $data['challengeId'],
            'content'    => $data['flag'],
            'is_correct' => $challenge['flag'] === $data['flag'],
            'submitter'  => Auth::id(),
        ];

        $submission = Submission::create($data);

        return [
            'status' => 200,
            'success' => !!$submission,
            'correct' => $data['is_correct'],
            'message' => !!$submission ? '提交成功' : '提交失败'
        ];
    }
}
