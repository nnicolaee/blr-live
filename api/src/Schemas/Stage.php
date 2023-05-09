<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

readonly class Stage
{
    public function __construct(
        public int $id,
        public string $name,
        public ?BracketNode $bracket,
        public array $scoreboard,
        public array $matches
    ) {
    }
}
