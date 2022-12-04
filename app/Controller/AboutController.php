<?php

namespace App\Controller;

use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Request\Request;

class AboutController
{

    public function __construct(public Request $request)
    {
        //
    }

    public function index(Config $config)
    {
//        dump(request()->get('foo'));
//        dump(config('database'));
//        dd(app());
        return 'this is a about page';
    }
}