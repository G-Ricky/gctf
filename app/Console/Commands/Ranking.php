<?php

namespace App\Console\Commands;

use App\Models\Admin\User;
use App\Models\Base\Challenge;
use App\Models\Base\Submission;
use App\Plugins\Points\Points;
use Illuminate\Console\Command;
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
        //TODO 按 Bank 区分

        $usersDict = User
            ::all()
            ->mapWithKeys(function($item, $key) {
                return [$item['id'] => $item];
            })
            ->toArray();
        $userIds = array_column($usersDict, 'id');

        $submissions = Submission
            ::where('is_correct', '=', true)
            ->whereIn('submitter', $userIds)
            ->orderBy('created_at')
            ->get()
            ->toArray();

        $challengeIds = array_column($submissions, 'challenge');
        $challengeIds = array_keys(array_flip($challengeIds));

        $challengesDict = Challenge
            ::whereIn('id', $challengeIds)
            ->get()
            ->mapWithKeys(function($item, $key) {
                return [$item['id'] => $item];
            })
            ->toArray();

        //
        $solvers = [];
        $solutions = [];
        foreach($submissions as $submission) {
            $submitterId = $submission['submitter'];
            $challengeId = $submission['challenge'];
            $solvers[$challengeId] = $solvers[$challengeId] ?? [];
            $solvers[$challengeId][$submitterId] = $usersDict[$submitterId];
            $solutions[$submitterId] = $solutions[$submitterId] ?? [];

            if(!isset($solutions[$submitterId][$challengeId])) {
                $solutions[$submitterId][$challengeId] = $challengesDict[$challengeId];
                $solutions[$submitterId][$challengeId]['solved_at'] = $submission['created_at'];
                $solutions[$submitterId][$challengeId]['solved_time'] = strtotime($submission['created_at']);
            }
        }

        //
        $solversCount = [];
        foreach($solvers as $challengeId => $solver) {
            $solversCount[$challengeId] = count($solver);
        }

        //计算每一道题的分值
        foreach($challengesDict as &$challenge) {
            $challengeId = $challenge['id'];
            $challenge['dynamic_points'] = Points::calculate($challenge['points'], $solversCount[$challengeId]);
        }

        //计算每个人的分数
        foreach($usersDict as &$user) {
            $userId = $user['id'];
            $userSolutions = $solutions[$userId] ?? [];
            $user['solutions_count'] = count($userSolutions);
            $user['solutions'] = [];
            $user['solved_time'] = 0;
            $user['points'] = 0;
            foreach($userSolutions as $challengeId => $userSolution) {
                $user['solutions'][] = $userSolution;
                $user['points'] += $challengesDict[$challengeId]['dynamic_points'];
                $user['solved_time'] = max($user['solved_time'], $userSolution['solved_time']);
            }
            $user['solved_at'] = date('Y-m-d H:i:s', $user['solved_time']);
        }

        $rankings = $usersDict;
        $scores = array_column($usersDict, 'points');
        $solvedTimes = array_column($usersDict, 'solved_time');
        array_multisort($scores, SORT_DESC, $solvedTimes, SORT_ASC, $rankings);

        Redis::set('rankings', json_encode($rankings));
        Redis::set('challenges', json_encode($challengesDict));
        Redis::set('users', json_encode($usersDict));
    }
}
