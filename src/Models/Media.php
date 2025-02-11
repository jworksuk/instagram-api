<?php

namespace JWorksUK\InstagramApi\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Media implements Arrayable
{
    public function __construct(
        protected int $userId,
        protected int $id,
        protected ?Location $location,
        protected string $shortCode,
        protected string $typeName,
        protected string $thumbnailSrc,
        protected string $displaySrc,
        protected ?string $videoUrl,
        protected Carbon $posted_at,
        protected string $link,
        protected ?string $caption,
        protected ?Collection $carouselMedia,
        protected ?Collection $taggedUsers,
        protected ?Collection $coauthors,
        protected ?Music $music,
    ) {
    }

    public static function create(array $post): static
    {
        $location = isset($post['location']) ? Location::fromArray($post['location']) : null;

        return new static(
            $post['user']['pk'],
            $post['pk'],
            $location,
            $post['code'],
            static::determineType($post),
            static::getThumbnailFromImages($post['image_versions2']['candidates']),
            $post['image_versions2']['candidates'][0]['url'],
            static::getVideoUrlFromArray($post['video_versions'] ?? []),
            new Carbon($post['taken_at']),
            "https://www.instagram.com/p/{$post['code']}/",
            isset($post['caption']) ? $post['caption']['text'] : null,
            static::getSideCarItemsFromArray($post['carousel_media'] ?? [], $post['code']),
            static::getUserTagsFromArray($post['usertags'] ?? []),
            static::getCoAuthorsFromArray($post['coauthor_producers'] ?? []),
            static::getMusicFromArray($post['clips_metadata']['music_info'] ?? [])
        );
    }

    protected static function getVideoUrlFromArray(?array $data): ?string
    {
        if (empty($data)) {
            return null;
        }

        return $data[0]['url'] ?? null;
    }

    protected static function determineType(array $post): string
    {
        if (!empty($post['video_versions'])) {
            return 'video';
        } elseif (!empty($post['carousel_media'])) {
            return 'carousel';
        }
        return 'image';
    }

    protected static function getSideCarItemsFromArray(mixed $carouselMedia, string $shortCode): ?Collection
    {
        return collect($carouselMedia)->map(function ($item) use ($shortCode) {
            return new CarouselMedia(
                id: $item['pk'],
                shortCode:  $shortCode,
                videoUrl: static::getVideoUrlFromArray($item['video_versions'] ?? []),
                displaySrc: $item['image_versions2']['candidates'][0]['url'],
            );
        });
    }

    protected static function getUserTagsFromArray(?array $taggedUsers): ?Collection
    {
        if (empty($taggedUsers)) {
            return null;
        }

        return collect($taggedUsers['in'])->pluck('user')->map(function ($user) {
            return new TaggedUser($user['pk'], $user['username']);
        });
    }

    protected static function getCoAuthorsFromArray(?array $coAuthors): ?Collection
    {
        if (empty($coAuthors)) {
            return null;
        }

        return collect($coAuthors)->map(function ($user) {
            return new CoAuthor($user['pk'], $user['username']);
        });
    }

    protected static function getMusicFromArray($music): ?Music
    {
        if (empty($music)) {
            return null;
        }

        return Music::fromArray($music);
    }

    private static function getThumbnailFromImages(array $images): string
    {
        return collect($images)->reject(function (array $image) {
            return $image['height'] !== $image['width'];
        })->reject(function (array $image) {
            return $image['width'] > 640;
        })->pluck('url')->first();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPostedAt(): Carbon
    {
        return $this->posted_at;
    }

    public function getThumbnailSrc(): string
    {
        return $this->thumbnailSrc;
    }

    public function getDisplaySrc(): string
    {
        return $this->displaySrc;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function getShortCode(): string
    {
        return $this->shortCode;
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function getCarouselMedia(): ?Collection
    {
        return $this->carouselMedia;
    }

    public function getTaggedUsers(): ?Collection
    {
        return $this->taggedUsers;
    }

    public function getCoAuthors(): ?Collection
    {
        return $this->coauthors;
    }

    public function getMusic(): ?Music
    {
        return $this->music;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'location' => $this->location?->toArray(),
            'short_code' => $this->shortCode,
            'type_name' => $this->typeName,
            'thumbnail_src' => $this->thumbnailSrc,
            'display_src' => $this->displaySrc,
            'video_url' => $this->videoUrl,
            'posted_at' => $this->posted_at,
            'link' => $this->link,
            'caption' => $this->caption,
            'carousel_media' => $this->carouselMedia?->toArray(),
            'tagged_users' => $this->taggedUsers?->toArray(),
            'co_authors' => $this->coauthors?->toArray(),
            'music' => $this->music?->toArray(),
        ];
    }
}
