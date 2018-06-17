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
     * @param string $title
     * @param \DateTime $published
     * @param string $content
     */
    public function __construct(string $title, \DateTime $published, string $content)
    {
        $this->title = $title;
        $this->published = $published;
        $this->content = $content;
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
}
