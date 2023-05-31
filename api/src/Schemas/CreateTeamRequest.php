<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class CreateTeamRequest
{
    use ValidatedSchema;

    public string $username;
    public string $name;
}
