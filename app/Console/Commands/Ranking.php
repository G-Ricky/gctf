<?php

namespace App\Console\Commands;

use App\Models\Admin\User;
use App\Models\Base\Bank;
use App\Models\Base\Challenge;
use App\Models\Base\Submission;
use App\Plugins\Points\Points;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class Ranking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Ranking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh ranking';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $banks = Bank::all()->toArray();

        $users = User::all()->toArray();
        $userIds = array_column($users, 'id');

        $submissions = Submission
            ::where('is_correct', '=', true)
            ->whereIn('submitter', $userIds)
            ->orderBy('created_at', 'DESC') // 主要是为了在后面的操作中覆盖掉重复的提交，以最早的为准
            ->get()
            ->toArray();

        $challengeIds = array_column($submissions, 'challenge');
        $challengeIds = array_keys(array_flip($challengeIds));

        $challenges = Challenge
            ::whereIn('id', $challengeIds)
            ->get()
            ->toArray();

        $usersDict = [];
        $challengesDict = [];
        $solutionsDict = [];
        $solversDict = [];
        $banksDict = [];

        //生成字典
        foreach($banks as &$bank) {
            $bank['rankings'] = [];
            $banksDict[$bank['id']] = &$bank;
        }
        unset($bank); //解除引用

        foreach($challenges as &$challenge) {
            $challenge['points'] = 0;
            $challenge['solvers_count'] = 0;
            $challenge['solvers'] = [];
            $challengesDict[$challenge['id']] = &$challenge;
        }
        unset($challenge);

        foreach($users as &$user) {
            $usersDict[$user['id']] = &$user;
        }
        unset($user);

        foreach($submissions as &$submission) {
            $submitterId = $submission['submitter'];
            $challengeId = $submission['challenge'];

            if(!isset($usersDict[$submitterId])) {
                continue; //排除被删除的用户 （以后可能会用到）
            }

            if(!isset($challengesDict[$challengeId])) {
                continue; //排除被删除的题目
            }

            if(!isset($solutionsDict[$submitterId])) {
                $solutionsDict[$submitterId] = [];
            }

            if(!isset($solversDict[$challengeId])) {
                $solversDict[$challengeId] = [];
            }

            $solutionsDict[$submitterId][$challengeId] = &$submission;
            $solversDict[$challengeId][$submitterId] = &$submission;
        }
        unset($submission);

        //计算动态分
        foreach($challengesDict as &$challenge) {
            $challengeId = $challenge['id'];
            $solvers = [];
            foreach($solversDict[$challengeId] as $submission) {
                $submitterId = $submission['submitter'];
                $user = $usersDict[$submitterId];
                $solvers[] = [
                    'id'          => $submitterId,
                    'nickname'    => $user['nickname'],
                    'username'    => $user['username'],
                    'solved_at'   => $submission['created_at'],
                    'solved_time' => strtotime($submission['created_at']),
                ];
            }
            $challenge['solvers'] = $solvers;
            $challenge['solvers_count'] = count($challenge['solvers']);
            $challenge['points'] = Points::calculate($challenge['basic_points'], $challenge['solvers_count']);
        }
        unset($challenge);

        //计算得分
        foreach($usersDict as &$user) {
            $userId = $user['id'];
            $userSolutions = $solutionsDict[$userId] ?? [];
            foreach($userSolutions as $submission) {
                $challengeId = $submission['challenge'];
                $challenge = $challengesDict[$challengeId];
                $bankId = $challenge['bank'];

                $rankings = &$banksDict[$bankId]['rankings'];
                $solver = $rankings[$userId] ?? [
                    'id'              => $userId,
                    'sid'             => $user['sid'],
                    'nickname'        => $user['nickname'],
                    'username'        => $user['username'],
                    'name'            => $user['name'],
                    'points'          => 0,
                    'solutions_count' => 0,
                    'solutions'       => [],
                ];

                $solver['points'] += $challenge['points'];
                $solver['solutions_count']++;
                $solver['solutions'][] = [
                    'id'           => $challengeId,
                    'title'        => $challenge['title'],
                    'description'  => $challenge['description'],
                    'points'       => $challenge['points'],
                    'basic_points' => $challenge['basic_points'],
                    'solved_date'  => $submission['created_at'],
                    'solved_at'    => Carbon::parse($submission['created_at'])->diffForHumans(),
                    'solved_time'  => strtotime($submission['created_at']),
                ];

                $rankings[$userId] = $solver;
            }
        }
        unset($user, $rankings);

        foreach($banksDict as &$bank) {
            $points = array_column($bank['rankings'], 'points');
            array_multisort($points, SORT_DESC, $bank['rankings']);
        }

        Redis::set('rankings', json_encode($banksDict));
        Redis::set('challenges', json_encode($challengesDict));
        Redis::set('users', json_encode($usersDict));
    }
}
