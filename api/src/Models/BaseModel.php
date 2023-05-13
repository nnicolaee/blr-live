<?php

declare(strict_types=1);

namespace BLRLive\Models;

abstract class BaseModel implements \JsonSerializable
{
    public function getId()
    {
        return $this->id;
    }

    abstract public static function get(string $id): ?BaseModel;

    public static function exists(string $id): bool
    {
        return !is_null(static::get($id));
    }
}
