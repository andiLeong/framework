<?php

use Andileong\Framework\Core\Application;

require('././vendor/autoload.php');

$appPath = realpath(dirname('././'));
$container = new Application($appPath);
$_SERVER['DOCUMENT_ROOT'] = $appPath;
