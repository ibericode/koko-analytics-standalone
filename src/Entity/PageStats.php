<?php

namespace App\Entity;

class PageStats {
    public ?\DateTimeInterface $date;
    public int $visitors;
    public int $pageviews;
    public string $url;

    public static function fromArray(array $data): self
    {
        $obj = new self;
        $obj->date = isset($data['date']) ? new \DateTimeImmutable($data['date']) : null;
        $obj->visitors = (int) $data['visitors'];
        $obj->pageviews = (int) $data['pageviews'];
        $obj->url = $data['url'];
        return $obj;
    }
}
