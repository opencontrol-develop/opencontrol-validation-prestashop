{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="openpay-module-wrapper">
    <div class="openpay-module-header">
        <a href="http://www.openpay.mx" target="_blank" rel="external">
            <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/opencontrol-logo.png" alt="OpenControl logo" class="openpay-logo" />
        </a>
        <span class="openpay-module-intro">{l s='Empieza a protegerte del fraude con OpenControl.' mod='opencontrol'}</span>
        <a href="https://sandbox-dashboard.opencontrol.mx/" rel="external" target="_blank" class="openpay-module-create-btn">
            <span>{l s='Iniciar sesión' mod='opencontrol'}</span>
        </a>
    </div>
    <div class="openpay-module-wrap">
        <div class="openpay-module-col1 floatRight">
            <div class="openpay-module-wrap-video">
                <h3>{l s='Panel de administración' mod='opencontrol'}</h3>
                <p>{l s='Contamos con un panel donde podrás visualizar todas tus transacciones.' mod='opencontrol'}</p>
                <a target="_blank" href="https://sandbox-dashboard.opencontrol.mx">
                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/opencontrol-dashboard.png" alt="opencontrol dashboard" class="openpay-dashboard" />
                </a>
                <hr>
                <div class="openpay-prestashop-partner mt30">
                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/prestashop_partner.png" alt="" />
                </div>
            </div>
        </div>
        <div class="openpay-module-col2">
            <div class="row">
                <div class="col-md-9">
                    <h3>{l s='Beneficios' mod='opencontrol'}</h3>
                    <p>{l s='OpenControl es una herramienta para prevención de fraudes en cargos a tarjetas, enfocada en aumentar el número de transacciones seguras, reducir contracargos, reducir el porcentaje de cargos rechazados que son legítimos, así como reducir y/o eliminar las revisiones manuales. A través de una serie de reglas se valida si una transacción es fraudulenta o legítima en cuestión de milisegundos, haciendo este proceso casi imperceptible para sus clientes.' mod='opencontrol'}</p>
                </div>
            </div>
            <hr>
            <br />


            <fieldset>
                <legend><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/checks-icon.gif" alt="" />{l s='Chequeo técnico' mod='opencontrol'}</legend>
                <div class="conf">{$openpay_validation_title|escape:'htmlall':'UTF-8'}</div>
                <table cellspacing="0" cellpadding="0" class="openpay-technical">
                    {if $openpay_validation}
                        {foreach from=$openpay_validation item=validation}
                            <tr>
                                <td>
                                    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/{($validation['result']|escape:'htmlall':'UTF-8') ? 'tick' : 'close'}.png" alt="" style="height: 25px;" />
                                </td>
                                <td>
                                    {$validation['name']|escape:'htmlall':'UTF-8'}
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                </table>
            </fieldset>
            <br />

            {if $openpay_error}
                <fieldset>
                    <legend>Errors</legend>
                    <table cellspacing="0" cellpadding="0" class="openpay-technical">
                        <tbody>
                        {foreach from=$openpay_error item=error}
                            <tr>
                                <td><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/close.png" alt="" style="height: 25px;"></td>
                                <td>{$error|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </fieldset>
                <br />
            {/if}

            <form action="{$opencontrol_form_link|escape:'htmlall':'UTF-8'}" method="post">
                <fieldset class="openpay-settings">
                    <legend><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/technical-icon.gif" alt="" />{l s='Configuración' mod='opencontrol'}</legend>
                    <label>Modo</label>
                    <input type="radio" name="opencontrol_mode" value="0" {if $opencontrol_configuration.MODE == 0} checked="checked"{/if} /> {l s='Sandbox' mod='opencontrol'}
                    <input type="radio" name="opencontrol_mode" value="1" {if $opencontrol_configuration.MODE == 1} checked="checked"{/if} /> {l s='Producción' mod='opencontrol'}
                    <br /><br />
                    <table cellspacing="0" cellpadding="0" class="openpay-settings">
                        <tr>
                            <td align="center" valign="middle" colspan="2">
                                <table cellspacing="0" cellpadding="0" class="innerTable">
                                    <!-- OPENCONTROL LICENCE-->
                                    <tr>
                                        <td align="left" valign="middle">{l s='Sandbox licencia' mod='opencontrol'}</td>
                                        <td align="left" valign="middle"><input autocomplete="off" type="text" name="opencontrol_sandbox_licence" value="{if $opencontrol_configuration.SANDBOX_LICENCE}{$opencontrol_configuration.SANDBOX_LICENCE|escape:'htmlall':'UTF-8'}{/if}" /></td>
                                        <td width="15"></td>
                                        <td width="15" class="vertBorder"></td>
                                        <td align="left" valign="middle">{l s='Producción licencia' mod='opencontrol'}</td>
                                        <td align="left" valign="middle"><input autocomplete="off" type="text" name="opencontrol_live_licence" value="{if $opencontrol_configuration.LIVE_LICENCE}{$opencontrol_configuration.LIVE_LICENCE|escape:'htmlall':'UTF-8'}{/if}" /></td>
                                    </tr>
                                    <!-- OPENCONTROL MERCHANT ID-->
                                    <tr>
                                        <td align="left" valign="middle">{l s='Sandbox merchant ID' mod='opencontrol'}</td>
                                        <td align="left" valign="middle"><input autocomplete="off" type="text" name="opencontrol_sandbox_merchant_id" value="{if $opencontrol_configuration.SANDBOX_MERCHANT_ID}{$opencontrol_configuration.SANDBOX_MERCHANT_ID|escape:'htmlall':'UTF-8'}{/if}" /></td>
                                        <td width="15"></td>
                                        <td width="15" class="vertBorder"></td>
                                        <td align="left" valign="middle">{l s='Producción merchant ID' mod='opencontrol'}</td>
                                        <td align="left" valign="middle"><input autocomplete="off" type="text" name="opencontrol_live_merchant_id" value="{if $opencontrol_configuration.LIVE_MERCHANT_ID}{$opencontrol_configuration.LIVE_MERCHANT_ID|escape:'htmlall':'UTF-8'}{/if}" /></td>
                                    </tr>
                                    <!-- OPENCONTROL LLAVE PRIVADA-->
                                    <tr>
                                        <td align="left" valign="middle">{l s='Sandbox llave privada' mod='opencontrol'}</td>
                                        <td align="left" valign="middle"><input autocomplete="off" type="password" name="opencontrol_sandbox_sk" value="{if $opencontrol_configuration.SANDBOX_SK}{$opencontrol_configuration.SANDBOX_SK|escape:'htmlall':'UTF-8'}{/if}" /></td>
                                        <td width="15"></td>
                                        <td width="15" class="vertBorder"></td>
                                        <td align="left" valign="middle">{l s='Producción llave privada' mod='opencontrol'}</td>
                                        <td align="left" valign="middle"><input autocomplete="off" type="password" name="opencontrol_live_sk" value="{if $opencontrol_configuration.LIVE_SK}{$opencontrol_configuration.LIVE_SK|escape:'htmlall':'UTF-8'}{/if}" /></td>
                                    </tr>
                                    <!-- OPENCONTROL LLAVE PUBLICA-->
                                    <tr>
                                        <td align="left" valign="middle">{l s='Sandbox llave pública' mod='opencontrol'}</td>
                                        <td align="left" valign="middle"><input autocomplete="off" type="password" name="opencontrol_sandbox_pk" value="{if $opencontrol_configuration.SANDBOX_PK}{$opencontrol_configuration.SANDBOX_PK|escape:'htmlall':'UTF-8'}{/if}" /></td>
                                        <td width="15"></td>
                                        <td width="15" class="vertBorder"></td>
                                        <td align="left" valign="middle">{l s='Producción llave pública' mod='opencontrol'}</td>
                                        <td align="left" valign="middle"><input autocomplete="off" type="password" name="opencontrol_live_pk" value="{if $opencontrol_configuration.LIVE_PK}{$opencontrol_configuration.LIVE_PK|escape:'htmlall':'UTF-8'}{/if}" /></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="td-noborder save"><input type="submit" class="button" name="SubmitOpenControlConf" value="{l s='Guardar configuración' mod='opencontrol'}" /></td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>
    </div>
    <div class="clear"></div>
</div>
