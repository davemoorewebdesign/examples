<?php

namespace App\Modules\DispatchSystem\Models;

use App\Modules\DispatchSystem\Interfaces\Courier;

/**
 * Class RoyalMail
 * A specific courier class that will behave different to other couriers.
 * @package App\Modules\DispatchSystem\Models
 */
class RoyalMail extends Record implements Courier
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
        return date('Y-m-d') . '-' . $consignment->id;
    }

    /**
     * This method is required due to the interface and allows the courier to have it's own way of sending it's Consignment list
     * @param array $consignments
     * @return bool
     */
    public function send(array $consignments): bool
    {
        // TODO: These values are not normally hardcoded. This is just placeholder.
        $to = "postie@royalmail.com";
        $from = "dispatch@bobsclothing.com";
        $subject = "Bobs Clothing Consignments - " . date('Y-m-d');
        $headers = 'From: ' . $from . "\r\n" .
            'Reply-To: webmaster@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $message = "";
        foreach ($consignments as $consignment) {
            $message .= $consignment->getUin() . "\r\n";
        }

        return mail($to, $subject, $message, $headers);
    }
}