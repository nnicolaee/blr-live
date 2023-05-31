<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class ScoreboardLine
{
    public function __construct(
        public Team $team,
        public int $wins,
        public int $draws,
        public int $losses,
        public int $score,
        public int $tiebreaker,
        public string $status
    ) {
    }
}
