<?php

class ValidationService {
    public static function create($order, $extraInformation){
        $request = new Validation();
        self::setGeneralInformation($order, $request, $extraInformation);
        self::setProductsInformation($order, $request);
        self::setCustomerInformation($order, $request);
        self::setPaymentInformation($order, $request, $extraInformation);
        self::setShippingInformation($order, $request);
        self::setBillingInformation($order, $request);
        self::setMerchantInformation($order, $request);
        return $request;
    }

    public static function setGeneralInformation($order, $request, $extraInformation) {
        $request->id = IdGenerator::generate(10);
        $request->session_id = $extraInformation->session;
        $request->amount = $order['total_amount'];
        $request->order_id = $order['cart']->id;
    }

    public static function setProductsInformation($order, $request) {
        $items = $order['products'];
        foreach ($items as $item) {
            $product = new OpenControlProduct();
            $product->id = $item['id_product'];
            $product->name = $item['name'];
            $product->quantity = $item['cart_quantity'];
            $product->total_amount = number_format(floatval($product->quantity * $item['price_wt']), 2, '.', '');
            $product->type = $item['is_virtual'] ? OpenControlProduct::TYPE_OF_PRODUCTS["DIGITAL"] : OpenControlProduct::TYPE_OF_PRODUCTS["PHYSICAL"];
            $request->products[] = $product;
        }
    }
    
    public static function setCustomerInformation($order, $request) {
        $request->customer->id = ( $order['customer']->is_guest != 0 ) ? 'GUEST' : $order['customer']->id;
        $request->customer->name = ( $order['billing_address']->firstname ) ? $order['billing_address']->firstname : $order['shipping_address']->firstname;
        $request->customer->last_name = ($order['billing_address']->lastname) ? $order['billing_address']->lastname : $order['shipping_address']->lastname ;
        $request->customer->phone = ($order['billing_address']->phone) ? $order['billing_address']->phone : $order['shipping_address']->phone;
        $request->customer->email = $order['customer']->email;
    }
    
    public static function setPaymentInformation($order, $request, $extraInformation) {
        $request->payment->address->line1 = ( $order['billing_address']->address1 ) ? $order['billing_address']->address1 : $order['shipping_address']->address1;
        $request->payment->address->line2 = ( $order['billing_address']->address2 ) ? $order['billing_address']->address2 : $order['shipping_address']->address2;
        $request->payment->address->city = ( $order['billing_address']->city ) ? $order['billing_address']->city : $order['shipping_address']->city;
        $request->payment->address->state = (State::getNameById($order['billing_address']->id_state)) ? State::getNameById($order['billing_address']->id_state) : State::getNameById($order['shipping_address']->id_state);
        $request->payment->address->postal_code = ( $order['billing_address']->postcode ) ? $order['billing_address']->postcode : $order['shipping_address']->postcode;
        $request->payment->card->number = $extraInformation->number;
        $request->payment->card->holder_name = ( $order['billing_address']->firstname ) ? $order['billing_address']->firstname . " " . $order['billing_address']->lastname : $order['shipping_address']->firstname . " " . $order['shipping_address']->lastname;
    }

    public static function setShippingInformation($order, $request) {
        $request->shipping->line1 = ($order['shipping_address']->address1) ? $order['shipping_address']->address1 : $order['billing_address']->address1;
        $request->shipping->line2 = ($order['shipping_address']->address2) ? $order['shipping_address']->address2 : $order['billing_address']->address2;
        $request->shipping->city = ($order['shipping_address']->city) ? $order['shipping_address']->city : $order['billing_address']->city;
        $request->shipping->state = (State::getNameById($order['shipping_address']->id_state)) ? State::getNameById($order['shipping_address']->id_state) : State::getNameById($order['billing_address']->id_state);
        $request->shipping->postal_code = ($order['shipping_address']->postcode) ? $order['shipping_address']->postcode : $order['billing_address']->postcode;
    }

    public static function setBillingInformation($order, $request) {
        $request->billing->line1 = ( $order['billing_address']->address1 ) ? $order['billing_address']->address1 : $order['shipping_address']->address1;
        $request->billing->line2 = ( $order['billing_address']->address2 ) ? $order['billing_address']->address2 : $order['shipping_address']->address2;
        $request->billing->city = ( $order['billing_address']->city ) ? $order['billing_address']->city : $order['shipping_address']->city;
        $request->billing->state = (State::getNameById($order['billing_address']->id_state)) ? State::getNameById($order['billing_address']->id_state) : State::getNameById($order['shipping_address']->id_state);
        $request->billing->postal_code = ( $order['billing_address']->postcode ) ? $order['billing_address']->postcode : $order['shipping_address']->postcode;
    }

    public static function setMerchantInformation($order, $request) {
        $enviroment = Configuration::get('MODE') !== null ? 'SANDBOX' : 'LIVE';
        $merchantId = Configuration::get($enviroment.'_MERCHANT_ID');
        $request->merchant->id = $merchantId;
    }
}
