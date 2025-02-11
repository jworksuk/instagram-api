<?php

namespace JWorksUK\InstagramApi\Models;

use Illuminate\Contracts\Support\Arrayable;

class User implements Arrayable
{
    public function __construct(protected int $id, protected string $username)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
        ];
    }
}
