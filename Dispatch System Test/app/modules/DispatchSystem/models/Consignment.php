<?php

namespace App\Modules\DispatchSystem\Models;

use App\Modules\DispatchSystem\Interfaces\Courier;

/**
 * Class Consignment
 * Used to store a Consignment's UIN and whether it has been sent already
 *
 * @package App\Modules\DispatchSystem\Models
 */
class Consignment extends Record
{
    private int $batch_id;
    private string $uin;
    private string $courier_class;
    public int $sent = 0;

    /**
     * Consignment constructor.
     * @param int $batch_id
     * @param Courier $courier
     */
    public function __construct(int $batch_id, Courier $courier)
    {
        parent::__construct();

        // Set $this->batch_id so it can be saved for this Consignment in storage
        $this->batch_id = $batch_id;

        // Set $this->courier_class so it can be saved for this Consignment in storage
        $this->courier_class = get_class($courier);

        // Set the $this->uin based on the courier's UIN algorithm so it can be saved for this Consignment in storage
        $this->uin = $courier->generateUin($this);
    }


    /**
     * Allows read only access to the UIN
     * @return string|null
     */
    public function getUin(): ?string
    {
        return $this->uin;
    }

    /**
     *  Set the Consignment to sent and save it to storage so it can't be sent again
     */
    public function markAsSent()
    {
        $this->sent = 1;
        $this->save();
    }

    /**
     * Used by the Courier to determine whether send or if it has already been sent.
     * @return bool
     */
    public function isSent(): bool
    {
        if ($this->sent === 1) {
            return true;
        }
        return false;
    }
}