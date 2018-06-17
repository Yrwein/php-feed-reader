<?php

declare(strict_types=1);

namespace FeedReader;
use FeedReader\Model\Article;

/**
 * Parse articles from feeds
 */
class FeedParser
{
    /**
     * @param string $xml
     * @return Article[]
     */
    public function parseFeedToArticles(string $xml): array
    {
        $articles = [];
        $simpleXml = new \SimpleXMLElement($xml);
        $xmlArticles = ($simpleXml->entry->count() > 1) ? $simpleXml->entry : [$simpleXml->entry];

        $utc = new \DateTimeZone('UTC');

        foreach ($xmlArticles as $xmlArticle) {
            $published = \DateTime::createFromFormat(\DateTime::RFC3339, (string) $xmlArticle->published, $utc);
            $articles[] = new Article(
                (string) $xmlArticle->title,
                $published,
                $xmlArticle->content->asXML()
            );
        }

        return $articles;
    }
}
