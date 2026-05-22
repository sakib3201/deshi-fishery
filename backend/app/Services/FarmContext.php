<?php

declare(strict_types=1);

namespace App\Services;

class FarmContext
{
    private static ?int $farmId = null;

    public static function setFarmId(?int $farmId): void
    {
        self::$farmId = $farmId;
    }

    public static function getFarmId(): ?int
    {
        return self::$farmId;
    }

    public static function clear(): void
    {
        self::$farmId = null;
    }
}
