<?php

namespace JWorksUK\InstagramApi\Models;

use Illuminate\Contracts\Support\Arrayable;

class CarouselMedia implements Arrayable
{
    protected string $typeName;

    public function __construct(
        protected int $id,
        protected string $shortCode,
        protected ?string $videoUrl,
        protected string $displaySrc,
    ) {
        $this->typeName = $this->determineType($videoUrl);
    }

    protected function determineType(?string $videoUrl): string
    {
        if (!empty($videoUrl)) {
            return 'video';
        }
        return 'image';
    }

    public function getDisplaySrc(): string
    {
        return $this->displaySrc;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getShortCode(): string
    {
        return $this->shortCode;
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'short_code' => $this->shortCode,
            'type_name' => $this->typeName,
            'video_url' => $this->videoUrl,
            'display_src' => $this->displaySrc,
        ];
    }
}
