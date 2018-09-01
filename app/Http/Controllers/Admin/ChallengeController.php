<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Base\Challenge;
use App\Models\Base\Tag;
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
            'tags'        => 'string|max:256',
            'flag'        => 'required|string|max:256',
            'bank'        => 'required|integer|exists:banks,id'
        ]);
        $data = $request->all([
            'title', 'description', 'points', 'poster', 'category', 'tags', 'flag', 'bank'
        ]);

        //TODO validate competition id here

        $data['poster'] = Auth::user()->id;
        $data['tags'] = str_replace(' ', '', $data['tags']);
        $data['tags'] = explode(',', $data['tags']);

        // Remove extra data

        // Multiple saving
        $success = (bool)$challenges->saveWithTags($data);

        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function edit(Request $request, Challenge $challenges, Tag $tags)
    {
        //TODO Only administer
        $this->validate($request, [
            'id'          => 'required|integer|exists:challenges,id',
            'title'       => 'required|string|max:32',
            'description' => 'required|string|max:1024',
            'points'      => 'required|integer',
            'category'    => 'required|string|max:256|in:CRYPTO,MISC,PWN,REVERSE,WEB',
            'tags'        => 'string|max:256',
            'flag'        => 'required|string|max:256',
            'bank'        => 'required|integer|exists:banks,id'
        ]);

        $data = $request->all([
            'id', 'title', 'description', 'points', 'category', 'tags', 'flag', 'bank'
        ]);

        //$data['poster'] = Auth::user()->id;
        $data['tags'] = str_replace(' ', '', $data['tags']);
        $data['tags'] = explode(',', $data['tags']);

        $tags->select()->where('challenge', '=', $data['id'])->delete();

        $tagModels = [];
        foreach($data['tags'] as $tag) {
            $tagModels[] = new Tag([
                'name'      => $tag,
                'challenge' => $data['id']
            ]);
        }
        unset($data['tags']);
        $success = (bool)$challenges->where('id', '=', $data['id'])->update($data);

        $challenges->setAttribute('id', $data['id']);
        $challenges->tags()->saveMany($tagModels);

        return [
            'status'  => 200,
            'success' => $success
        ];
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
