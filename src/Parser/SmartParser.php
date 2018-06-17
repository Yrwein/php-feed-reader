<?php

declare(strict_types=1);

namespace FeedReader\Parser;

use FeedReader\Model\Article;
use FeedReader\Model\Feed;

/**
 * Uses RSS / Atom parser by response content.
 */
class SmartParser implements FeedParser
{
    /**
     * @var FeedParser[]
     */
    private $parsers;

    public function __construct()
    {
        $this->parsers = [
            'feed' => new AtomParser(),
            'rss' => new RssParser(),
        ];
    }

    /**
     * @param Feed $feed
     * @param string $httpResponseBody
     * @return Article[]
     */
    public function parseFeedToArticles(Feed $feed, string $httpResponseBody): array
    {
        $simpleXml = new \SimpleXMLElement($httpResponseBody);
        if (isset($this->parsers[$simpleXml->getName()])) {
            return $this->parsers[$simpleXml->getName()]->parseFeedToArticles($feed, $httpResponseBody);
        } else {
            throw new ParseException('Unknown format: ' . $simpleXml->getName());
        }
    }
}
