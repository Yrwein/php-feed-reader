<?php

declare(strict_types=1);

namespace FeedReader\Model;

class Article
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var \DateTime
     */
    private $published;

    /**
     * @var string
     */
    private $content;

    /**
     * @var Feed
     */
    private $feed;

    /**
     * @param string $title
     * @param \DateTime $published
     * @param string $content
     * @param Feed $feed
     */
    public function __construct(string $title, \DateTime $published, string $content, Feed $feed)
    {
        $this->title = $title;
        $this->published = $published;
        $this->content = $content;
        $this->feed = $feed;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return \DateTime
     */
    public function getPublished(): \DateTime
    {
        return $this->published;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Feed
     */
    public function getFeed(): Feed
    {
        return $this->feed;
    }
}
