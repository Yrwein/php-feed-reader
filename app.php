<?php

declare(strict_types=1);

require_once 'bootstrap.php';

use FeedReader\FeedReaderCommand;
use Symfony\Component\Console\Application;

/** @noinspection PhpUnhandledExceptionInspection */
(new Application(FeedReaderCommand::COMMAND_NAME))
    ->add(new FeedReaderCommand())
    ->getApplication()
    ->setDefaultCommand(FeedReaderCommand::COMMAND_NAME, true)
    ->run();
