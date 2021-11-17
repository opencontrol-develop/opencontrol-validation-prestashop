{*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


{if isset($smarty.get.typeReturn) && $smarty.get.typeReturn == 'failure'}
    <div class='alert alert-danger alert-dismissible'>
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>×</span>
        </button>
        ¡Ops! Compra fallida, Intente nuevamente con otro método de pago.
    </div>
{/if}

<script src="https://code.jquery.com/jquery-3.1.0.min.js"
        integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>

<script type="text/javascript">

    $(document).ready(function () {
        let formId = undefined;

        $(".payment-options input[type='radio']").on("change", function (e) {
            console.log(e);
            formId = $(this).attr('id');
            console.log(formId);
            //$("#pay-with-"+formId+"-form form").attr('onsubmit','return false;');
        });

        $("#payment-confirmation .ps-shown-by-js button").on("click", function (e) {
            let myPaymentMethodSelected = $(".payment-options").find("input[id*='payment-option-']:checked");
            let formId = myPaymentMethodSelected.attr('id');

            if (myPaymentMethodSelected.is(":checked")) {
                e.preventDefault();
                let iframe = jQuery('iframe#opencontrol');
                if (iframe.length == 0) {
                    console.log("Create Iframe");
                    document.body.insertAdjacentHTML("beforeend", "<iframe id='opencontrol' style='width:0;height:0;border:0;border:none;'" +
                        "src='{$urlDeviceSessionId}'></iframe>");
                }

                let opencontrolValidation = jQuery("#pay-with-" + formId + "-form").find("input[id*='card-number']").val().replace(/ /g, '');
                let opencontrolValidationHold = jQuery("input[id='field-firstname']").val() + " " + jQuery("input[id='field-lastname']").val();
                let lengthOpencontrol = opencontrolValidation.length;
                let fourDigits = lengthOpencontrol - 4;
                let opencontrolValidationNumber = opencontrolValidation.substr(0, 6) + '000000' + opencontrolValidation.substr(fourDigits, lengthOpencontrol);

                //console.log(opencontrolValidationHold);
                //console.log("NUMBER: " + opencontrolValidationNumber);
                //console.log("SESSION ID: " + '{$sessionId}');

                $.ajax({
                    type: 'POST',
                    url: prestashop.urls.base_url + 'module/opencontrol/fraudvalidation',
                    dataType: 'json',
                    data: {
                        holderName: opencontrolValidationHold,
                        cardNumber: opencontrolValidationNumber,
                        sessionId: '{$sessionId}'
                    },
                    success: function (response) {
                        //console.log("AJAX SUCCESS");
                        console.log(response);
                    },
                    error: function (response) {
                        //console.error("AJAX ERROR");
                        //console.error(response);
                    }
                });


            }
        });
    });
</script>