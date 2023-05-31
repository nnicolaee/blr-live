<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class CreateBracketRequest
{
    use ValidatedSchema;

    public int $depth;
}
