<?php

namespace JWorksUK\InstagramApi\Responses;

use Illuminate\Support\Collection;

class MediaResponse
{
    public function __construct(
        protected Collection $medias,
        protected ?string $endCursor = null,
    ) {
    }

    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function getEndCursor(): ?string
    {
        return $this->endCursor;
    }
}
