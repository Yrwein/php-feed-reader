<?php

declare(strict_types=1);

namespace FeedReader\Parser;

use FeedReader\Model\Article;
use FeedReader\Model\Feed;

class AtomParser implements FeedParser
{
    /**
     * @param Feed $feed
     * @param string $httpResponseBody
     * @return Article[]
     */
    public function parseFeedToArticles(Feed $feed, string $httpResponseBody): array
    {
        $simpleXml = new \SimpleXMLElement($httpResponseBody);
        $xmlArticles = ($simpleXml->entry->count() > 1) ? $simpleXml->entry : [$simpleXml->entry];
        $utc = new \DateTimeZone('UTC');

        $articles = [];
        foreach ($xmlArticles as $xmlArticle) {
            $published = \DateTime::createFromFormat(\DateTime::RFC3339, (string) $xmlArticle->updated, $utc);
            if (!$published) {
                $published = \DateTime::createFromFormat(\DateTime::RFC3339, (string) $xmlArticle->published, $utc);
            }
            $articles[] = new Article(
                (string) $xmlArticle->title,
                $published,
                $xmlArticle->content->asXML(),
                $feed
            );
        }
        return $articles;
    }
}
