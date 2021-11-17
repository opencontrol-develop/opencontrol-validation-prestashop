<?php

class OpencontrolPayment
{
    const REQUIRED_FIELDS = [
        'card'
    ];
    
    public $channel = '9'; //Comercio eléctronico

    public $card;

    public $address;

    public function __construct() {
        $this->card = new OpencontrolCard();
        $this->address = new OpencontrolAddress();
    }
}