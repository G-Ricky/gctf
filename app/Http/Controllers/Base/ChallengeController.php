<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Library\Setting\Facades\Setting;
use App\Models\Base\Bank;
use App\Models\Base\Challenge;
use App\Models\Base\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    protected $challenges;
    protected $bank;
    protected $startTime;
    protected $endTime;

    public function __construct(Challenge $challenges)
    {
        $this->challenges = $challenges;
    }

    public function index($bank = null)
    {
        $this->authorize('listChallenges');

        $bank = $bank ??
            request()->session()->get('bank') ??
            Setting::get('bank.default', 1);

        request()->session()->put('bank', $bank);

        return view('base.challenge.index', [
            'bank' => $bank
        ]);
    }

    public function detail($id)
    {
        $this->authorize('listChallenges');

        $data = Challenge::where('id', '=', $id)
            ->firstOrFail()
            ->toArray();

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $data
        ];
    }

    public function list($bank)
    {
        $this->authorize('listChallenges');

        $b = Bank::findOrFail($bank);

        $paginate = Challenge
            ::select([
                'id', 'title', 'description', 'category', 'points', 'created_at', 'updated_at'
            ])
            ->where('bank', '=', $bank)
            ->whereNotNull('flag')
            ->orderBy('updated_at', 'desc')
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
        $this->authorize('submitFlag');

        $data = $this->validate($request, [
            'challengeId' => 'required|integer',
            'flag'        => 'required'
        ]);

        $challenge = \App\Models\Admin\Challenge
            ::where('id', '=', $data['challengeId'])
            ->firstOrFail()
            ->toArray();

        $submission = Submission
            ::where('challenge', '=', $data['challengeId'])
            ->where('submitter', '=', Auth::id())
            ->where(function($query) use($data) {
                /**
                 * @var \Illuminate\Database\Query\Builder $query
                 */
                $query
                    ->orWhere('content', '=', $data['flag'])
                    ->orWhere('is_correct', '=', true);
            })
            ->first();

        if(!is_null($submission)) {
            if($submission->is_correct) {
                return $this->fail('该挑战已解决，请勿重复提交', 200);
            } else {
                return $this->fail('该Flag已提交过，请勿重复提交', 200);
            }
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
