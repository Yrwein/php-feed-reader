<?php

declare(strict_types=1);

namespace Tests\Model;

use FeedReader\Model\FeedRepository;
use PHPUnit\Framework\TestCase;

class FeedRepositoryTest extends TestCase
{
    public function testGetFeeds()
    {
        $repo = new FeedRepository(__DIR__ . '/../_fixtures/testfeeds.json');
        $feeds = $repo->getFeeds();

        $this->assertCount(2, $feeds);
        $this->assertSame('Test feed 1', $feeds[0]->getName());
        $this->assertSame('http://example.com', $feeds[0]->getUrl());
        $this->assertSame('Test feed 2', $feeds[1]->getName());
        $this->assertSame('http://google.com', $feeds[1]->getUrl());
    }

    public function testGetFeeds_ShouldKickRuntimeException_OnNotFoundFile()
    {
        $notExistingFeedFile = __DIR__ . '/../_fixtures/doesnotexists.json';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Feed config file does not exists: ' . $notExistingFeedFile);

        $repo = new FeedRepository($notExistingFeedFile);
        $repo->getFeeds();
    }
}
