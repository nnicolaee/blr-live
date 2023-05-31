<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class CreateStageRequest
{
    use ValidatedSchema;
    
	public string $name;
}
