<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RankingController extends Controller
{
    public function index()
    {
        return view('base.ranking.index');
    }

    public function list(Request $request)
    {
        $data = $this->validate($request, [
            'bank' => 'nullable|integer|exists:banks,id'
        ]);

        $rankingsJson = Redis::get('rankings');
        $userDictJson = Redis::get('users');

        if(!isset($rankingsJson)) {
            return $this->fail('加载数据失败');
        }

        if(!isset($data['bank'])) {
            $data['bank'] = 1;
        }

        $bankDict = json_decode($rankingsJson, true);
        $bank = $bankDict[$data['bank']];
        $rankings = $bank['rankings'];
        $rankingIds = array_column($rankings, 'id');

        $userDict = json_decode($userDictJson, true);

        foreach($userDict as $user) {
            $userId = $user['id'];
            if(!in_array($userId, $rankingIds)) {
                $user['points'] = 0;
                $user['solutions_count'] = 0;
                $user['solutions'] = [];
                $rankings[] = $user;
            }
        }

        $newRankings = [];

        foreach($rankings as $i => $ranking) {
            if($ranking['is_hidden']) {
                continue;
            }
            $ranking = array_only($ranking, [
                'id', 'sid', 'username', 'nickname', 'name', 'solutions', 'solutions_count', 'points'
            ]);
            foreach($ranking['solutions'] as $j => $solution) {
                $solution['solved_at'] = Carbon::parse($solution['solved_date'])->diffForHumans();
                $ranking['solutions'][$j] = array_only($solution, [
                    'id', 'title', 'description', 'points', 'basic_points', 'solved_at', 'solved_time', 'solved_date'
                ]);
            }

            $newRankings[] = $ranking;
        }

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $newRankings
        ];
    }
}
