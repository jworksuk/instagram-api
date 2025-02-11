<?php

namespace JWorksUK\InstagramApi\Models;

use Illuminate\Contracts\Support\Arrayable;

class Music implements Arrayable
{
    public function __construct(
        protected int $id,
        protected ?int $artistId,
        protected string $title,
        protected string $subtitle = '',
        protected ?string $coverArtworkUri = null,
        protected ?int $accountId = null,
        protected ?string $accountUsername = null,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            $data['music_asset_info']['id'] ?? $data['music_asset_info']['audio_cluster_id'] ?? null,
            $data['music_asset_info']['artist_id'] ?? null,
            $data['music_asset_info']['title'],
            $data['music_asset_info']['subtitle'] ?? '',
            $data['music_asset_info']['cover_artwork_uri'] ?? null,
            $data['music_consumption_info']['ig_artist']['pk'] ?? null,
            $data['music_asset_info']['ig_username'] ?? null,
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArtistId(): int
    {
        return $this->artistId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function getCoverArtworkUri(): ?string
    {
        return $this->coverArtworkUri;
    }

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function getAccountUsername(): ?string
    {
        return $this->accountUsername;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'artist_id' => $this->artistId,
            'cover_artwork_uri' => $this->coverArtworkUri,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'account' => [
                'id' => $this->accountId,
                'username' => $this->accountUsername,
            ],
        ];
    }
}
