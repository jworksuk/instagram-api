<?php

namespace JWorksUK\InstagramApi\Models;

class VideoResource
{
    public function __construct(
        public string $src,
        public int $config_width,
        public int $config_height,
        public string $mime_type,
        public string $profile
    ) {
    }
}
