<?php

namespace App\Modules\DispatchSystem\Models;

/**
 * Class BatchManager
 * This controls access to the active branch and provides the 3 functions required.
 * It is also possible for a dev to not use this and do it directly using Batch and ConsignmentSender but this class makes it a lot easier.
 * This could also be extended to manage multiple batches.
 * @package App\Modules\DispatchSystem\Models
 */
class BatchManager
{
    private static ?self $instance = null;
    private ?Batch $active_batch;

    /**
     * BatchManager constructor.
     * Private to prevent multiple initiations.
     */
    private function __construct()
    {
        $this->active_batch = Batch::getActiveBatch();
    }

    /**
     * Checks if an instance exists and if not, it creates one.
     * @return BatchManager
     */
    public static function getInstance(): self
    {
        if (self::$instance !== null) {
            self::$instance = new BatchManager;
        }

        return self::$instance;
    }

    /**
     * Used to create a Batch and assign to $this->active_batch.
     * This is not in Batch class because we need to track the active Batch.
     * (An active Batch has a start_time that includes today's date and has no end_time.)
     * @return Batch
     */
    public function startBatch(): ?Batch
    {
        // If there is an active batch, throw an exception
        if ($this->hasActiveBatch()) {
            throw new ErrorException("Cannot start a new Batch. You must first end the active Batch (id: {$this->active_batch->getId()})");
        }

        // Otherwise create a new Batch and start it
        $this->active_batch = new Batch;
        $this->active_batch->start();

        return $this->active_batch;
    }

    /**
     * Used to end the active Batch.
     * This is not in Batch class because we need to track the active Batch.
     */
    public function endBatch()
    {
        if ($this->hasActiveBatch()) {
            $this->active_batch->end();
            $this->sendConsignments($this->active_batch);
            $this->active_batch = null;
        } else {
            throw new ErrorException("End Batch failed. No active batch.");
        }
    }

    /**
     * Used to initiate the sending of consignment lists to couriers.
     * I opted for a static method here so it can be called with any Batch.
     * This is because a batch might not get sent at the end of the day as planned due to unforeseen circumstances.
     * Consignment UINs wont be sent if the Consignment is already marked as sent.
     * @param Batch $batch
     */
    public static function sendConsignments(Batch $batch)
    {
        $consignment_sender = new ConsignmentSender;
        $consignment_sender->send($batch);
    }

    //

    /**
     * Can be used by UI dev to check if for an active batch before starting, adding a consignment or ending without an exception being thrown.
     * @return bool
     */
    public function hasActiveBatch(): bool
    {
        if (isset($this->active_batch)) {
            return true;
        }
        return false;
    }

    /**
     * Allows access to the current active branch
     * @return Batch|null
     */
    public function getActiveBatch(): ?Batch
    {
        return $this->active_batch;
    }
}