<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;


class Country extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Country';
    }
}
