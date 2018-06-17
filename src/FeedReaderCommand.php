<?php

declare(strict_types=1);

namespace FeedReader;

use FeedReader\Model\Article;
use FeedReader\Model\Feed;
use FeedReader\Model\FeedRepository;
use FeedReader\Parser\SmartParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FeedReaderCommand extends Command
{
    public const COMMAND_NAME = 'feed-reader';

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Config file for feeds',
            'config/feeds.json.dist'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getOption('config');
        $output->writeln('Config file: ' . $configFile);

        $feedRepository = new FeedRepository(ROOT_DIR . '/' . $configFile);
        $feedReader = $this->buildFeedReader();

        $this->addProgressEventListeners($output, $feedReader);
        $articlesPromise = $feedReader->downloadAll($feedRepository->getFeeds());
        /** @var Article[] $articles */
        $articles = $articlesPromise->wait();

        $output->writeln('');
        $output->writeln('The last twenty articles:');
        $articles = array_slice($articles, 0, 20);
        foreach ($articles as $article) {
            $output->writeln(
                $article->getFeed()->getName() . ': '
                . $article->getTitle() . ' - '
                . $article->getPublished()->format('Y-m-d H:i'))
            ;
        }
    }

    /**
     * @todo kick this out to some DI and implement CommandLoaderInterface
     * @return FeedReader
     */
    private function buildFeedReader(): FeedReader
    {
        $feedParser = new SmartParser();
        $feedDownloadClient = new FeedDownloadClient($feedParser);
        $feedReader = new FeedReader($feedDownloadClient);
        return $feedReader;
    }

    /**
     * @param OutputInterface $output
     * @param $feedReader
     */
    private function addProgressEventListeners(OutputInterface $output, $feedReader): void
    {
        $feedReader->setOnStart(function (Feed $feed) use ($output) {
            $output->writeln($feed->getName() . ': Download started');
        });
        $feedReader->setOnDownload(function (Feed $feed, array $articles) use ($output) {
            $output->writeln($feed->getName() . ': Download finished with ' . count($articles) . ' articles');
        });
        $feedReader->setOnFailedDownload(function (Feed $feed, \Throwable $err) use ($output) {
            $output->writeln($feed->getName() . ': Download failed with ' . $err->getMessage());
        });
    }
}
