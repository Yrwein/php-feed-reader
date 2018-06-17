<?php

declare(strict_types=1);

namespace FeedReader;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FeedReaderCommand extends Command
{
    public const COMMAND_NAME = 'feed-reader';

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Test');
    }
}
