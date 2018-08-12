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

    public function index()
    {
        return view('base.challenge.index');
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
        $id = $request->query('id');
        $success = $this->challenges->remove($id);
        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function list(Request $request)
    {
        $page_size = $request->query('id', 20);
        $page_size = $page_size > 20 ? 20 : $page_size;
        $page_data = $this->challenges->list($page_size);

        $data = &$page_data['data'];
        foreach($data as &$challenge) {
            $tags = [];
            foreach($challenge['tags'] as &$tag) {
                $tags[] = $tag['name'];
            }
            $challenge['tags'] = $tags;
            $challenge['description'] = str_limit($challenge['description'], 40);
        }

        $page_data['status'] = 200;
        $page_data['success'] = true;
        unset($page_data['total']);

        return $page_data;
    }

    public function info(Request $request)
    {
        $id = $request->query('id', 0);
        $challenge = $this->challenges->info($id);
        $tags = [];
        foreach($challenge['tags'] as &$tag) {
            $tags[] = $tag['name'];
        }
        $challenge['tags'] = $tags;

        return [
            'status'  => 200,
            'success' => (bool)$challenge,
            'data'    => $challenge
        ];
    }
}
