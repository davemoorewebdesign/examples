<?php

namespace App\Modules\DispatchSystem\Models;

use App\Modules\DispatchSystem\Interfaces\Courier;

/**
 * Class CourierFactory
 * This is a Factory class which acts as a gatekeeper, ensuring that only couriers that have a class are initiated.
 * I'd probably expand on this by keeping courier data in storage so more can be easily added but I'll keep it simple for this exercise.
 * @package App\Modules\DispatchSystem\Models
 */
class CourierFactory
{
    public static array $courier_classes = array('ANC', 'RoyalMail');

    /**
     * Creates a specific courier class but only if the class exists.
     * @param string $courier_class
     * @return Courier|null
     */
    public static function create(string $courier_class): ?Courier
    {
        $courier = null;
        if (in_array($courier_class, self::courier_classes)) {
            // TODO: Add real criteria when storage is decided
            $courier = $courier_class::load(array('Criteria: class_name = $courier_class'));
        }

        return $courier;
    }
}