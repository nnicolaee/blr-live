<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class UpdateStageRequest
{
    use ValidatedSchema;

    public ?int $bracket;
}
