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
