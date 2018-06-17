<?php

declare(strict_types=1);

namespace FeedReader;
use FeedReader\Model\Article;

/**
 * Parse articles from feeds (RSS / atom)
 */
class FeedParser
{
    /**
     * @param string $xml
     * @return Article[]
     */
    public function parseFeedToArticles(string $xml): array
    {
        $simpleXml = new \SimpleXMLElement($xml);
        if ($simpleXml->getName() === 'feed') {
            $articles = $this->parseAtomFeed($simpleXml);
        } else if ($simpleXml->getName() === 'rss') {
            $articles = $this->parseRssFeed($simpleXml);
        } else {
            throw new ParseException('Unknown format: ' . $simpleXml->getName());
        }
        return $articles;
    }

    /**
     * @param \SimpleXMLElement $simpleXml
     * @return Article[]
     */
    private function parseAtomFeed(\SimpleXMLElement $simpleXml): array
    {
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
                $xmlArticle->content->asXML()
            );
        }
        return $articles;
    }

    /**
     * @param \SimpleXMLElement $simpleXml
     * @return Article[]
     */
    private function parseRssFeed(\SimpleXMLElement $simpleXml): array
    {
        $xmlArticles = ($simpleXml->channel->item->count() > 1) ? $simpleXml->channel->item : [$simpleXml->item];
        $utc = new \DateTimeZone('UTC');

        $articles = [];
        foreach ($xmlArticles as $xmlArticle) {
            $published = \DateTime::createFromFormat('l, d M Y H:i:s T', (string) $xmlArticle->pubDate, $utc);
            $articles[] = new Article(
                (string) $xmlArticle->title,
                $published,
                html_entity_decode((string) $xmlArticle->description)
            );
        }
        return $articles;
    }
}
