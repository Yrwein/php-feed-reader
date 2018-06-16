<?php

declare(strict_types=1);

namespace FeedReader\Model;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class FeedRepository
{
    /**
     * @var string
     */
    private $jsonConfig;

    /**
     * @var Feed[]
     */
    private $feeds;

    /**
     * @param string $jsonConfig
     */
    public function __construct(string $jsonConfig)
    {
        $this->jsonConfig = $jsonConfig;
    }

    /**
     * @return Feed[]
     */
    public function getFeeds(): array
    {
        if ($this->feeds === null) {
            $serializer = new Serializer(
                [
                    new PropertyNormalizer(),
                    new ArrayDenormalizer(),
                ], [
                    new JsonEncoder(),
                ]
            );

            if (!file_exists($this->jsonConfig)) {
                throw new \RuntimeException('Feed config file does not exists: ' . $this->jsonConfig);
            }
            $jsonConfigContents = file_get_contents($this->jsonConfig);
            $this->feeds = $serializer->deserialize($jsonConfigContents, Feed::class . '[]', 'json');
        }

        return $this->feeds;
    }
}
