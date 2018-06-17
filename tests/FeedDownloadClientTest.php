<?php

declare(strict_types=1);

namespace Tests;

use FeedReader\FeedDownloadClient;
use FeedReader\Model\Article;
use FeedReader\Model\Feed;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class FeedDownloadClientTest extends TestCase
{
    public function testDownloadArticles_ShouldReturnArrayOfArticleObjects()
    {
        $mockHandler = new MockHandler([
            new Response(200, [], file_get_contents(ROOT_DIR . '/tests/_fixtures/php.net_feed.xml')),
        ]);
        $client = new FeedDownloadClient($mockHandler);

        $promise = $client->downloadArticles(new Feed("test feed", "http://phpnet.feed.tom"));
        /** @var Article[] $articles */
        $articles = $promise->wait();

        $this->assertInternalType('array', $articles);
        $this->assertCount(3, $articles);

        $this->assertSame('php[world] 2018 - Call for Speakers', $articles[0]->getTitle());
        $this->assertSame('2018-06-13 12:00:51', $articles[0]->getPublished()->format('Y-m-d H:i:s'));
        $this->assertRegExp('/<p>This year we are wanting to provide our attendees deep-dive content which/', $articles[0]->getContent());

        $this->assertSame('LaravelConf Taiwan 2018', $articles[1]->getTitle());
        $this->assertSame('2018-06-11 11:00:00', $articles[1]->getPublished()->format('Y-m-d H:i:s'));
        $this->assertRegExp('/<p>Location: No. 11, Zhongshan South Road, Zhongzheng District, Taipei City, 100 Taiwan/', $articles[1]->getContent());

        $this->assertSame('PHP 7.3.0 alpha 1 Released', $articles[2]->getTitle());
        $this->assertSame('2018-06-07 18:36:37', $articles[2]->getPublished()->format('Y-m-d H:i:s'));
        $this->assertRegExp('/<p>The signatures for the release can be found in/', $articles[2]->getContent());
    }

    public function testDownloadArticles_ShouldResolveToException_OnHttpError()
    {
        $mockHandler = new MockHandler([
            new Response(404, [], 'Not found'),
        ]);
        $client = new FeedDownloadClient($mockHandler);

        $promise = $client->downloadArticles(new Feed("test feed", "http://phpnet.feed.tom"));

        $this->expectException(ClientException::class);
        $this->expectExceptionMessageRegExp('/404 Not Found/');
        $promise->wait();
    }
}
