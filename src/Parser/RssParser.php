<?php

declare(strict_types=1);

namespace FeedReader\Parser;

use FeedReader\Model\Article;
use FeedReader\Model\Feed;

class RssParser implements FeedParser
{
    /**
     * @param Feed $feed
     * @param string $httpResponseBody
     * @return Article[]
     */
    public function parseFeedToArticles(Feed $feed, string $httpResponseBody): array
    {
        $simpleXml = new \SimpleXMLElement($httpResponseBody);
        $xmlArticles = ($simpleXml->channel->item->count() > 1) ? $simpleXml->channel->item : [$simpleXml->item];
        $utc = new \DateTimeZone('UTC');

        $articles = [];
        foreach ($xmlArticles as $xmlArticle) {
            $published = \DateTime::createFromFormat('l, d M Y H:i:s T', (string) $xmlArticle->pubDate, $utc);
            $articles[] = new Article(
                (string) $xmlArticle->title,
                $published,
                html_entity_decode((string) $xmlArticle->description),
                $feed
            );
        }
        return $articles;
    }
}
