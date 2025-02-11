<?php

namespace JWorksUK\InstagramApi\Models;

use Illuminate\Contracts\Support\Arrayable;

class Location implements Arrayable
{
    public function __construct(
        protected int $id,
        protected string $name,
        protected ?float $latitude,
        protected ?float $longitude
    ) {
    }
    public static function fromArray(array $data): static
    {
        return new static(
            $data['pk'],
            $data['name'],
            $data['lat'],
            $data['lng']
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
