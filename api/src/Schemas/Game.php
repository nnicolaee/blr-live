<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class Game
{
    public function __construct(
        public int $match,
        public string $status,
        public string $time
    ) {
    }
}
