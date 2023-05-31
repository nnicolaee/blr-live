<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class Participation
{
    public function __construct(
        public string $team,
        public string $stage,
        public string $status
    ) {
    }
}
