<?php

namespace App\Library\Setting\Facades;

use Illuminate\Support\Facades\Facade;

class Loader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Library\Setting\DatabaseLoader::class;
    }
}