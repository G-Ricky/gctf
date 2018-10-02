<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\Base\Content;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $contentsCollection = Content
            ::where('type', '=', 'home')
            ->with('modifier:id,username,nickname')
            ->orderBy('updated_at', 'DESC')
            ->get();

        $contents = $contentsCollection->toArray();

        foreach($contents as &$content) {
            $content['segments'] = explode("\n", $content['content']);
        }

        return view('base.home.index', [
            'contents' => $contents,
        ]);
    }
}
