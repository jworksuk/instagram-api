<?php

namespace JWorksUK\InstagramApi\Models;

use DateTime;
use Illuminate\Contracts\Support\Arrayable;

class Story implements Arrayable
{
    protected array $videoResources = [];

    public function __construct(
        protected int      $id,
        protected DateTime $takenAtDate,
        array              $videoResources,
        protected string   $displayUrl,
        protected string   $typeName,
        protected array    $mentions = [],
        protected ?int     $locationId = null
    ) {
        foreach ($videoResources as $videoResource) {
            $videoResource = (array) $videoResource;
            $this->videoResources[] = new VideoResource(
                $videoResource['src'],
                $videoResource['config_width'],
                $videoResource['config_height'],
                $videoResource['mime_type'],
                $videoResource['profile']
            );
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTakenAtDate(): DateTime
    {
        return $this->takenAtDate;
    }

    /**
     * @return VideoResource[]
     */
    public function getVideoResources(): array
    {
        return $this->videoResources;
    }

    public function getDisplayUrl(): string
    {
        return $this->displayUrl;
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * @return string[]
     */
    public function getMentions(): array
    {
        return $this->mentions;
    }

    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'taken_at_date' => $this->takenAtDate,
            'video_resources' => $this->videoResources,
            'display_url' => $this->displayUrl,
            'type_name' => $this->typeName,
            'mentions' => $this->mentions,
            'location_id' => $this->locationId
        ];
    }

    public static function fromArray(array $story): self
    {
        // get taggable objects
        $tappableObjects = collect($story['tappable_objects']);

        // get location id from taggable objects
        $locationId = $tappableObjects->filter(function ($taggableObject) {
            return $taggableObject['__typename'] === 'GraphTappableLocation';
        })->map(function ($location) {
            return $location['id'];
        })->first() ?? null;

        // get mentions from taggable objects
        $mentions = $tappableObjects->filter(function ($taggableObject) {
            return $taggableObject['__typename'] === 'GraphTappableMention';
        })->map(function ($mention) {
            return $mention['username'];
        })->toArray();

        return new Story(
            $story['id'],
            DateTime::createFromFormat('U', $story['taken_at_timestamp']),
            $story['video_resources'] ?? [],
            $story['display_url'],
            $story['__typename'],
            $mentions,
            $locationId,
        );
    }
}
