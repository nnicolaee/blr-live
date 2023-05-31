<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class AddGameRequest
{
    use ValidatedSchema;
    
	public string $outcome;
}
