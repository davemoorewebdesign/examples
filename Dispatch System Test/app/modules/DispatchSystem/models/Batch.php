<?php

namespace App\Modules\DispatchSystem\Models;

/**
 * Class Batch
 * Used to store a Batch's start_time and end_time (both used to determine if it is the active batch).
 *
 * @package App\Modules\DispatchSystem\Models
 */
class Batch extends Record
{
    protected ?string $start_time;
    protected ?string $end_time;

    /**
     * Start the Batch by giving it a start time and saving it. It is now considered the "active_batch".
     */
    public function start()
    {
        // Add Batch to Storage
        $this->start_time = now();
        $result = $this->save();
    }

    /**
     * End the Batch by giving it an end time.
     * The send-lists-to-couriers functionality was not included here so that it can be accessed externally if needed.
     */
    public function end()
    {
        if (isset($this->end_time)) {
            throw new ErrorException("End Batch failed. Already ended.");
        }

        // Give Batch an end_time and save
        $this->end_time = now();
        $result = $this->save();

        if (!$result) {
            throw new ErrorException("End Batch failed. Could not save Batch.");
        }
    }

    /**
     * Creates a new Consignment and associates it with this Batch's id.
     * @param string $courier_class
     * @return Consignment|null
     */
    public function addConsignment(string $courier_class): ?Consignment
    {
        if ($this->end_time) {
            throw new ErrorException("Add Consignment failed. Batch not active.");
        }

        $courier = CourierFactory::create($courier_class);
        if (!$courier) {
            throw new ErrorException("Cannot add new Consignment batch is not active.");
        }

        // Setup a new Consignment and save it to storage.
        $consignment = new Consignment($this->id, $courier);
        $result = $consignment->save();

        // Return the consignment so useful data like the consignment number can be displayed
        return $consignment;
    }

    /**
     * Gets the currently active Batch or returns null if there isn't one.
     * @return Batch|null
     */
    public static function getActiveBatch(): ?Batch
    {
        // Load a Batch record that was started today and has no end date
        // TODO: Add real criteria when storage is decided
        return Batch::load(array('Criteria: start time >= than 00:00 today, end date = null, limit 1'));
    }

    /**
     * This is only used in a BatchManager error message currently.
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}