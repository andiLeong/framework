<?php

namespace App\Controller;

class MeController
{
    public function index()
    {
        return app('auth')->user();
    }
}
