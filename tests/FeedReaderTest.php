<?php

declare(strict_types=1);

namespace Tests;

use FeedReader\FeedDownloadClient;
use FeedReader\FeedReader;
use FeedReader\Model\Article;
use FeedReader\Model\Feed;
use function GuzzleHttp\Promise\promise_for;
use function GuzzleHttp\Promise\rejection_for;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class FeedReaderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testDownloadAll_ShouldReturnSortedArticles_OnTwoSuccessResults()
    {
        $feeds = [
            $feed1 = new Feed('', ''),
            $feed2 = new Feed('', ''),
        ];

        $feedDownloadClient = m::mock(FeedDownloadClient::class);
        $feedDownloadClient
            ->shouldReceive('downloadArticles')->with($feed1)->once()->andReturn(promise_for([
                article('2018-09-01', $feed1),
                article('2018-03-01', $feed1),
            ]))
            ->shouldReceive('downloadArticles')->with($feed2)->once()->andReturn(promise_for([
                article('2018-06-01', $feed2),
            ]))
        ;

        $feedReader = new FeedReader($feedDownloadClient);
        /** @var Article[] $articles */
        $articles = $feedReader->downloadAll($feeds)->wait();
        $this->assertCount(3, $articles);
        $this->assertSame('2018-09-01', $articles[0]->getPublished()->format('Y-m-d'));
        $this->assertSame('2018-06-01', $articles[1]->getPublished()->format('Y-m-d'));
        $this->assertSame('2018-03-01', $articles[2]->getPublished()->format('Y-m-d'));
    }

    public function testDownloadAll_ShouldReturnSortedArticles_OnOneSuccessAndOneFailure()
    {
        $feeds = [
            $feed1 = new Feed('', ''),
            $feed2 = new Feed('', ''),
        ];

        $feedDownloadClient = m::mock(FeedDownloadClient::class);
        $feedDownloadClient
            ->shouldReceive('downloadArticles')->with($feed1)->once()->andReturn(promise_for([
                article('2018-09-01', $feed1),
                article('2018-03-01', $feed1),
            ]))
            ->shouldReceive('downloadArticles')->with($feed2)->once()->andReturn(rejection_for(
                new \Exception('Foobar')
            ))
        ;

        $feedReader = new FeedReader($feedDownloadClient);
        /** @var Article[] $articles */
        $articles = $feedReader->downloadAll($feeds)->wait();
        $this->assertCount(2, $articles);
        $this->assertSame('2018-09-01', $articles[0]->getPublished()->format('Y-m-d'));
        $this->assertSame('2018-03-01', $articles[1]->getPublished()->format('Y-m-d'));
    }

    public function testDownloadAll_ShouldReturnEmptyArray_OnTwoFailures()
    {
        $feeds = [
            $feed1 = new Feed('', ''),
            $feed2 = new Feed('', ''),
        ];

        $feedDownloadClient = m::mock(FeedDownloadClient::class);
        $feedDownloadClient
            ->shouldReceive('downloadArticles')->with($feed1)->once()->andReturn(rejection_for(
                new \Exception('Barbaz')
            ))
            ->shouldReceive('downloadArticles')->with($feed2)->once()->andReturn(rejection_for(
                new \Exception('Foobar')
            ))
        ;

        $feedReader = new FeedReader($feedDownloadClient);
        /** @var Article[] $articles */
        $articles = $feedReader->downloadAll($feeds)->wait();
        $this->assertCount(0, $articles);
    }

    public function testOptionalProgressEvents()
    {
        $feeds = [
            $feed1 = new Feed('feed 1', ''),
            $feed2 = new Feed('feed 2', ''),
        ];

        $feedDownloadClient = m::mock(FeedDownloadClient::class);
        $feedDownloadClient
            ->shouldReceive('downloadArticles')->with($feed1)->once()->andReturn(promise_for([
                article('2018-09-01', $feed1),
                article('2018-03-01', $feed1),
            ]))
            ->shouldReceive('downloadArticles')->with($feed2)->once()->andReturn(rejection_for(
                new \Exception('Foobar')
            ))
        ;

        $feedReader = new FeedReader($feedDownloadClient);

        $events = [];
        $feedReader->setOnStart(function (Feed $feed) use (&$events) {
            $events[] = ['start', $feed->getName()];
        });
        $feedReader->setOnDownload(function (Feed $feed) use (&$events) {
            $events[] = ['download', $feed->getName()];
        });
        $feedReader->setOnFailedDownload(function (Feed $feed) use (&$events) {
            $events[] = ['failed download', $feed->getName()];
        });

        /** @var Article[] $articles */
        $feedReader->downloadAll($feeds)->wait();

        $this->assertSame([
            ['start', 'feed 1'],
            ['start', 'feed 2'],
            ['download', 'feed 1'],
            ['failed download', 'feed 2'],
        ], $events);
    }
}

function article(string $date, Feed $feed): Article {
    return new Article('', \DateTime::createFromFormat('Y-m-d', $date), '', $feed);
}
