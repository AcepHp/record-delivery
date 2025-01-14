<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\PurchaseRequest; // Tambahkan ini

class StatusSubmitChanged
{
    use SerializesModels;

    public $data;

    public function __construct(PurchaseRequest $data)
    {
        $this->data = $data;
    }
}



