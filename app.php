<?php

declare(strict_types=1);

use FeedReader\FeedReaderCommand;
use Symfony\Component\Console\Application;

// script settings
set_time_limit(0); // run forever
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . './vendor/autoload.php';

/** @noinspection PhpUnhandledExceptionInspection */
(new Application(FeedReaderCommand::COMMAND_NAME))
    ->add(new FeedReaderCommand())
    ->getApplication()
    ->setDefaultCommand(FeedReaderCommand::COMMAND_NAME, true)
    ->run();
