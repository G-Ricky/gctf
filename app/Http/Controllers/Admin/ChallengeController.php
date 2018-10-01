<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Challenge;
use App\Models\Base\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ChallengeController extends Controller
{
    public function __construct()
    {

    }

    public function add(Request $request, Challenge $challenge)
    {
        $this->authorize('addChallenge');

        $data = $this->validate($request, [
            'title'        => 'required|string|max:32',
            'description'  => 'nullable|string|max:1024',
            'basic_points' => 'required|integer|max:10000',
            'category'     => 'required|string|max:256|in:CRYPTO,MISC,PWN,REVERSE,WEB',
            'tags'         => 'nullable|string|max:256',
            'flag'         => 'required|string|max:256',
            'bank'         => 'required|integer|exists:banks,id'
        ]);

        $data['poster'] = Auth::id();
        $data['points'] = $data['basic_points'];
        if(isset($data['tags'])) {
            $data['tags'] = str_replace(' ', '', $data['tags']);
            $data['tags'] = explode(',', $data['tags']);
        }else{
            $data['tags'] = [];
        }
        // Multiple saving
        $success = (bool)$challenge->createWithTags($data);

        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function edit(Request $request, Challenge $challenge)
    {
        $this->authorize('editChallenge');

        $data = $this->validate($request, [
            'id'           => 'required|integer',
            'title'        => 'required|string|max:32',
            'description'  => 'required|string|max:1024',
            'basic_points' => 'required|integer',
            'category'     => 'required|string|max:256|in:CRYPTO,MISC,PWN,REVERSE,WEB',
            'tags'         => 'nullable|string|max:256',
            'flag'         => 'required|string|max:256',
            'bank'         => 'required|integer|exists:banks,id'
        ]);

        if(array_key_exists('poster', $data)) {
            unset($data['poster']);
        }

        if(isset($data['tags'])) {
            $data['tags'] = str_replace(' ', '', $data['tags']);
            $data['tags'] = explode(',', $data['tags']);
        }else{
            $data['tags'] = [];
        }

        $success = false;
        DB::transaction(function() use($data, $challenge, &$success) {
            Tag::where('challenge', '=', $data['id'])
                ->delete();

            $success = (bool)$challenge->updateWithTags($data);
        });

        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function delete(Request $request)
    {
        $this->authorize('deleteChallenge');

        $data = $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $success = Challenge
            ::where('id', '=', $data['id'])
            ->firstOrFail()
            ->delete();

        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function info(Request $request)
    {
        $this->authorize('viewFlag');

        $data = $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $challenges = Challenge
            ::where('id', '=', $data['id'])
            ->where('is_hidden', '=', false)
            ->with('tags:challenge,name')
            ->firstOrFail()
            ->toArray();

        if(array_key_exists('tags', $challenges)) {
            foreach($challenges['tags'] as $i => $tag) {
                $challenges['tags'][$i] = $tag['name'];
            }
        }

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $challenges
        ];
    }
}
