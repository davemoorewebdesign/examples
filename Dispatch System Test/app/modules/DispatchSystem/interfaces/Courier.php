<?php

namespace App\Modules\DispatchSystem\Interfaces;

use App\Modules\DispatchSystem\Models\Consignment;

/**
 * Interface Courier
 * We need to ensure the specific Courier class implementing have the generateUin and send methods
 * @package App\Modules\DispatchSystem\Interfaces
 */
interface Courier
{
    /**
     * @param Consignment $consignment
     * @return string
     */
    public function generateUin(Consignment $consignment): string;

    /**
     * @param array $consignments
     * @return bool
     */
    public function send(array $consignments): bool;
}