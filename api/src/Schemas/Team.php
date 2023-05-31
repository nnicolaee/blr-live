<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class Team
{
    public function __construct(
        public string $username,
        public string $name,
        public string $image
    ) {
    }
}
