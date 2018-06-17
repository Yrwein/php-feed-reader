<?php

declare(strict_types=1);

namespace Tests\Parser;

use FeedReader\Model\Feed;
use FeedReader\Parser\ParseException;
use FeedReader\Parser\SmartParser;
use PHPUnit\Framework\TestCase;

class SmartParserTest extends TestCase
{
    public function testParseFeedToArticles_ShouldAutomaticallyChooseAtomParser_OnXmlWithFeedTag()
    {
        $feedParser = new SmartParser();
        $feed = new Feed('', '');

        $articles = $feedParser->parseFeedToArticles($feed, file_get_contents(ROOT_DIR . '/tests/_fixtures/php.net.atom.xml'));

        $this->assertCount(3, $articles);
        $this->assertSame('php[world] 2018 - Call for Speakers', $articles[0]->getTitle());
        $this->assertSame('LaravelConf Taiwan 2018', $articles[1]->getTitle());
        $this->assertSame('PHP 7.3.0 alpha 1 Released', $articles[2]->getTitle());
    }

    public function testParseFeedToArticles_ShouldKickException_WhenUnableToRecognizeFeed()
    {
        $feedParser = new SmartParser();
        $feed = new Feed('', '');

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unknown format: foo');

        $feedParser->parseFeedToArticles($feed, '<foo></foo>');
    }
}
