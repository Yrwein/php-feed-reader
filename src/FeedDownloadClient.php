<?php

declare(strict_types=1);

namespace FeedReader;

use FeedReader\Model\Article;
use FeedReader\Model\Feed;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;

/**
 * Downloads feeds asynchronously.
 */
class FeedDownloadClient
{
    /**
     * @var FeedParser
     */
    private $feedParser;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param FeedParser $feedParser
     * @param callable|null $guzzleHandler Allows mocking, defaults to CurlMultiHandler
     */
    public function __construct(FeedParser $feedParser, callable $guzzleHandler = null)
    {
        $this->feedParser = $feedParser;
        $handlerStack = HandlerStack::create($guzzleHandler ?: new CurlMultiHandler());
        $this->client = new Client([
            'handler' => $handlerStack,
        ]);
    }

    /**
     * @param Feed $feed
     * @return PromiseInterface - will resolve to an array of unsorted articles
     */
    public function downloadArticles(Feed $feed): PromiseInterface
    {
        $downloadPromise = $this->client->getAsync($feed->getUrl());
        $articlePromise = $downloadPromise->then(
            function (Response $response) {
                $xml = (string) $response->getBody();
                return $this->feedParser->parseFeedToArticles($xml);
            }
        );
        return $articlePromise;
    }
}
