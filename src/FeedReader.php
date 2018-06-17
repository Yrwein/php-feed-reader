<?php

declare(strict_types=1);

namespace FeedReader;

use FeedReader\Model\Article;
use FeedReader\Model\Feed;
use function GuzzleHttp\Promise\all;
use GuzzleHttp\Promise\PromiseInterface;

class FeedReader
{
    /**
     * @var FeedDownloadClient
     */
    private $feedDownloadClient;

    /**
     * @var callable
     */
    private $onStart = null;

    /**
     * @var callable
     */
    private $onDownload = null;

    /**
     * @var callable
     */
    private $onFailedDownload = null;

    /**
     * @param FeedDownloadClient $feedDownloadClient
     * @param Feed[] $feeds
     */
    public function __construct(FeedDownloadClient $feedDownloadClient)
    {
        $this->feedDownloadClient = $feedDownloadClient;
    }

    /**
     * Starts download
     * @param $feeds Feed[]
     * @return PromiseInterface
     */
    public function start(array $feeds): PromiseInterface
    {
        $promises = $this->startDownloadOfAllFeeds($feeds);
        return $this->notifyOnFinishWithSortedResults($promises);
    }

    /**
     * @param callable $onStart
     * @return self
     */
    public function setOnStart(callable $onStart): self
    {
        $this->onStart = $onStart;
        return $this;
    }

    /**
     * @param callable $onDownload
     * @return self
     */
    public function setOnDownload(callable $onDownload): self
    {
        $this->onDownload = $onDownload;
        return $this;
    }

    /**
     * @param callable $onFailedDownload
     * @return self
     */
    public function setOnFailedDownload(callable $onFailedDownload): self
    {
        $this->onFailedDownload = $onFailedDownload;
        return $this;
    }

    /**
     * Starts download of all feeds & hooks onDownload/onFailedDownload to returned promises.
     * @param array $feeds
     * @return PromiseInterface[]
     */
    private function startDownloadOfAllFeeds(array $feeds): array
    {
        $promises = array_map(function (Feed $feed) {
            // start download
            $articlesPromise = $this->feedDownloadClient->downloadArticles($feed);
            $this->onStart && ($this->onStart)($feed);

            // wrap optional notification events to returned promise
            return $articlesPromise->then(
                function (array $articles) use ($feed) {
                    $this->onDownload && ($this->onDownload)($feed, $articles);
                    return $articles;
                }, function ($err) use ($feed) {
                    $this->onFailedDownload && ($this->onFailedDownload)($feed, $err);
                    return [];
                }
            );
        }, $feeds);
        return $promises;
    }

    /**
     * @param PromiseInterface[] $promises
     * @return PromiseInterface
     */
    private function notifyOnFinishWithSortedResults(array $promises): PromiseInterface
    {
        return all($promises)->then(function ($results) {
            $articles = array_merge(...$results);
            usort($articles, function (Article $article1, Article $article2) {
                return $article1->getPublished()->getTimestamp() < $article2->getPublished()->getTimestamp();
            });
            return $articles;
        });
    }
}
