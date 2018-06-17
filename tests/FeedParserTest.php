<?php

declare(strict_types=1);

namespace Tests;

use FeedReader\FeedParser;
use PHPUnit\Framework\TestCase;

class FeedParserTest extends TestCase
{
    public function testParseFeedToArticles_WithAtomFeed()
    {
        $feedParser = new FeedParser();
        $articles = $feedParser->parseFeedToArticles(file_get_contents(ROOT_DIR . '/tests/_fixtures/php.net.atom.xml'));
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
}
