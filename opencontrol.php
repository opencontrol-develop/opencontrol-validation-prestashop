<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once("helper/loader.php");
class Opencontrol extends Module
{
    private $validation = array();
    private $error = array();

    public function __construct()
    {
        $this->name = 'opencontrol';
        $this->tab = 'payment_security';
        $this->version = '1.0.0';
        $this->author = 'Openpay S.A. de C.V.';
        parent::__construct();
        $this->displayName = $this->l('OpenControl');
        $this->description = $this->l('OpenControl es una herramienta para la prevención de fraudes en cargos a tarjetas, enfocada en aumentar el número de transacciones seguras y...');
        $this->confirmUninstall = $this->l('Esta seguro de que desea desinstalar este módulo?');
    }

    public function install()
    {
        return parent::install() &&
            $this->createOpenControlApprovedState() &&
            $this->createOpenControlRefusedState() &&
            $this->registerHook('actionPaymentCCAdd') &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('displayPayment') &&
            $this->registerHook('actionPaymentConfirmation') &&
            $this->registerHook('header') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayPaymentTop') &&
            $this->installDb();
    }

    public function uninstall()
    {
        $order_status = (int) Configuration::get('PS_OS_OPENCONTROL_APPROVED');
        $orderState = new OrderState($order_status);
        $orderState->delete();

        $order_status = (int) Configuration::get('PS_OS_OPENCONTROL_REFUSED');
        $orderState = new OrderState($order_status);
        $orderState->delete();

        return parent::uninstall();
    }

