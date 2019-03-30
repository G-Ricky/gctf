<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Library\Setting\Facades\Setting;
use App\Models\Base\Bank;
use App\Models\Base\Challenge;
use App\Models\Base\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ChallengeController extends Controller
{
    protected $challenges;
    protected $bank;
    protected $startTime;
    protected $endTime;

    public function __construct(Challenge $challenges)
    {
        $this->challenges = $challenges;
        $this->bank = Setting::get('bank.current') ?? false;
        $this->startTime = Setting::get('bank.start_time') ?? '1970-01-01 00:00:00';
        $this->endTime = Setting::get('bank.end_time') ?? '1970-01-01 00:00:00';
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
        $this->authorize('listChallenges');

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

    public function list()
    {
        $this->authorize('listChallenges');


        if(
            !$this->bank ||
            !Bank::where('id', $this->bank)->exists()
        ) {
            return $this->fail('请求的页面不存在', 403);
        }

        if(Carbon::parse($this->startTime)->gt(Carbon::now())) {
            return $this->fail('比赛暂未开放', 403);
        }

        if(Carbon::parse($this->endTime)->lt(Carbon::now())) {
            return $this->fail('比赛已结束', 403);
        }

        $paginate = Challenge
            ::select([
                'id', 'title', 'description', 'category', 'points', 'created_at', 'updated_at'
            ])
            ->where('is_hidden', '=', false)
            ->where('bank', '=', $this->bank)
            ->whereNotNull('flag')
            ->orderBy('updated_at', 'desc')
            ->with('tags:challenge,name')
            ->paginate(20, ['*'], 'p')
            ->jsonSerialize();

        $challenges = $paginate['data'];
        unset($paginate['data']);

        $challengeIds = array_column($challenges, 'id');

        $submissionsMap = Submission
            ::where('is_correct', '=', true)
            ->where('submitter', '=', Auth::id())
            ->whereIn('challenge', $challengeIds)
            ->get()
            ->mapWithKeys(function($item, $key) {
                return [$item['challenge'] => $item];
            })
            ->toArray();

        foreach($challenges as &$challenge) {
            foreach($challenge['tags'] as $i => $tag) {
                $challenge['tags'][$i] = $tag['name'];
            }
            $challenge['is_solved'] = (bool)($submissionsMap[$challenge['id']]['is_correct'] ?? 0);
            $challenge['description'] = str_limit($challenge['description'], 40);
            $challenge['created_time'] = strtotime($challenge['created_at']);
            $challenge['updated_time'] = strtotime($challenge['updated_at']);
        }

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $challenges,
            'paginate' => $paginate
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
            ::where('challenge', '=', $data['challengeId'])
            ->where('submitter', '=', Auth::id())
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
