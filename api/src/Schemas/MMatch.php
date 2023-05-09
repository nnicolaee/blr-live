<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

readonly class MMatch
{
    public function __construct(
        public int $id,
        public string $stage,
        public string $team1,
        public string $team2,
        public int $score1,
        public int $score2,
        public string $status,
        public array $games // of Game
    ) {
    }
}
