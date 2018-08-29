<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Base\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    public function __construct()
    {

    }

    public function add(Request $request, Challenge $challenges)
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
        $success = $challenges->saveWithTags($data);

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

    public function remove(Request $request, Challenge $challenges)
    {
        //TODO Only administer
        $this->validate($request, [
            'id' => 'required|integer|exists:challenges,id'
        ]);
        $id = $request->input('id');
        $success = $challenges->remove($id);
        return [
            'status'  => 200,
            'success' => $success,
            'id' => $id
        ];
    }

    public function info(Request $request, Challenge $challenges)
    {
        $id = $request->query('id', 0);
        $result = $challenges->info($id)->toArray();
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
