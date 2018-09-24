<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RankingController extends Controller
{
    public function index()
    {
        return view('base.ranking.index');
    }

    public function list()
    {
        $jsonText = Redis::get('rankings');
        if(!isset($jsonText)) {
            return $this->fail('加载数据失败');
        }

        $rankings = json_decode($jsonText, true);

        foreach($rankings as $i => $ranking) {
            $rankings[$i] = array_only($ranking, [
                'id', 'sid', 'username', 'nickname', 'name', 'solutions', 'solutions_count', 'solved_at', 'points'
            ]);
            foreach($rankings[$i]['solutions'] as $j => $solution) {
                $rankings[$i]['solutions'][$j] = array_only($solution, [
                    'id', 'title', 'description', 'solved_at'
                ]);
            }
        }

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $rankings
        ];
    }
}
