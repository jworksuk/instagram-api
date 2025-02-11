<?php

namespace JWorksUK\InstagramApi\Models;

use Illuminate\Contracts\Support\Arrayable;

class Profile implements Arrayable
{
    public function __construct(
        protected int $id,
        protected string $username,
        protected string $fullName,
        protected string $biography,
        protected int $followers,
        protected int $following,
        protected string $profilePicture,
        protected ?string $externalUrl,
        protected int $mediaCount,
        protected ?bool $isPrivate = null,
        protected ?bool $isVerified = null,
        protected ?bool $isBusiness = null,
        protected ?string $profilePictureHd = null
    ) {
    }
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'full_name' => $this->fullName,
            'biography' => $this->biography,
            'followers' => $this->followers,
            'following' => $this->following,
            'profile_picture' => $this->profilePicture,
            'profile_picture_hd' => $this->profilePictureHd,
            'external_url' => $this->externalUrl,
            'media_count' => $this->mediaCount,
            'is_private' => $this->isPrivate,
            'is_verified' => $this->isVerified,
            'is_business' => $this->isBusiness,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getBiography(): string
    {
        return $this->biography;
    }

    public function getFollowers(): int
    {
        return $this->followers;
    }

    public function getFollowing(): int
    {
        return $this->following;
    }

    public function getProfilePicture(): string
    {
        return $this->profilePicture;
    }

    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    public function getMediaCount(): int
    {
        return $this->mediaCount;
    }

    public function getIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function getIsBusiness(): ?bool
    {
        return $this->isBusiness;
    }

    public function getProfilePictureHd(): ?string
    {
        return $this->profilePictureHd;
    }
}
