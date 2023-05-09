<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

readonly class CurrentStatus
{
    public function __construct(
        public ?string $stage,
        public ?int $match,
        public ?string $livestream
    ) {
    }
}
