<?php

declare(strict_types=1);

namespace FeedReader\Parser;

use FeedReader\Model\Article;
use FeedReader\Model\Feed;

interface FeedParser
{
    /**
     * @param Feed $feed
     * @param string $httpResponseBody
     * @return Article[]
     */
    function parseFeedToArticles(Feed $feed, string $httpResponseBody): array;
}