    public function installDb() {

        $sql="CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ ."opencontrol_validation` (
                                `id_validation` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `id_order` INT( 11 ) UNSIGNED NULL,
                                `id_cart` INT( 11 ) UNSIGNED NULL,
                                `validation_code` INT( 11 ) UNSIGNED NULL,
                                `validation_response` varchar(255) NULL,
                                PRIMARY KEY (`id_validation`),
                                UNIQUE  `id_order` (  `id_order` ),
                                UNIQUE  `id_cart` (  `id_cart` )
                                ) ENGINE= ". _MYSQL_ENGINE_ ." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";

        Db::getInstance()->execute($sql);
    }

    private function createOpenControlApprovedState()
    {
        $state = new OrderState();
        $languages = Language::getLanguages();
        $names = array();

        foreach ($languages as $lang) {
            $names[$lang['id_lang']] = $this->l('Aprobado por OpenControl');
        }

        $state->name = $names;
        $state->color = '#3498D8';
        $state->send_email = false;
        $state->module_name = 'opencontrol';
        $templ = array();

        foreach ($languages as $lang) {
            $templ[$lang['id_lang']] = 'opencontrol';
        }

        $state->template = $templ;

        if ($state->save()) {
            try {
                Configuration::updateValue('PS_OS_OPENCONTROL_APPROVED', $state->id);
            } catch (Exception $e) {
                if (class_exists('Logger')) {
                    Logger::addLog($e->getMessage(), 1, null, null, null, true);
                }
            }
        } else {
            return false;
        }
        return true;
    }

    private function createOpenControlRefusedState()
    {
        $state = new OrderState();
        $languages = Language::getLanguages();
        $names = array();

        foreach ($languages as $lang) {
            $names[$lang['id_lang']] = $this->l('Denegado por OpenControl');
        }

        $state->name = $names;
        $state->color = '#d85a34';
        $state->send_email = false;
        $state->module_name = 'opencontrol';
        $templ = array();

        foreach ($languages as $lang) {
            $templ[$lang['id_lang']] = 'opencontrol';
        }

        $state->template = $templ;

        if ($state->save()) {
            try {
                Configuration::updateValue('PS_OS_OPENCONTROL_REFUSED', $state->id);
            } catch (Exception $e) {
                if (class_exists('Logger')) {
                    Logger::addLog($e->getMessage(), 1, null, null, null, true);
                }
            }
        } else {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        $this->context->controller->addCSS(array($this->_path.'views/css/openpay-prestashop-admin.css'));
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('SubmitOpenControlConf')) == true) {
            $configuration_values = array(
                'MODE' => Tools::getValue('opencontrol_mode'),
                'SANDBOX_LICENCE' => trim(Tools::getValue('opencontrol_sandbox_licence')),
                'LIVE_LICENCE' => trim(Tools::getValue('opencontrol_live_licence')),
                'SANDBOX_MERCHANT_ID' => trim(Tools::getValue('opencontrol_sandbox_merchant_id')),
                'LIVE_MERCHANT_ID' => trim(Tools::getValue('opencontrol_live_merchant_id')),
                'SANDBOX_SK' => trim(Tools::getValue('opencontrol_sandbox_sk')),
                'LIVE_SK' => trim(Tools::getValue('opencontrol_live_sk')),
                'SANDBOX_PK' => trim(Tools::getValue('opencontrol_sandbox_pk')),
                'LIVE_PK' => trim(Tools::getValue('opencontrol_live_pk'))
            );

            foreach ($configuration_values as $configuration_key => $configuration_value) {
                Configuration::updateValue($configuration_key, $configuration_value);
            }

            $this->configValidation();
            $validation_title = $this->checkRequirements();
        }

        /**
         * Load configuration values in admin dashboard
         */
        $this->context->smarty->assign(array(
            'opencontrol_form_link' => $_SERVER['REQUEST_URI'],
            'opencontrol_configuration' => Configuration::getMultiple(
                array(
                    'MODE',
                    'SANDBOX_LICENCE',
                    'LIVE_LICENCE',
                    'SANDBOX_MERCHANT_ID',
                    'LIVE_MERCHANT_ID',
                    'SANDBOX_SK',
                    'LIVE_SK',
                    'SANDBOX_PK',
                    'LIVE_PK'
                )
            ),
            'openpay_validation' => $this->validation,
            'openpay_error' => (empty($this->error) ? false : $this->error),
            'openpay_validation_title' => $validation_title
        ));

        /**
         * Call configuration template for admin OpenControl dashboard
         */
        return $this->display(__FILE__, 'views/templates/admin/configuration.tpl');
    }

    public function checkRequirements()
    {

        if ( empty($this->error) ) {
            $validation_title = $this->l('Todos los chequeos fueron exitosos, ahora puedes comenzar a utilizar OpenControl.');
        } else {
            $validation_title = $this->l('Al menos un problema fue encontrado, para poder comenzar a utilizar OpenControl. Por favor resuelve los problemas y refresca esta página.');
        }
        return $validation_title;
    }

    public function configValidation(){
        $data = ['session_id'=>'Check_configuration'];
        $enviroment = Configuration::get('MODE') !== null ? 'SANDBOX' : 'LIVE';
        $enviromentUrl  = Configuration::get('MODE') !== null ? Constants::SANDBOX_URL : Constants::LIVE_URL;
        $url = $enviromentUrl.'/v1/validation';
        $auth = new Auth();
        $auth->user = Configuration::get($enviroment.'_LICENCE');
        $auth->password = Configuration::get($enviroment.'_SK');
        $response = Client::execute($url, $data, $auth, 'POST');
        if ($response->httpCode !== 400) {
            $errors[] = 'Llave privada o licencia errónea. Por favor corrobore los datos.';
        }

        /**
         * If errors have been detected, show them.
         */
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->error[] = $error;
            }
        }
    }

    public function checkoutScript(){
        if (Module::isEnabled('opencontrol')){
            $enviroment = Configuration::get('MODE') !== null ? 'SANDBOX' : 'LIVE';
            $url  = Configuration::get('MODE') !== null ? Constants::SANDBOX_URL : Constants::LIVE_URL;
            $merchantId = Configuration::get($enviroment.'_MERCHANT_ID');
            $sessionId = IdGenerator::generate();
            $license = Configuration::get($enviroment.'_LICENCE');
            $publicKey = Configuration::get($enviroment.'_PK');
            $urlDeviceSessionId = $url . sprintf(Constants::DEVICE_SESSION_URL, $merchantId, $sessionId, $license, $publicKey);
            $this->context->smarty->assign(array(
                'urlDeviceSessionId' => $urlDeviceSessionId,
                'sessionId' => $sessionId
            ));
        }
    }

    public function updateApprovedOpencontrolStatus($order)
    {
        if($order->current_state != Configuration::get('PS_OS_OPENCONTROL_APPROVED')){
            $order_history = new OrderHistory();
            $order_history->id_order = (int) $order->id;
            $order_history->changeIdOrderState(Configuration::get('PS_OS_OPENCONTROL_APPROVED'), (int) $order->id);
            $order_history->addWithemail();
        }

    }

    public function updateRefusedOpencontrolStatus($order)
    {
        if($order->current_state != Configuration::get('PS_OS_OPENCONTROL_REFUSED')){
            $order_history = new OrderHistory();
            $order_history->id_order = (int) $order->id;
            $order_history->changeIdOrderState(Configuration::get('PS_OS_OPENCONTROL_REFUSED'), (int) $order->id);
            $order_history->addWithemail();
        }

    }

    public function hookActionValidateOrder($params)
    {
        Logger::addLog('Setting Opencontrol Validation Status', 1, null, null, null, true);
        $order_details = $params['order'];
        $new_order = new Order((int) $order_details->id);
        $cart = Cart::getCartByOrderId($order_details->id);

        $query = 'SELECT * FROM '._DB_PREFIX_.'opencontrol_validation WHERE id_cart = '.(int)$cart->id;
        $order_validated = Db::getInstance()->getRow($query);

        if($order_validated){
            Db::getInstance()->update('opencontrol_validation', array(
                'id_order' => pSQL($new_order->id)),
                    'id_cart = '.(int)$cart->id
            );
        }

        if($order_validated['validation_response'] == "ACCEPTED"){
            $this->updateApprovedOpencontrolStatus($new_order);
        }else{
            $this->updateRefusedOpencontrolStatus($new_order);
        }
    }

    public function hookDisplayPaymentTop($params){
        Logger::addLog('hook Opencontrol Script', 1, null, null, null, true);
        $this->checkoutScript();
        return $this->context->smarty->fetch('module:opencontrol/views/templates/front/opencontrol.tpl');
    }
}