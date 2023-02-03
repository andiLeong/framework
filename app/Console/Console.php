<?php

namespace App\Console;

use Andileong\Framework\Core\Console\Console as CoreConsole;
use App\Console\Commands\Play;

class Console extends CoreConsole
{
    protected $appCommands = [
        Play::class
    ];
}
