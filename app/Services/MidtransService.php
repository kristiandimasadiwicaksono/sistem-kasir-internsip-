<?php
namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService {
    public function __construct()
    {

    }

    public function createTransaction($params)
    {
        return Snap::createTransaction('$params');
    }
}
?>