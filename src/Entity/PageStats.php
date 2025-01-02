<?php

namespace App\Entity;

class PageStats {
    public ?\DateTimeInterface $date;
    public int $visitors = 0;
    public int $pageviews = 0;
    public string $url = '';

    public static function fromArray(array $data): self
    {
        $obj = new self;
        $obj->date = isset($data['date']) ? new \DateTimeImmutable($data['date'], new \DateTimeZone('UTC')) : null;
        $obj->visitors = (int) $data['visitors'];
        $obj->pageviews = (int) $data['pageviews'];
        $obj->url = $data['url'];
        return $obj;
    }
}
