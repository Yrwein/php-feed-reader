<?php

declare(strict_types=1);

namespace FeedReader\Parser;

use FeedReader\Model\Article;
use FeedReader\Model\Feed;

/**
 * Responsible for converting feed response to an array of Article objects.
 */
interface FeedParser
{
    /**
     * @param Feed $feed
     * @param string $httpResponseBody
     * @return Article[]
     */
    function parseFeedToArticles(Feed $feed, string $httpResponseBody): array;
}
