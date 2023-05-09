<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

readonly class BracketNode
{
    public function __construct(
        public int $id,
        public ?int $match,
        public array $children
    ) {
    }
}
