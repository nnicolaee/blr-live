<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class MMatch
{
    public function __construct(
        public int $id,
        public string $stage,
        public Team $team1,
        public Team $team2,
        public int $score1,
        public int $score2,
        public string $status,
        public array $games // of Game
    ) {
    }
}
