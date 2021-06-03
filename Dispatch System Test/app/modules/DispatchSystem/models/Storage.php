<?php

namespace App\Modules\DispatchSystem\Models;

/**
 * Class Storage
 *
 * @package App\Modules\DispatchSystem\Models
 */
class Storage
{
    private static ?self $instance = null;

    /**
     * Storage constructor.
     * Private to prevent multiple initiations.
     */
    private function __construct()
    {
    }

    /**
     * Checks if an instance exists and if not, it creates one.
     * @return Storage
     */
    public static function getInstance(): self
    {
        if (self::$instance == null) {
            self::$instance = new Storage;
        }

        return self::$instance;
    }

    public function query(string $query): ?array
    {
        // TODO: Connect to storage, send $query string and set result
        $result = null;

        return $result;
    }
}