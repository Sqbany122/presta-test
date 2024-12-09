{*
* @author    Innovation Software Sp.z.o.o
* @copyright 2018 Innovation Software Sp.z.o.o
* @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @category  apaczkashipment
* @package   apaczkashipment
* @version   1.1
*}

<br>
<fieldset id="fieldset_2" style="margin-bottom: 20px;
    padding: 10px;
    border: solid 1px #d3d8db;
    background-color: #fff;
    -webkit-border-radius: 5px;
    border-radius: 5px;">
    <legend style="height: 0; margin-bottom: 0; padding-block-end: 34px; margin-top: 29px; border: none;">
        <img src="../modules/apaczkashipping/views/img/logoMed.png">{l s='Apaczka Shipping' mod='apaczkashipping'}
    </legend>
    <div id="apaczka_orderDialog" style="margin-top: 35px;">
        <form method="POST"
              action="index.php?controller=AdminOrders&id_order={$id_order|escape:'htmlall':'UTF-8'}&vieworder&sendsubmit=1&token={$token|escape:'htmlall':'UTF-8'}"
              id="apaczka_orderForm">
            {l s='Przed zamówieniem kuriera upewnij się czy poniższe dane są prawidłowe.' mod='apaczkashipping'}
            <table style="width: 60%">
                <tbody>
                <tr>
                    <td style="font-weight: bold; text-align: center">{l s='Nadawca' mod='apaczkashipping'}</td>
                    <td style="font-weight: bold; text-align: center">{l s='Odbiorca' mod='apaczkashipping'}</td>
                </tr>

                <tr>
                    <td style="vertical-align: top">
                        <table>
                            <tbody>
                            <tr>
                                <td>
                                    {l s='Ksiazka adresowa:' mod='apaczkashipping'}
                                </td>
                                <td>
                                    <style>
                                        th, td {
                                            padding: 5px;
                                            text-align: left;
                                        }
                                    </style>
                                    {if count($contacts)>0}
                                        <select name="cont_id" id="cont_id" onchange="nowyAdresNadawcy(this.value);">
                                            {foreach from=$contacts item=contact}
                                                <option value="{$contact['contact_id']|escape:'htmlall':'UTF-8'}">
                                                    {$contact['nazwa']|escape:'htmlall':'UTF-8'}
                                                    , {$contact['miasto']|escape:'htmlall':'UTF-8'}
                                                </option>
                                            {/foreach}
                                        </select>
                                    {else}
                                        <br>
                                    {/if}

                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label for="sender_name" class=" required">
                                        {l s='Nazwa:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" id="sender_name" name="sender_name" size="30"
                                           maxlength="35" value="{$S_company|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="sender_addressLine1" class=" required">
                                        {l s='Adres:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" id="sender_addressLine1"
                                           name="sender_addressLine1" size="30" maxlength="35"
                                           value="{$S_address1|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="sender_addressLine2">
                                        {l s='Adres cd.:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" id="sender_addressLine2" name="sender_addressLine2" size="30"
                                           maxlength="35" value="{$S_address2|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label for="sender_postalCode" class=" required">
                                        {l s='Kod pocztowy:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" id="sender_postalCode"
                                           name="sender_postalCode" size="10" maxlength="10"
                                           value="{$S_postcode|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label for="sender_city" class=" required">
                                        {l s='Miasto:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" id="sender_city" name="sender_city" size="20"
                                           maxlength="35" value="{$S_city|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="sender_countryID"
                                           class=" required">{l s='Kraj' mod='apaczkashipping'}</label></td>
                                <td>
                                    <select name="sender_countryID" id="sender_countryID">
                                        {foreach from=$countries item=country}
                                            <option value="{$country->id|escape:'htmlall':'UTF-8'}"
                                                    {if $country->id == $S_country|escape:'htmlall':'UTF-8'}selected=""{/if}>
                                                {$country->name|escape:'htmlall':'UTF-8'}
                                            </option>
                                        {/foreach}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="sender_contactName" class=" required">
                                        {l s='Osoba kontaktowa:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="sender_contactName"
                                           id="sender_contactName" size="30" maxlength="35"
                                           value="{$S_contactname|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="sender_phone" class=" required">
                                        {l s='Telefon:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="sender_phone" id="sender_phone"
                                           size="20" maxlength="15" value="{$S_phone|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="sender_email">
                                        {l s='E-mail:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="sender_email" id="sender_email"
                                           size="30" maxlength="100" value="{$S_email|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {l s='Dodaj do ksiazki adresowej:' mod='apaczkashipping'}
                                </td>
                                <td>
                                    <input type="submit" required="required" onclick="linkFun1();" id="addContact"
                                           name="addContact" class="button" value="Zapisz do książki adresowej">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <!------------------------------------------------------ DRUGA KOLUMNA --------------------------------------------------------------------------->
                    <td style="vertical-align: top">
                        <table>
                            <tbody>
                            <tr>
                                <td>
                                    <br></td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="receiver_name" class=" required">
                                        {l s='Nazwa:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="receiver_name" id="receiver_name"
                                           size="30" maxlength="35"
                                           value="{if $R_company!==''}{$R_company|escape:'htmlall':'UTF-8'}{else}{$R_firstname|escape:'htmlall':'UTF-8'} {$R_lastname|escape:'htmlall':'UTF-8'}{/if}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="receiver_addressLine1" class="required">
                                        {l s='Adres:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="receiver_addressLine1"
                                           id="receiver_addressLine1" size="30" maxlength="35"
                                           value="{$R_address1|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label for="receiver_addressLine2">
                                        {l s='Adres cd.:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" name="receiver_addressLine2" id="receiver_addressLine2" size="30"
                                           maxlength="35" value="{$R_address2|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="receiver_postalCode" class=" required">
                                        {l s='Kod pocztowy:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="receiver_postalCode"
                                           id="receiver_postalCode" size="10" maxlength="10"
                                           value="{$R_postcode|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="receiver_city" class="required">
                                        {l s='Miasto:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="receiver_city" id="receiver_city"
                                           size="20" maxlength="35" value="{$R_city|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='receiver_countryId' class="required">
                                        {l s='Kraj:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <select name="receiver_countryId" id="receiver_countryId"
                                            onChange="changeCountry();">
                                        {foreach from=$countries item=country}
                                            <option value="{$country->id|escape:'htmlall':'UTF-8'}"
                                                    {if $country->code == $R_country || $country->id == $R_country  }
                                                        selected
                                                    {/if}>
                                                {$country->name|escape:'htmlall':'UTF-8'}
                                            </option>
                                        {/foreach}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='receiver_contactName' class="required">
                                        {l s='Osoba kontaktowa:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="receiver_contactName"
                                           id="receiver_contactName" size="30" maxlength="35"
                                           value="{$R_firstname|escape:'htmlall':'UTF-8'} {$R_lastname|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='receiver_phone' class=" required">
                                        {l s='Telefon:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="receiver_phone" id="receiver_phone"
                                           size="20" maxlength="15" value="{$R_phone|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='receiver_email'>
                                        {l s='E-mail:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <input type="text" required="required" name="receiver_email" id="receiver_email"
                                           size="30" maxlength="100" value="{$R_email|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table>
                            <tbody>

                            <tr>
                                <td>
                                    <label class=" required">Opis zawartości<br>(max. 35 znaków): </label></td>
                                <td>
                                    <input type="text" name="contents" id="contents" maxlength="35" size="40"
                                           value="{$DEF_contents|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Domyślny opis<br>(np. nr zamówienia, faktury): </label></td>
                                <td>
                                    <input type="text" name="referenceNumber" id="referenceNumber" size="30"
                                           maxlength="35" value="{$RefNum|escape:'htmlall':'UTF-8'}">
                                </td>
                            </tr>

                            <tr>
                                <td><label class=" required">{l s='Usługa' mod='apaczkashipping'}</label></td>
                                <td>
                                    <select name="serviceCode" id="serviceCode"
                                            onChange="refreshPickupTimeSelects(); showOrHideFields(); handleDpd(); ">

                                        <option value="UPS_K_STANDARD" {if $DEF_serviceCode == 'UPS Standard' || $DEF_serviceCode == 'UPS_K_STANDARD'} selected="true"{/if}>
                                            UPS Standard
                                        </option>
                                        <option value="UPS_Z_STANDARD" {if $DEF_serviceCode == 'UPS Zagranica' || $DEF_serviceCode == 'UPS_Z_STANDARD'} selected="true"{/if}>
                                            UPS Zagranica
                                        </option>
                                        <option value="UPS_K_EX_SAV" {if $DEF_serviceCode == 'UPS Expres Saver KRAJ' || $DEF_serviceCode == 'UPS_K_EX_SAV'} selected="true"{/if}>
                                            UPS Express Saver KRAJ
                                        </option>
                                        <option value="UPS_Z_EX_SAV" {if $DEF_serviceCode == 'UPS Expres Saver ZAGR' || $DEF_serviceCode == 'UPS_K_EX_SAV'} selected="true"{/if}>
                                            UPS Express Saver ZAGR
                                        </option>
                                        <option value="DHLSTD" {if $DEF_serviceCode == 'DHL Standard' || $DEF_serviceCode == 'DHLSTD'} selected="true"{/if}>
                                            DHL Standard
                                        </option>
                                        <option value="DHL12" {if $DEF_serviceCode == 'DHL Express 12' || $DEF_serviceCode == 'DHL12'} selected="true"{/if}>
                                            DHL Express 12
                                        </option>
                                        <option value="KEX_EXPRESS" {if $DEF_serviceCode == 'K-EX Express' || $DEF_serviceCode == 'KEX_EXPRESS'} selected="true"{/if}>
                                            K-EX Express
                                        </option>
                                        <option value="FEDEX" {if $DEF_serviceCode == 'FEDEX' } selected="true"{/if}>
                                            FEDEX
                                        </option>
                                        <option value="DPD_CLASSIC" {if $DEF_serviceCode == 'DPD Classic' || $DEF_serviceCode == 'DPD_CLASSIC'} selected="true"{/if}>
                                            DPD Classic
                                        </option>
                                        <option value="DPD_CLASSIC_FOREIGN" {if $DEF_serviceCode == 'DPD Classic Foreign' || $DEF_serviceCode == 'DPD_CLASSIC_FOREIGN'} selected="true"{/if}>
                                            DPD Classic Foreign
                                        </option>
                                        <option value="TNT_Z" {if $DEF_serviceCode == 'TNT Economy Express' || $DEF_serviceCode == 'TNT_Z'} selected="true"{/if}>
                                            TNT Economy Express
                                        </option>
                                        <option value="POCZTA_POLSKA_E24" {if $DEF_serviceCode == 'Pocztex 24' || $DEF_serviceCode == 'POCZTA_POLSKA_E24'} selected="true"{/if}>
                                            Pocztex 24
                                        </option>
                                        <option value="PACZKOMAT"
                                                {if $DEF_serviceCode == 'PACZKOMAT' || $DEF_serviceCode == 'InPost Paczkomaty'}
                                        selected
                                                {/if}>
                                            {l s='InPost Paczkomaty' mod='apaczkashipping'}
                                        </option>
                                        <option value="APACZKA_DE"
                                                {if $DEF_serviceCode == 'APACZKA_DE' || $DEF_serviceCode == 'Apaczka Niemcy'}
                                        selected
                                                {/if}>
                                            {l s='Apaczka Niemcy' mod='apaczkashipping'}
                                        </option>
                                        <option value="INPOST"
                                                {if $DEF_serviceCode == 'INPOST' || $DEF_serviceCode == 'InPost Kurier'}
                                        selected
                                                {/if}>
                                            {l s='InPost Kurier' mod='apaczkashipping'}
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="apaczka-chooseParcelLockerReceiverRow">
                                {if isset($apaczka_parcel_locker_delivery_obj) && $apaczka_parcel_locker_delivery_obj != NULL}
                                    {assign var=receiver_parcel_locker_code  value=$apaczka_parcel_locker_delivery_obj->receiver_parcel_locker_code}
                                {else}
                                    {assign var=receiver_parcel_locker_code  value=''}
                                {/if}
                                <td>
                                    <label>
                                        {l s='Wybrany paczkomat:' mod='apaczkashipping'}
                                    </label>
                                    <input id="apaczka-chosenReceiverParcelLockerName"
                                           name="apaczka-chosenReceiverParcelLockerName" type="hidden"
                                           value="{$receiver_parcel_locker_code|escape:'htmlall':'UTF-8'}">
                                    <input id="apaczka-orderId" type="hidden"
                                           value="{$id_order|escape:'htmlall':'UTF-8'}">
                                    {if isset($receiver_parcel_locker_code) && !empty($receiver_parcel_locker_code)}
                                        <span id="apaczka-chosenReceiverParcelLockerNameText"
                                              class="parcelLockerLabelChecked">
                                            {$receiver_parcel_locker_code|escape:'htmlall':'UTF-8'}
                                        </span>
                                    {else}
                                        <span id="apaczka-chosenReceiverParcelLockerNameText"
                                              class="parcelLockerLabelUnchecked">
                                            {l s='Brak' mod='apaczkashipping'}
                                        </span>
                                    {/if}
                                </td>
                                <td>
                                    <button id="openReceiverParcelLockerModalButton" class="btn btn-primary">
                                        {l s='Wybierz paczkomat klienta' mod='apaczkashipping'}
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr>
                                </td>
                            </tr>
                            <!--    Do schowania        --><br>
                            <tr id="shipmentTypeCodeRow">
                                <td><label class=" required">{l s='Typ przesyłki' mod='apaczkashipping'} </label></td>
                                <td>
                                    <table colspan="2">
                                        <td>

                                            <input type="radio" id="shipmentTypeCode1" name="shipmentTypeCode"
                                                   onchange="shipmentChange('shipmentTypeCode1'); showMultipack();"
                                                   value="LIST">List
                                        </td>
                                        <td>

                                            <input type="radio" id="shipmentTypeCode2" name="shipmentTypeCode"
                                                   onchange="shipmentChange('shipmentTypeCode2'); showMultipack();"
                                                   value="PACZ" checked>Paczka
                                        </td>

                                    </table>
                                </td>
                            </tr>
                            <style type="text/css">
                                label {
                                    width: 100%;
                                    height: 100%;
                                    text-align: left
                                }
                            </style>
                            {l s='Niestandard' mod='apaczkashipping'}
                            {l s='Duża paczka' mod='apaczkashipping'}
                            <tr id="noStdField">
                                <td>
                                    <label>
                                        {l s='Paczka niestandardowa' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <table colspan="2">
                                        <td>
                                            <input type="checkbox" id="noStd" name="noStd"
                                                   onchange="noStdChange('noStd');"
                                                   value=0> {l s='Niestandard' mod='apaczkashipping'}</td>
                                        <td id="bigPackLabel" style="display:none">
                                            <input type="checkbox" id="bigPack" name="bigPack"
                                                   onchange="noStdChange('bigPack');"
                                                   value=0> {l s='Duża paczka' mod='apaczkashipping'}</td>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="required">
                                        {l s='Sposób nadania:' mod='apaczkashipping'}
                                    </label>
                                </td>
                                <td>
                                    <select name="orderPickupType" id="orderPickupType"
                                            onChange="refreshPickupTimeSelects();">
                                        <option value="SELF" {if $DEF_orderPickupType == 'SELF'} selected="true"{/if}>
                                            samodzielne dostarczenie do kuriera
                                        </option>
                                        <option value="COURIER" {if $DEF_orderPickupType == 'COURIER'} selected="true"{/if}>
                                            zamowienie odbioru przesylek
                                        </option>
                                        <option value="BOX_MACHINE" {if $DEF_orderPickupType == 'BOX_MACHINE'} selected{/if}>
                                            InPost Paczkomaty
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="apaczka-chooseParcelLockerSenderRow">
                                {if isset($apaczka_parcel_locker_delivery_obj) && $apaczka_parcel_locker_delivery_obj != NULL}
                                    {assign var=sender_parcel_locker_code  value=$apaczka_parcel_locker_delivery_obj->sender_parcel_locker_code}
                                {else}
                                    {assign var=sender_parcel_locker_code  value=''}
                                {/if}
                                <td>
                                    <label>
                                        {l s='Wybrany paczkomat:' mod='apaczkashipping'}
                                    </label>
                                    <input id="apaczka-chosenSenderParcelLockerName"
                                           name="apaczka-chosenSenderParcelLockerName" type="hidden"
                                           value="{$sender_parcel_locker_code|escape:'htmlall':'UTF-8'}">
                                    {if isset($apaczka_parcel_locker_delivery_obj->sender_parcel_locker_code) && !empty($apaczka_parcel_locker_delivery_obj->sender_parcel_locker_code)}
                                        <span id="apaczka-chosenSenderParcelLockerNameText"
                                              class="parcelLockerLabelChecked">{$sender_parcel_locker_code|escape:'htmlall':'UTF-8'}</span>
                                    {else}
                                        <span id="apaczka-chosenSenderParcelLockerNameText"
                                              class="parcelLockerLabelUnchecked">{l s='Brak' mod='apaczkashipping'}</span>
                                    {/if}
                                </td>
                                <td>
                                    <button id="openSenderParcelLockerModalButton"
                                            class="btn btn-primary">{l s='Wybierz paczkomat nadawcy' mod='apaczkashipping'}</button>
                                </td>
                            </tr>
                            </tbody>

                            <tbody id="orderPickupDetails" {if $DEF_orderPickupType=='SELF'} style="display: none"{/if}>
                            <tr>
                                <td>
                                    <label class="required">Data: </label></td>
                                <td>
                                    <select name="pickupDate" id="pickupDate" class="hasDatepicker"
                                            onChange="refreshPickupTimeSelects();">
                                        {for $i=0 to 0}
                                            {if (  (((intval(date("w"))+$i)%7)!== 0 ) && (((intval(date("w"))+$i)%7)!== 6 ) )}
                                                <option
                                                value="{date("Y-m-d", time()+$i*86400)}">{date("Y-m-d", time()+$i*86400)}</option>{/if}
                                        {/for}

                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><label class=" required">W godzinach: </label></td>
                                <td>
                                    <table>
                                        <td>
                                            <select name="pickupTimeFrom" id="pickupTimeFrom"
                                                    onChange="onPickupTimeFromChangeNew();">
                                                <!--wszystkie opcje sa pobierane z apaczka zaleznie od kodu pocztowego i typu usługi-->
                                            </select>
                                        </td>
                                        <td>
                                            <label>_</label>
                                        </td>

                                        <td><select name="pickupTimeTo" id="pickupTimeTo"
                                                    onChange="onPickupTimeToChangeNew();">
                                                <!--wszystkie opcje sa pobierane z apaczka zaleznie od kodu pocztowego i typu usługi-->
                                            </select></td>
                                    </table>
                                </td>
                            </tr>
                            </tbody>

                            <tbody>
                            <hr>
                            <td colspan="2"></td>
                            </hr>
                            <tr id="codRow">
                                <td>
                                    <label class="required">Pobranie:</label></td>
                                <td>
                                    <select name="cod" id="cod" onchange="handleDpd();">
                                        <option value="0" {if $cod == 0} selected="true"{/if}>nie</option>
                                        <option value="1" {if $cod == 1} selected="true"{/if}>tak</option>
                                    </select>
                                </td>
                            </tr>
                            </tbody>

                            <tbody id="codDetails" {if $cod==0} style="display: none"{/if}>
                            <tr>
                                <td>
                                    <label class="required">Kwota:</label></td>
                                <td>
                                    <div colspan="2"><input type="text" required="required" name="codAmount"
                                                            id="codAmount"
                                                            value="{$SHOP_cost|string_format:"%.2f"|escape:'htmlall':'UTF-8'}"
                                                            size="10">
                                </td>
                                <td><span class="unit">zł</span>
    </div>
    </td></tr>
    <tr>
        <td>
            <label class="required">Konto pobraniowe:</label></td>
        <td><input type="text" required="required" name="sender_account" id="sender_account" size="40" maxlength="40"
                   value="{$S_account|escape:'htmlall':'UTF-8'}">
        </td>
    </tr>
    </tbody>
    <tbody>

    <tr>
        <td colspan="2">
            <hr>
        </td>
    </tr>
    <tr id="insuranceLabel">
        <td>
            <label for="insurance" class=" required">
                {l s='Ubezpieczenie:' mod='apaczkashipping'}
            </label>
        </td>
        <td>
            <select name="insurance" id="insurance">
                <option value="0" {if $DEF_insurance == 'insurOff' || $DEF_insurance ==0} selected="true"{/if} >nie
                </option>
                <option value="1" {if $DEF_insurance == 'insurOn' || $DEF_insurance ==1} selected="true"{/if} >tak
                </option>
            </select>
        </td>
    </tr>
    </tbody>

    <tbody id="insuranceDetails"{if $DEF_insurance==0} style="display: none"{/if}>

    <!---------------------------------------------->
    <tr>
        <td>
            <label class=" required">Kwota:</label></td>
        <td>
            <div colspan="2"><input type="text" required="required" id="shipments_shipmentValue"
                                    name="shipments_shipmentValue"
                                    value="{$SHOP_cost|string_format:"%.2f"|escape:'htmlall':'UTF-8'}"
                                    maxlength="8" size="10">
        </td>
        <td>
            <span class="unit">zł</span>
            </div>
        </td>
    </tr>
    </tbody>
    <!---------------------------------------------->
    <tbody id="multiPackBody"> <!-- TU ZMIANA-->

    <tr id="multiPack">
        <td><p><label class=" required">Ilość paczek</label><span>Maksymalna ilość paczek to 20</span></p></td>
        <td>
            <input type="text" required="required" id="packageCount" name="packageCount"
                   value="{$DEF_numOdPacks|escape:'htmlall':'UTF-8'}" size="3" maxlength="2"
                   onchange="updateWeightMsg();"
                   size="8">

        </td>
    </tr>


    </td></tr>
    </tbody>    <!-- /TU ZMIANA-->
    <tbody>
    <tr>
        <td colspan="2">
        </td>
    </tr>
    </tbody>

    <tbody id="weight">
    <tr>
        <td colspan="2">
            <hr>
        </td>
    </tr>
    <tr>
        <td>
            <!----------------------------------------->
            <div id="messageOne"><label class="required">Waga paczki:</label></div>
            <div id="messageMany" style="display:none"><label class="required">Średnia waga paczki:</label></div>
        </td>
        <td>
            <div colspan="2">
                <input type="text" required="required" name="shipments_weight" id="shipments_weight" size="3"
                       maxlength="2" value="{$DEF_weight|escape:'htmlall':'UTF-8'}">
        </td>
        <td><span class="unit">kg</span>
            </div>
            <!----------------------------------------->

        </td>
    </tr>
    </tbody>

    <tbody id="dimensions">
    <tr>
        <td>
            <label class=" required">Wymiary paczki (dl/sz/wys):</label></td>
        <td>
            <table>
                <td>
                    <input type="text" required="required" name="shipments_dim1" id="shipments_dim1" size="3"
                           maxlength="3" value="{$DEF_dim1|escape:'htmlall':'UTF-8'}"></td>
                <td>[cm]/</td>
                <td><input type="text" required="required" name="shipments_dim2" id="shipments_dim2" size="3"
                           maxlength="3" value="{$DEF_dim2|escape:'htmlall':'UTF-8'}"></td>
                <td>[cm]/</td>
                <td><input type="text" required="required" name="shipments_dim3" id="shipments_dim3" size="3"
                           maxlength="3" value="{$DEF_dim3|escape:'htmlall':'UTF-8'}"></td>
                <td>[cm]</td>
            </table>
        </td>
    </tr>
    </tbody>

    </table>

    </td></tr></tbody>
    <tbody>

    <tr>
        <th colspan="3">
            <button type="button" name="notifications" id="notifications" onclick="changeVisibleNotifications();">Zmień
                ustawienia powiadomień
            </button>
        </th>

    </tr>
    </tbody>
    </table>
    <table name="setNofitications" id="setNofitications" style="width:45%; display:none">
        <tbody>
        <tr>
            <th style="width:30%"><label>Powiadomienia</label></th>
            <th style="width:20%"><label>dla ODBIORCY</label></th>
            <th style="width:20%"><label>dla NADAWCY</label></th>
        </tr>
        <tr>
            <td><label>Doręczenie </label></td>
            <td><input type="checkbox" id="cbo11"
                       value="{if $R_DEF_notifDelivered == '1' || $R_DEF_notifDelivered =='true'}1{else}0{/if}"
                       onchange="checkchange(id);"
                       name="cbo11" {if $R_DEF_notifDelivered == '1' ||  $R_DEF_notifDelivered == 'true'} checked{/if}>
            </td>

            <td><input type="checkbox" id="cbo12"
                       value="{if $S_DEF_notifDelivered == '1' || $S_DEF_notifDelivered=='true'}1{else}0{/if}"
                       onchange="checkchange(id);"
                       name="cbo12" {if $S_DEF_notifDelivered == '1' || $S_DEF_notifDelivered == 'true'} checked{/if}>
            </td>
        </tr>
        <tr>
            <td><label>Wyjątek </label></td>
            <td><input type="checkbox" id="cbo21"
                       value="{if $R_DEF_notifException == '1'||  $R_DEF_notifException=='true'}1{else}0{/if}"
                       onchange="checkchange(id);"
                       name="cbo21" {if $R_DEF_notifException == '1' || $R_DEF_notifException == 'true'} checked{/if}>
            </td>

            <td><input type="checkbox" id="cbo22"
                       value="{if $S_DEF_notifException == '1'|| $S_DEF_notifException=='true'}1{else}0{/if}"
                       onchange="checkchange(id);"
                       name="cbo22" {if $S_DEF_notifException == '1' || $S_DEF_notifException == 'true'} checked{/if}>
            </td>
        </tr>
        <tr>
            <td><label>Nowe </label></td>
            <td><input type="checkbox" id="cbo31"
                       value="{if $R_DEF_notifRegister == '1'|| $R_DEF_notifRegister=='true'}1{else}0{/if}"
                       onchange="checkchange(id);"
                       name="cbo31" {if $R_DEF_notifRegister == '1' || $R_DEF_notifRegister == 'true'} checked{/if}>
            </td>


            <td><input type="checkbox" id="cbo32"
                       value="{if $S_DEF_notifRegister == '1'|| $S_DEF_notifRegister=='true'}1{else}0{/if}"
                       onchange="checkchange(id);"
                       name="cbo32" {if $S_DEF_notifRegister == '1' || $S_DEF_notifRegister == 'true'} checked{/if}>
            </td>
        </tr>
        <tr>
            <td><label>Wydanie kurierowi </label></td>
            <td><input type="checkbox" id="cbo41"
                       value="{if $R_DEF_notifSent == '1' || $R_DEF_notifSent=='true'}1{else}0{/if}"
                       onchange="checkchange(id);"
                       name="cbo41" {if $R_DEF_notifSent == '1' || $R_DEF_notifSent == 'true'} checked{/if}></td>

            <td><input type="checkbox" id="cbo42"
                       value="{if $S_DEF_notifSent == '1' || $S_DEF_notifSent=='true'}1{else}0{/if}"
                       onchange="checkchange(id);"
                       name="cbo42" {if $S_DEF_notifSent == '1' || $S_DEF_notifSent == 'true'} checked{/if}></td>
        </tr>
        <div class="small"><label class="required">Required field</label></div>
        </tbody>
    </table>
    <br>
    <input type="submit" id="sendsubmit" name="sendsubmit" class="button" value="Zamów kuriera">
    </form>
    <input type="hidden" id="apaczka-parcel_locker_map_context" value="">
    </div>
    {literal}
    <script type="text/javascript">
        {/literal}
        var DEFserviceCode = '{$DEF_serviceCode|escape:'htmlall':'UTF-8'}';
        {literal}

        $(document).ready(function () {
            showOrHideFields();
            noStdChange('bigPack');
            noStdChange('noStd');
            changeCountry();
            handleDpd();
        });

        $('#orderPickupType').change(function () {
            if ($(this).val() !== 'COURIER')
                $('#orderPickupDetails').hide();
            else
                $('#orderPickupDetails').show();
        }).trigger('change');
        $('#insurance').change(function () {
            if ($(this).val() !== '1')
                $('#insuranceDetails').hide();
            else
                $('#insuranceDetails').show();
        }).trigger('change');
        $('#cod').change(function () {
            if ($(this).val() !== '1')
                $('#codDetails').hide();
            else
                $('#codDetails').show();
        }).trigger('change');

        //*************************************************************************** //tu ZMIANA
        function updateWeightMsg() {
            if (document.getElementById("packageCount").value === '1') {
                document.getElementById("messageOne").style.display = '';
                document.getElementById("messageMany").style.display = 'none';

            } else {

                document.getElementById("messageOne").style.display = 'none';
                document.getElementById("messageMany").style.display = '';
            }
        }

        //***************************************************************************
        //ZMIANY
        function inArray(needle, haystack) {
            var length = haystack.length;
            for (var i = 0; i < length; i++) {
                if (haystack[i] == needle)
                    return true;
            }
            return false;
        }

        //***************************************************************************
        function handleDpd() {
            if (document.getElementById("serviceCode").value === 'DPD_CLASSIC'
                && (document.getElementById("cod").value === '1'
                    || document.getElementById("shipments_shipmentValue").value <= 5000)
            ) {
                document.getElementById("insuranceLabel").style.display = "none";
                document.getElementById("insuranceDetails").style.display = "none";
                document.getElementById("insurance").value = "1";
            } else {
                document.getElementById("insuranceLabel").style.display = "";
                if ($("#insurance").val() === "1") {                               //document.getElementById("insurance").value === 1
                    document.getElementById("insuranceDetails").style.display = "";
                }
            }
        }

        //***************************************************************************
        function changeCountry() {

            if (document.getElementById('receiver_countryId').value === '0'
                || document.getElementById('receiver_countryId').value === 0) {   //Jakby ktos cos nagrzebal :)
                $("#serviceCode option[value='UPS_Z_STANDARD']").hide();
                $("#serviceCode option[value='UPS_Z_EX_SAV']").hide();
                $("#serviceCode option[value='DPD_CLASSIC_FOREIGN']").hide();
                $("#serviceCode option[value='TNT_Z']").hide();
                $("#serviceCode option[value='APACZKA_DE']").hide();

                $("#serviceCode option[value='UPS_K_EX_SAV']").show();
                $("#serviceCode option[value='UPS_K_STANDARD']").show();
                $("#serviceCode option[value='DHLSTD']").show();
                $("#serviceCode option[value='DHL12']").show();
                $("#serviceCode option[value='KEX_EXPRESS']").show();
                $("#serviceCode option[value='FEDEX']").show();
                $("#serviceCode option[value='DPD_CLASSIC']").show();
                $("#serviceCode option[value='POCZTA_POLSKA_E24']").show();
                $("#serviceCode option[value='INPOST']").show();
                $("#serviceCode option[value='PACZKOMAT']").show();

            } else {
                $("#serviceCode option[value='UPS_Z_STANDARD']").show();
                $("#serviceCode option[value='UPS_Z_EX_SAV']").show();
                $("#serviceCode option[value='DPD_CLASSIC_FOREIGN']").show();
                $("#serviceCode option[value='TNT_Z']").show();
                $("#serviceCode option[value='APACZKA_DE']").show();

                $("#serviceCode option[value='UPS_K_EX_SAV']").hide();
                $("#serviceCode option[value='UPS_K_STANDARD']").hide();
                $("#serviceCode option[value='DHLSTD']").hide();
                $("#serviceCode option[value='DHL12']").hide();
                $("#serviceCode option[value='KEX_EXPRESS']").hide();
                $("#serviceCode option[value='FEDEX']").hide();
                $("#serviceCode option[value='POCZTA_POLSKA_E24']").hide();
                $("#serviceCode option[value='DPD_CLASSIC']").hide();
                $("#serviceCode option[value='INPOST']").hide();
                $("#serviceCode option[value='PACZKOMAT']").hide();

            }
        }

        //***************************************************************************
        function showMultipack() {
            if (document.getElementById("shipmentTypeCode2").checked) {
                document.getElementById("multiPackBody").style.display = "";
            } else {
                document.getElementById("multiPackBody").style.display = "none";
                document.getElementById("packageCount").value = '1';
            }
        }

        //***************************************************************************
        function showOrHideFields() {
            var servCode = document.getElementById('serviceCode').value;

            //chowanie lub pokazywanie opcji DUZA_PACZKA zaleznie od tego czy przewoznik UPSowy
            if (
                servCode === 'UPS_K_STANDARD'
                || servCode === 'UPS_Z_STANDARD'
                || servCode === 'UPS_K_EX_SAV'
            ) {
                document.getElementById("bigPackLabel").style.display = "";
                document.getElementById('bigPack').checked = false;
                document.getElementById('bigPack').value = 0;
            } else {
                document.getElementById("bigPackLabel").style.display = "none";
                document.getElementById('bigPack').checked = false;
                document.getElementById('bigPack').value = 0;
            }
            if (servCode === 'DPD_CLASSIC'
                || servCode === 'PACZKOMAT') {
                document.getElementById("noStdField").style.display = "none";
                document.getElementById('bigPack').checked = false;
                document.getElementById('bigPack').value = 0;
                document.getElementById('noStd').checked = false;
                document.getElementById('noStd').value = 0;
            } else {
                document.getElementById("noStdField").style.display = "";
            }
            if(servCode === 'APACZKA_DE') {
                $('#cod').attr('disabled', true);
                $('#cod').trigger('change');
                $('#cod').val(0);
                $('#noStd').attr('disabled', true);
                $('#noStd').val(0);
                $('#codDetails').hide();
            } else {
                $('#cod').trigger('change');
                $('#cod').attr('disabled', false);
                $('#noStd').attr('disabled', false);
            }
            //chowanie lub pokazywanie opcji wyboru miedzy listem a paczka w przypadku niektorych przewoznikow.
            if (servCode === 'UPS_K_STANDARD'
                || servCode === 'UPS_Z_STANDARD'
                || servCode === 'FEDEX'
                || servCode === 'DPD_CLASSIC'
                || servCode === 'PACZKOMAT'
            ) {
                //chowa opcje listy i paczka oraz paczka standard/nie
                document.getElementById("shipmentTypeCode2").checked = true;
                document.getElementById("shipmentTypeCodeRow").style.display = "none";
                document.getElementById('bigPack').checked = false;
                document.getElementById('bigPack').value = 0;
                document.getElementById('noStd').checked = false;
                document.getElementById('noStd').value = 0;
                document.getElementById('noStdField').style.display = "none";
            } else {
                //pokazuje opcje listy.
                document.getElementById("shipmentTypeCodeRow").style.display = "";
                document.getElementById('bigPack').checked = false;
                document.getElementById('bigPack').value = 0;
                document.getElementById('noStd').checked = false;
                document.getElementById('noStd').value = 0;
            }
            //ZMIANY
            var multipackServices = ['UPS_K_STANDARD', 'UPS_Z_STANDARD', 'UPS_K_EX_SAV', 'UPS_Z_EX_SAV', 'DHLSTD', 'DHL12', 'KEX_EXPRESS'];
            if (inArray(servCode, multipackServices) && document.getElementById("shipmentTypeCode2").checked) {
                document.getElementById("multiPackBody").style.display = "";
            } else {
                document.getElementById("multiPackBody").style.display = "none";
                document.getElementById("packageCount").value = '1';
            }
        }

        //***************************************************************************
        function noStdChange(id) {
            (document.getElementById(id).checked === true) ? (document.getElementById(id).value = 1) : (document.getElementById(id).value = 0);

            if (document.getElementById('noStd').checked === true && document.getElementById('bigPack').checked === true) {
                if (id === 'noStd') {
                    document.getElementById('bigPack').checked = false;
                    document.getElementById('bigPack').value = 0;
                    document.getElementById('noStd').value = 1;
                } else {
                    document.getElementById('noStd').checked = false;
                    document.getElementById('noStd').value = 0;
                    document.getElementById('bigPack').value = 1;
                }

            }
        }

        //***************************************************************************
        function shipmentChange(id) {
            if (id === 'shipmentTypeCode1') {
                document.getElementById("dimensions").style.display = "none";
                document.getElementById("weight").style.display = "none";
                document.getElementById("noStdField").style.display = "none";

            }
            else {
                document.getElementById("dimensions").style.display = "";
                document.getElementById("weight").style.display = "";
                document.getElementById("noStdField").style.display = "";
            }
        }

        //***************************************************************************
        function checkchange(id) {
            if (document.getElementById(id).checked === true) {
                document.getElementById(id).value = true;
            } else {
                document.getElementById(id).value = false;
            }
        }

        //***************************************************************************	//GENERUJE FORMULARZ
        function generatePickupTime() {
            var selected = document.getElementById("pickupDate").value;
            var opt = '';
            var opt_timefrom = '';
            var opt_timeto = '';

            for (var i = 0; i <= 6; i++) {
                var date = getDate(i);
                opt = opt + "<option value=\"" + date + "\">" + date + "</option>";
            }

            for (var i = 9; i <= 15; i = i + 0.5) {
                if (i % 1 === 0) {
                    min = ":00";
                }
                else {
                    min = ":30";
                }
                var godz = parseInt(i, 10);
                if (godz < 10) {
                    godz = "0" + godz;
                }
                opt_timefrom = opt_timefrom + "<option value=\"" + godz + min + "\">" + godz + min + "</option>";
                godz = parseInt(godz, 10);
                godz = godz + 2;
                if (godz < 10) {
                    godz = "0" + godz;
                }
                opt_timeto = opt_timeto + "<option value=\"" + godz + min + "\">" + godz + min + "</option>";
            }

            $('#pickupDate').find('option').remove();
            $("#pickupDate").append(opt);
            document.getElementById("pickupDate").value = selected;

            $('#pickupTimeFrom').find('option').remove();
            $('#pickupTimeTo').find('option').remove();
            $("#pickupTimeFrom").append(opt_timefrom);
            $("#pickupTimeTo").append(opt_timeto);

            if (document.getElementById("pickupDate").selectedIndex === -1) {
                document.getElementById("pickupDate").selectedIndex = 0;
            }
        }

        //***************************************************************************	GENERUJE DATE DO FORMULARZA
        function getDate(day) {
            var today = new Date();
            today.setDate(today.getDate() + day);

            var dd = today.getDate();
            var mm = today.getMonth() + 1; //January is 0!
            var yyyy = today.getFullYear();

            if (dd < 10) {
                dd = '0' + dd;
            }
            if (mm < 10) {
                mm = '0' + mm;
            }
            today = yyyy + "-" + mm + "-" + dd;
            return today;
        }

        //***************************************************************************
        function refreshPickupTimeSelectsCourier(supplierCode) {	//Wywolywana przez refreshPickupTimeSelects
            generatePickupTime();					//GENERUJE FORMULARZ
            setPickupType(supplierCode);

            if (supplierCode === 'DHL') {
                checkPickupTimeFromJSON(supplierCode, 1, 2);
                checkPickupTimeFromJSON(supplierCode, 0, 2);
            }
            else if (supplierCode === 'DPD') {
                checkPickupTimeFromJSON(supplierCode, 1, 3);
                checkPickupTimeFromJSON(supplierCode, 0, 3);
            }
            else if (supplierCode === 'TNT') {
                checkPickupTimeFromJSON(supplierCode, 1, 1);
            }
            else {
                checkPickupTimeFromJSON(supplierCode);
            }
        }

        //***************************************************************************
        function setPickupType(supplierCode) {

            if (supplierCode === "DHL" || supplierCode === "DPD" || supplierCode === "INPOST" || supplierCode === "UPS" || supplierCode === "FEDEX") {
                enablePickupTypes();
            }
            else if (supplierCode === "KEX" || supplierCode === "TNT") {
                enableCourierPickupType();
            }

        }

        //***************************************************************************
        function refreshPickupTimeSelects() {			//Wywolywana po jakimkolwiek kliknieciu
            var service = document.getElementById('serviceCode').value;

            switch (service) {
                case "UPS_K_STANDARD":
                case "UPS_K_EX_SAV":
                case "UPS_Z_STANDARD":
                    refreshPickupTimeSelectsCourier("UPS");
                    break;
                case "DHLSTD":
                case "DHL12":
                    refreshPickupTimeSelectsCourier("DHL");
                    break;
                case "KEX_EXPRESS":
                    refreshPickupTimeSelectsCourier("KEX");
                    break;
                case "DPD_CLASSIC":
                    refreshPickupTimeSelectsCourier("DPD");
                    break;
                case "DPD_CLASSIC_FOREIGN":
                    refreshPickupTimeSelectsCourier("DPD");
                    break;
                case "FEDEX":
                    refreshPickupTimeSelectsCourier("FEDEX");
                    break;
                case "INPOST":
                    refreshPickupTimeSelectsCourier("INPOST");
                    break;
                case "PACZKOMAT":
                    refreshPickupTimeSelectsCourier("PACZKOMAT");
                    break;
            }

        }

        //******************************************************************************
        function linkFun1() {
            {/literal}
            document.getElementById('apaczka_orderForm').action = "index.php?controller=AdminOrders&id_order={$id_order|escape:'htmlall':'UTF-8'}&vieworder&addContact=1&token={$token|escape:'htmlall':'UTF-8'}";
            id = "apaczka_orderForm";{literal}
        }
        {/literal}
        var contacts = {$contacts|@json_encode:64|escape:'html':'UTF-8'|htmlspecialchars_decode:3};
        {literal}
        //******************************************************************************
        function nowyAdresNadawcy(id) {
            if (typeof document.getElementById('cont_id') !== 'undefined') {
                document.getElementById('sender_name').value = contacts[id - 1]['nazwa'];
                document.getElementById('sender_addressLine1').value = contacts[id - 1]['adres'];
                document.getElementById('sender_addressLine2').value = contacts[id - 1]['adres2'];
                document.getElementById('sender_postalCode').value = contacts[id - 1]['kod_pocztowy'];
                document.getElementById('sender_city').value = contacts[id - 1]['miasto'];
                document.getElementById('sender_countryID').value = parseInt(contacts[id - 1]['id_kraju']);
                document.getElementById('sender_contactName').value = contacts[id - 1]['osoba_kontaktowa'];
                document.getElementById('sender_phone').value = contacts[id - 1]['telefon'];
                document.getElementById('sender_email').value = contacts[id - 1]['email'];
                document.getElementById('sender_account').value = contacts[id - 1]['konto_pobraniowe'];

            }

        }

        //******************************************************************************
        function convertPostcodeToInteger(postcode) {
            var postcodeWithoutPause = "" + postcode.replace("-", "");
            return postcodeWithoutPause;
        }

        //***************************************************************************	--------
        function onPickupTimeFromChangeNew() {
            var idx1 = document.getElementById('pickupTimeFrom').selectedIndex;
            var idx2 = document.getElementById('pickupTimeTo').selectedIndex;
            if (idx1 > idx2) {
                document.getElementById("pickupTimeTo").selectedIndex = idx1;
            }
        }

        //***************************************************************************	--------
        function onPickupTimeToChangeNew() {
            var idx1 = document.getElementById("pickupTimeFrom").selectedIndex;
            var idx2 = document.getElementById("pickupTimeTo").selectedIndex;
            if (idx1 > idx2) {
                document.getElementById("pickupTimeFrom").selectedIndex = idx2;
            }
        }

        //***************************************************************************
        function disablePickupTypes() {
            var op = document.getElementById('orderPickupType').getElementsByTagName("option");
            for (var i = 0; i < op.length; i++) {
                op[i].disabled = true;
            }
        }

        //***************************************************************************
        function enableCourierPickupType() {
            var op = document.getElementById('orderPickupType').getElementsByTagName("option");
            for (var i = 0; i < op.length; i++) {
                // lowercase comparison for case-insensitivity
                if (op[i].value.toLowerCase() === "courier") {
                    op[i].disabled = false;
                }
            }
        }

        //***************************************************************************
        function enablePickupTypes() {
            //orderPickupType
            var op = document.getElementById('orderPickupType').getElementsByTagName("option");
            for (var i = 0; i < op.length; i++) {
                op[i].disabled = false;
            }
        }

        //***************************************************************************
        function removeColon(input) {
            var output = "" + input.replace(":", "");
            return output;
        }

        //******************************************************************************
        function checkPickupTimeFromJSON(supplierCode, isstatic, timeDiff) {

            isstatic = typeof isstatic !== 'undefined' ? isstatic : 1;
            timeDiff = typeof timeDiff !== 'undefined' ? timeDiff : 3;
            var postalcode = document.getElementById('sender_postalCode').value;
            postalcode = convertPostcodeToInteger(postalcode);

            var url = '//www.apaczka.pl/getPickupTime.php?callback=pickup&postalcode=' + postalcode + "&isstatic=" + isstatic;
            var opt = '';
            var opt_timefrom = '';  //
            var opt_timeto = '';
            var selected = document.getElementById("pickupDate").value;
            var daty_array = [];
            $.ajax({
                dataType: "jsonp",
                url: url,
                data: "a",
                success: function (json) {		//JESLI UDA SIE PRZESLAC TO WYWOLYWANA JEST TA FUNKCJA
                    var daty = json.daty;
                    //var olej =0;
                    for (var i in daty) {
                        var services = daty[i].services;
                        for (var j in services) {
                            if (services[j].service === supplierCode) {
                                opt = opt + "<option value=\"" + daty[i].data + "\">" + daty[i].data + "</option>";
                                daty_array.push(daty[i].data);
                                if (selected === '') {
                                    selected = daty[i].data;
                                }
                                if (daty[i].data === selected) {
                                    var timeFrom = services[j].timefrom;
                                    var timeTo = services[j].timeto;
                                    var from_h_tmp = parseInt(timeFrom.substring(0, 2));      //(godziny)
                                    var from_m_tmp = parseInt(removeColon(timeFrom.substring(2, 5)));               //(minuty)
                                    //zaokraglenie do gory
                                    var d = new Date();


                                    if (String(daty[i].data) === String(d.toISOString().substring(0, 10))) {   //Jesli chcemy wysylac w dniu dzisiejszym
                                        var hours_now = parseInt(d.getHours());
                                        var minutes_now = parseInt(d.getMinutes());

                                        if (hours_now > from_h_tmp || (hours_now === from_h_tmp && minutes_now > from_m_tmp)) {  //Jesli czas Proponowany jest mniejszy od obecnego.

                                            if (minutes_now < 30) {
                                                from_h_tmp = hours_now;   //Zaokraglamy do 30 po
                                                from_m_tmp = ":30";
                                            } else {
                                                from_h_tmp = hours_now + 1; //Zaokraglamy do nastepnej pelnej godziny
                                                from_m_tmp = ":00";
                                            }
                                        }
                                    }

                                    var to_tmp = parseInt(timeTo.substring(0, 2));
                                    var to_tmp2 = timeTo.substring(2, 5);
                                    var l;
                                    var min;
                                    //if()
                                    for (l = from_h_tmp + 0.5 * ((from_m_tmp === ":30") ? 1 : 0); l <= to_tmp - timeDiff; l = l + 0.5) {
                                        if (l % 1 === 0) {
                                            min = ":00";
                                        }
                                        else {
                                            min = ":30";
                                        }
                                        var h = parseInt(l, 10);
                                        if (h < 10) {
                                            h = "0" + h;

                                        }

                                        opt_timefrom = opt_timefrom + "<option value=\"" + h + min + "\">" + h + min + "</option>";
                                    }
                                    /*if (olej <= 0) {*/
                                    for (l = from_h_tmp + 0.5 * ((from_m_tmp === ":30") ? 1 : 0) + timeDiff; l <= to_tmp; l = l + 0.5) {
                                        var h = parseInt(l, 10);
                                        if (h < 10) {
                                            h = "0" + h;
                                        }
                                        if (l % 1 === 0) {
                                            min = ":00";
                                        }
                                        else {
                                            min = ":30";
                                        }

                                        opt_timeto = opt_timeto + "<option value=\"" + h + min + "\">" + h + min + "</option>";
                                    }
                                } else {

                                }
                            }
                        }
                    }
                    $('#pickupDate').find('option').remove();
                    $("#pickupDate").append(opt); //DATA
                    document.getElementById("pickupDate").value = selected;
                    if(opt_timefrom) {
                        $('#pickupTimeFrom').find('option').remove();
                    }
                    if(opt_timeto) {
                        $('#pickupTimeTo').find('option').remove();
                    }
                    if (supplierCode === "KEX") {
                        $("#pickupTimeFrom").append("<option value=\"08:00\">08:00</option>"); //NA SZTYWNO WBIJA OD 8 DO 16
                        $("#pickupTimeTo").append("<option value=\"16:00\">16:00</option>");
                    }
                    else {
                        $("#pickupTimeFrom").append(opt_timefrom);
                        $("#pickupTimeTo").append(opt_timeto);
                    }

                    if (daty_array.length > 0 && daty_array.indexOf(selected) === -1) {	//JESLI NIC NIE WYBRANO TO WYBIERA NAJBLIZSZA DATE
                        document.getElementById("pickupDate").selectedIndex = 0;
                        checkPickupTimeFromJSON(supplierCode);
                    }

                },
                error: function () {
                    console.log("Nie udało się pobrać godzin pickup'u" + url);
                }
            });
        }

        //******************************************************************************
        function changeVisibleNotifications() {
            if (document.getElementById("setNofitications").style.display === "table") {
                document.getElementById("setNofitications").style.display = "none";
            } else {
                document.getElementById("setNofitications").style.display = "table";
            }

        }

    </script>
    {/literal}
</fieldset>