<?php namespace Golonka\BBCode\Facades;

use Illuminate\Support\Facades\Facade;

class BBCodeParser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bbcode';
    }
}
