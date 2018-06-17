<?php

declare(strict_types=1);

namespace Tests;

use FeedReader\Model\Feed;
use FeedReader\Parser\RssParser;
use PHPUnit\Framework\TestCase;

class RssParserTest extends TestCase
{
    public function testParseFeedToArticles()
    {
        $feedParser = new RssParser();
        $feed = new Feed('', '');
        $articles = $feedParser->parseFeedToArticles($feed, file_get_contents(ROOT_DIR . '/tests/_fixtures/omgubuntu.com.rss.xml'));
        $this->assertCount(3, $articles);

        $this->assertSame('You Can Now Play ‘TrackMania Nations Forever’ on Ubuntu', $articles[0]->getTitle());
        $this->assertSame('2018-06-15 14:37:03', $articles[0]->getPublished()->format('Y-m-d H:i:s'));
        $this->assertRegExp('/\/>A popular PC racing game has sped its way on to the Ubuntu/', $articles[0]->getContent());
        $this->assertSame($feed, $articles[0]->getFeed());

        $this->assertSame('GPD Pocket 2 Launches This Summer with a Faster Processor', $articles[1]->getTitle());
        $this->assertSame('2018-06-15 06:40:59', $articles[1]->getPublished()->format('Y-m-d H:i:s'));
        $this->assertRegExp('/\/>Remember that tiny 7-inch laptop we collectively cooed over/', $articles[1]->getContent());
        $this->assertSame($feed, $articles[1]->getFeed());

        $this->assertSame('How to Enable the Blur Effect in KDE Plasma 5.13', $articles[2]->getTitle());
        $this->assertSame('2018-06-15 05:47:47', $articles[2]->getPublished()->format('Y-m-d H:i:s'));
        $this->assertRegExp('/\/>The new blur effect in KDE Plasma 5.13 is wowing a lot of/', $articles[2]->getContent());
        $this->assertSame($feed, $articles[2]->getFeed());
    }
}
