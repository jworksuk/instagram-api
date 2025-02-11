<?php

namespace JWorksUK\InstagramApi\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Support\Arrayable;

class Comment implements Arrayable
{
    public function __construct(
        protected int $id,
        protected string $text,
        protected DateTime $createdAt,
        protected int $ownerId,
        protected string $ownerUsername
    ) {
    }

    public static function create(array $comment): static
    {

        return new static(
            $comment['id'],
            $comment['text'],
            new Carbon($comment['created_at']),
            $comment['owner']['id'],
            $comment['owner']['username'],
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getOwnerUsername(): string
    {
        return $this->ownerUsername;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'created_at' => $this->createdAt,
            'owner_id' => $this->ownerId,
            'owner_username' => $this->ownerUsername,
        ];
    }
}
