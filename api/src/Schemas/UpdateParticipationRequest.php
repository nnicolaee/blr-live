<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

readonly class UpdateParticipationRequest
{
    use ValidatedSchema;

    public string $status;
}
