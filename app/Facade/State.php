<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;


class State extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'State';
    }
}
