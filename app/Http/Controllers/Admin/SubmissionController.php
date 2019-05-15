<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Base\Submission;
use Illuminate\Support\Facades\DB;

class SubmissionController extends Controller
{
    private function submissionsFilter($submissions)
    {
        $result = [];
        foreach($submissions as &$submission) {
            $challenge = $submission['challenge'] ?? [];
            $submitter = $submission['submitter'] ?? [];
            $result[] = [
                'id'         => $submission['id'],
                'challenge'  => $challenge['title'] ?? '',
                'submitter'  => $submitter['nickname'] ?? $submitter['username'] ?? '',
                'content'    => $submission['content'],
                'isCorrect'  => $submission['is_correct'],
                'updateTime' => $submission['updated_at'],
                'createTime' => $submission['created_at'],
            ];
        }

        return $result;
    }

    public function index(Request $request)
    {
        $this->authorize('viewFlag');
        $this->authorize('listSubmissions');

        return view('admin.submission.index', [
            'apiUrl' => url('api/' . $request->path())
        ]);
    }

    public function listAll()
    {
        $this->authorize('viewFlag');
        $this->authorize('listSubmissions');

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
        $this->authorize('viewFlag');
        $this->authorize('listSubmissions');

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

    public function changeCorrectness(Request $request) {
        $this->authorize('correctSubmission');

        $data = $this->validate($request, [
            'id'         => 'required|integer|min:1',
            'is_correct' => 'required|boolean',
        ]);

        // TODO 确保最多存在一个正确的提交

        $submission = Submission::findOrFail($data['id']);

        if(
            $data['is_correct'] == 1 &&
            !$submission->is_correct &&
            Submission::where('challenge', '=', $submission->challenge)
                ->where('submitter', '=', $submission->submitter)
                ->where('is_correct', '=', true)
                ->count() > 0
        ) {
            return $this->fail('同一个挑战正确提交的个数最多为一个');
        }

        $affectedRows = $submission->update($data);

        return [
            'status'  => 200,
            'success' => (bool)$affectedRows
        ];
    }

    public function delete(Request $request)
    {
        $this->authorize('deleteSubmission');

        $data = $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $success = Submission
            ::findOrFail($data['id'])
            ->delete();

        return [
            'status'  => 200,
            'success' => !!$success
        ];
    }
}
