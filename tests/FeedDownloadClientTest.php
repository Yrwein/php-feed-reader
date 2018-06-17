<?php

declare(strict_types=1);

namespace Tests;

use FeedReader\FeedDownloadClient;
use FeedReader\Model\Article;
use FeedReader\Model\Feed;
use FeedReader\Parser\AtomParser;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class FeedDownloadClientTest extends TestCase
{
    public function testDownloadArticles_ShouldReturnArrayOfArticleObjects()
    {
        $feedParser = new AtomParser();
        $mockHandler = new MockHandler([
            new Response(200, [], file_get_contents(ROOT_DIR . '/tests/_fixtures/php.net.atom.xml')),
        ]);
        $client = new FeedDownloadClient($feedParser, $mockHandler);

        $promise = $client->downloadArticles(new Feed("test feed", "http://phpnet.feed.tom"));
        /** @var Article[] $articles */
        $articles = $promise->wait();

        $this->assertInternalType('array', $articles);
        $this->assertCount(3, $articles);
        $this->assertSame('php[world] 2018 - Call for Speakers', $articles[0]->getTitle());
        $this->assertSame('LaravelConf Taiwan 2018', $articles[1]->getTitle());
        $this->assertSame('PHP 7.3.0 alpha 1 Released', $articles[2]->getTitle());
    }

    public function testDownloadArticles_ShouldResolveToException_OnHttpError()
    {
        $feedParser = new AtomParser();
        $mockHandler = new MockHandler([
            new Response(404, [], 'Not found'),
        ]);
        $client = new FeedDownloadClient($feedParser, $mockHandler);

        $promise = $client->downloadArticles(new Feed("test feed", "http://phpnet.feed.tom"));

        $this->expectException(ClientException::class);
        $this->expectExceptionMessageRegExp('/404 Not Found/');
        $promise->wait();
    }
}
