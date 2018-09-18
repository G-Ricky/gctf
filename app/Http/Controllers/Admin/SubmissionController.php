<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Base\Submission;

class SubmissionController extends Controller
{
    public function index()
    {
        $correct = array_key_exists('correct', $_GET);
        $incorrect = array_key_exists('incorrect', $_GET);

        if($correct && !$incorrect) {
            $queryString = '?correct';
        }else if(!$correct && $incorrect) {
            $queryString = '?incorrect';
        }else{
            $queryString = '';
        }

        return view('admin.submission.index', [
            'queryString' => $queryString
        ]);
    }

    public function list()
    {
        $search = [
            'correct'   => array_key_exists('correct', $_GET),
            'incorrect' => array_key_exists('incorrect', $_GET)
        ];

        $paginate = Submission::submissions($search)->jsonSerialize();

        $result = [];
        foreach($paginate['data'] as &$submission) {
            $result[] = [
                'id'         => $submission['id'],
                'challenge'  => is_null($submission['challenge']) ? null : $submission['challenge']['title'],
                'submitter'  => is_null($submission['submitter']) ? null : $submission['submitter']['nickname'],
                'content'    => $submission['content'],
                'isCorrect'  => $submission['is_correct'],
                'updateTime' => $submission['updated_at'],
            ];
        }

        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'result'   => $result,
            'paginate' => $paginate
        ];
    }

    public function delete()
    {

    }
}
