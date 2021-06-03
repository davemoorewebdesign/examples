<?php

namespace App\Modules\DispatchSystem\Models;

use App\Modules\DispatchSystem\Interfaces\Courier;

/**
 * Class ANC
 * A specific courier class that will behave different to other couriers.
 * @package App\Modules\DispatchSystem\Models
 */
class ANC extends Record implements Courier
{
    /**
     * This method is required due to the interface and allows the courier to have it's own way of generating a Consignment UIN
     * @param Consignment $consignment
     * @return string
     */
    public function generateUin(Consignment $consignment): string
    {
        // Example algorithm
        // It's possible this algorithm might require the last UIN issued to generate a new one but I've avoided this complication for this exercise
        return date('Y-d-m') . '-' . $consignment->id;
    }

    /**
     * This method is required due to the interface and allows the courier to have it's own way of sending it's Consignment list
     * @param array $consignments
     * @return bool
     */
    public function send(array $consignments): bool
    {
        $result = false;
        $uin_list = '';
        $date = new DateTime();
        $timestamp = $date->getTimestamp();

        // Iterate through Consignments and format the content for the file that will be FTPed
        foreach ($consignments as $consignment) {
            $uin_list .= $consignment->getUin() . "\r\n";
        }

        // Only send if the file contents is empty
        if ($uin_list !== '') {
            $filename = $timestamp . '-ANC.txt';
            file_put_contents($filename, $uin_list);
            // TODO: Send via ftp_connect, ftp_put etc
            $result = true;
        }

        return $result;
    }
}