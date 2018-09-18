<?php

namespace App\Http\Controllers\Base;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RankingController extends Controller
{
    public function index()
    {
        return view('base.ranking.index');
    }
}
