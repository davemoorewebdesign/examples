<?php

namespace App\Modules\DispatchSystem\Models;

use App\Modules\DispatchSystem\Interfaces\Courier;

/**
 * Class ConsignmentSender
 * This purely concerned with taking Consignments from each Courier in a Batch and giving them to the Courier to send in their specific way.
 * @package App\Modules\DispatchSystem\Models
 */
class ConsignmentSender
{
    /**
     * Sends an array of Consignments to each courier so they can processed into a format that is sent to that courier.
     * Only Consignments that are not already sent will be added to the array.
     * @param Batch $batch
     * @return bool
     */
    public function send(Batch $batch): bool
    {
        $result = false;

        // Get all couriers
        // TODO: Add real criteria when storage is decided
        $couriers = Courier::all(array('Criteria: none, all Couriers are returned'));

        // Iterate through couriers and send a consignment list for each
        foreach ($couriers as $courier) {
            // Get all Consignments in batch for this courier that are not sent already
            // TODO: Add real criteria when storage is decided
            $consignments = Consignment::all(array('Criteria: batch_id = $batch->id AND courier_id = $courier->id AND sent != 1'));
            if (count($consignments)) {
                $result = $courier->send($consignments);
                if ($result) {
                    // If successfully sent, mark the Consignment as sent in storage
                    foreach ($consignments as $consignment) {
                        $consignment->markAsSent();
                    }
                }
            }
        }

        return $result;
    }
}