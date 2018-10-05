<?php

namespace App\Library\Setting\Facades;

use Illuminate\Support\Facades\Facade;

class Setting extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Library\Setting\Setting::class;
    }
}