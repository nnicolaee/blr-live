<?php

declare(strict_types=1);

namespace BLRLive\Models;

abstract class BaseModel implements \JsonSerializable
{
    protected static string $baseUrl;

    public function getId()
    {
        return $this->id;
    }

    /*public function getUrl(): string
    {
        return static::$baseUrl . '/' . $this->getId();
    }*/

    abstract public static function get(string $id): ?BaseModel;

    public static function exists(string $id): bool
    {
        return !is_null(static::get($id));
    }

    /*public static function fromUrl(string $url): ?BaseModel
    {
        if (!str_starts_with($url, static::baseUrl . '/')) {
            return null;
        }

        $id = substr($url, strlen(static::baseUrl . '/'));
        return static::get($id);
    }*/
}
