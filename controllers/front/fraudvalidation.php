<?php
class OpencontrolFraudvalidationModuleFrontController extends ModuleFrontController{
    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    private $holderName;
    private $cardNumber;
    private $sessionId;
    const PAYMENT_ERROR = 'Compra fallida. Favor intente con otro método de pago';
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const OK_REPONSES = [
        "ACCEPTED"
    ];

    public function init()
    {
        require_once _PS_MODULE_DIR_ . '/opencontrol/helper/loader.php';
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();
        $ajaxResponse = 'Approved';
        $this->holderName = Tools::getValue('holderName');
        $this->cardNumber = Tools::getValue('cardNumber');
        $this->sessionId = Tools::getValue('sessionId');

        if (!isset($this->cardNumber) && strlen($this->cardNumber) != 16) {
            return;
        }

        $extraInformation = new ExtraInformation();
        $extraInformation->number = $this->cardNumber ? $this->cardNumber : '';
        $extraInformation->session = $this->sessionId ? $this->sessionId : '';

        $enviroment = Configuration::get('MODE') !== null ? 'SANDBOX' : 'LIVE';
        $enviromentUrl  = Configuration::get('MODE') !== null ? Constants::SANDBOX_URL : Constants::LIVE_URL;
        $url = $enviromentUrl.'/v1/validation';

        $auth = new Auth();
        $auth->user = Configuration::get($enviroment.'_LICENCE');
        $auth->password = Configuration::get($enviroment.'_SK');

        $shipping_address = new Address($this->context->cart->id_address_delivery);
        $billing_address = new Address($this->context->cart->id_address_invoice);
        $cart = $this->context->cart;
        $customer = new Customer((int) $cart->id_customer);
        $products = $cart->getProducts();
        $total_amount = number_format(floatval($cart->getOrderTotal()), 2, '.', '');

        $order['shipping_address'] = $shipping_address;
        $order['billing_address'] = $billing_address;
        $order['cart'] = $cart;
        $order['customer'] = $customer;
        $order['products'] = $products;
        $order['total_amount'] = $total_amount;

        $data = ValidationService::create($order, $extraInformation);
        $response = Client::execute($url, $data, $auth, 'POST');
        $body = $response->body;

        if ($response->httpCode === self::HTTP_OK && !in_array($body['response'], self::OK_REPONSES) /*|| $response->httpCode === self::HTTP_BAD_REQUEST*/) {
            $matchedName = (isset($response->body['data']['matched_rules'])) ? 'matched_rules' : 'matched_list';
            //http_response_code(400);
            $ajaxResponse = 'Denied';
        }

        if($cart->id != 0){
            $query = 'SELECT * FROM '._DB_PREFIX_.'opencontrol_validation WHERE id_cart = '.(int)$cart->id;
            $order_validated = Db::getInstance()->getRow($query);

            if ($order_validated){
                Db::getInstance()->update('opencontrol_validation', array(
                    'validation_code' => pSQL($body['code']),
                    'validation_response' => pSQL($body['response'])),
                    'id_cart = '.(int)$cart->id
                );
            }else{
                Db::getInstance()->insert('opencontrol_validation', array(
                    'id_cart' => pSQL($cart->id),
                    'validation_code' => pSQL($body['code']),
                    'validation_response' => pSQL($body['response']))
                );
            }
        }
        echo json_encode($ajaxResponse);
        exit(0);
    }

}