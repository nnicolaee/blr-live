<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class UpdateTeamRequest
{
    use ValidatedSchema;

    public string $name;
}
