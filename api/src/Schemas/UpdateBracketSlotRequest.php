<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

readonly class UpdateBracketSlotRequest
{
    use ValidatedSchema;

    public int $match;
}
