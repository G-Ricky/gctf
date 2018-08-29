<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\Base\Challenge;
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
        return view('base.challenge.index', [
            'bank' => $request->query('bank', 1)
        ]);
    }

    public function add(Request $request)
    {
        //TODO Only administer

        $this->validate($request, [
            'title'       => 'required|string|max:32',
            'description' => 'required|string|max:1024',
            'points'      => 'required|integer',
            'category'    => 'required|string|max:256|in:CRYPTO,MISC,PWN,REVERSE,WEB',
            'tags'        => 'required|string|max:256',
            'flag'        => 'required|string|max:256',
            //'bank'        => 'required|integer|exists:banks,id'
        ]);
        $data = $request->all();

        //TODO validate competition id here

        $data['poster'] = Auth::user()->id;
        $data['tags'] = str_replace(' ', '', $data['tags']);
        $data['tags'] = explode(',', $data['tags']);

        // Remove extra data
        $data = array_only($data, ['title', 'description', 'points', 'poster', 'category', 'tags', 'flag', 'bank']);

        // Multiple saving
        $success = $this->challenges->saveWithTags($data);

        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function edit(Request $request)
    {
        //TODO Only administer
        $this->validate($request, [
            'id' => 'required|integer|exists:challenges,id'
        ]);
    }

    public function remove(Request $request)
    {
        //TODO Only administer
        $this->validate($request, [
            'id' => 'required|integer|exists:challenges,id'
        ]);
        $id = $request->input('id');
        $success = $this->challenges->remove($id);
        return [
            'status'  => 200,
            'success' => $success,
            'id' => $id
        ];
    }

    public function list(Request $request)
    {
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

    public function info(Request $request)
    {
        $id = $request->query('id', 0);
        $result = $this->challenges->info($id);
        foreach($result as &$challenge) {
            if(array_key_exists('tags', $challenge)) {
                $tags = [];
                foreach($challenge['tags'] as &$tag) {
                    $tags[] = $tag['name'];
                }
                $challenge['tags'] = $tags;
            }
        }
        return [
            'status'  => 200,
            'success' => (bool)$result[0],
            'data'    => $result[0]
        ];
    }
}
