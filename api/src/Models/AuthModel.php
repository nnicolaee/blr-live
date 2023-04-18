<?php

declare(strict_types=1);

namespace BLRLive\Models;

use BLRLive\Config;

class AuthModel
{
    public static function authenticate(string $user, string $pass): bool
    {
        // TODO: use wordpress auth database :)
        return $user == Config::AUTH_USER && $pass == Config::AUTH_PASS;
    }
}
