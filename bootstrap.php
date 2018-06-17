<?php

declare(strict_types=1);

// script settings
set_time_limit(0); // run forever
error_reporting(E_ALL);
ini_set('display_errors', '1');

set_error_handler(function ($errorCode, $errorString) {
    throw new Error($errorString, $errorCode);
}, E_ALL);

const ROOT_DIR = __DIR__;

require __DIR__ . './vendor/autoload.php';
