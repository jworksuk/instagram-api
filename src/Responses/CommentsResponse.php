<?php

namespace JWorksUK\InstagramApi\Responses;

use Illuminate\Support\Collection;

class CommentsResponse
{
    public function __construct(
        protected Collection $comments,
        protected ?string $endCursor = null,
    ) {
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getEndCursor(): ?string
    {
        return $this->endCursor;
    }
}
