<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class StageBrief
{
    public function __construct(
        public string $name,
        public ?string $bracket
    ) {
    }
}
