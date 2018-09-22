<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Base\Submission;

class SubmissionController extends Controller
{
    private function submissionsFilter($submissions)
    {
        $result = [];
        foreach($submissions as &$submission) {
            $result[] = [
                'id'         => $submission['id'],
                'challenge'  => is_null($submission['challenge']) ? null : $submission['challenge']['title'],
                'submitter'  => is_null($submission['submitter']) ? null : $submission['submitter']['nickname'],
                'content'    => $submission['content'],
                'isCorrect'  => $submission['is_correct'],
                'updateTime' => $submission['updated_at'],
            ];
        }

        return $result;
    }

    public function index(Request $request)
    {
        return view('admin.submission.index', [
            'apiUrl' => url('api/' . $request->path())
        ]);
    }

    public function listAll()
    {
        $paginate = Submission::search();

        $result = $this->submissionsFilter($paginate['data']);

        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $result,
            'paginate' => $paginate
        ];
    }

    public function list($type)
    {
        if(!in_array($type, ['correct', 'incorrect'])) {
            abort(404);
        }

        $paginate = Submission::search($type);

        $result = $this->submissionsFilter($paginate['data']);

        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $result,
            'paginate' => $paginate
        ];
    }

    public function delete()
    {

    }
}
