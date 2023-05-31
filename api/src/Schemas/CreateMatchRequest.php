<?php

declare(strict_types=1);

namespace BLRLive\Schemas;

/*readonly*/ class CreateMatchRequest
{
    use ValidatedSchema;
    
	public string $stage;
	public string $team1;
	public string $team2;
}
