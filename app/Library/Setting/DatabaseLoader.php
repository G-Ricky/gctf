<?php

namespace App\Library\Setting;

use Illuminate\Support\Facades\DB;

class DatabaseLoader extends Loader
{
    public function load()
    {
        return DB::table('settings')->get()->toArray();
    }
}