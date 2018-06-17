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
     * @var Client
     */
    private $client;

    /**
     * @param callable|null $guzzleHandler Allows mocking, defaults to CurlMultiHandler
     */
    public function __construct(callable $guzzleHandler = null)
    {
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
                $articles = $this->mapResponseToArticles($xml);
                return $articles;
            }
        );
        return $articlePromise;
    }

    /**
     * @param string $xml
     * @return array
     */
    private function mapResponseToArticles(string $xml): array
    {
        $articles = [];
        $simpleXml = new \SimpleXMLElement($xml);
        $xmlArticles = ($simpleXml->entry->count() > 1) ? $simpleXml->entry : [$simpleXml->entry];

        $utc = new \DateTimeZone('UTC');

        foreach ($xmlArticles as $xmlArticle) {
            $published = \DateTime::createFromFormat(\DateTime::RFC3339, (string) $xmlArticle->published, $utc);
            $articles[] = new Article(
                (string) $xmlArticle->title,
                $published,
                $xmlArticle->content->asXML()
            );
        }

        return $articles;
    }
}
