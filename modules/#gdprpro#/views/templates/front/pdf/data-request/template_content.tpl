{*
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 *}
<style>
    table.minimalistBlack {
        border: 1px solid #000000;
        width: 100%;
        text-align: left;
        border-collapse: collapse;
    }

    table.minimalistBlack td, table.minimalistBlack th {
        border: 1px solid #000000;
        padding: 5px 4px;
    }

    table.minimalistBlack tbody td {
        font-size: 13px;
    }

    table.minimalistBlack thead {
        background: #CFCFCF;
        background: -moz-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
        background: -webkit-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
        background: linear-gradient(to bottom, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
        border-bottom: 3px solid #000000;
    }

    table.minimalistBlack thead th {
        font-size: 15px;
        font-weight: bold;
        color: #000000;
        text-align: left;
    }

    table.minimalistBlack tfoot {
        font-size: 14px;
        font-weight: bold;
        color: #000000;
        border-top: 3px solid #000000;
    }

    table.minimalistBlack tfoot td {
        font-size: 14px;
    }
</style>
<h2>{l s='Customer data' mod='gdprpro'}</h2>

<table class="minimalistBlack">
    <tr>

        <td>
            {l s='Firstname' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.firstname}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Lastname' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.lastname}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Email' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.email}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Newsletter' mod='gdprpro'}
        </td>
        <td>
            {if $customerData.customer.newsletter == 1}
                {l s='Subscribed' mod='gdprpro'}
            {else}
                {l s='Not subscribed' mod='gdprpro'}
            {/if}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Registration IP' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.ip_registration_newsletter}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Newsletter registration date' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.newsletter_date_add}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Website' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.website}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Siret' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.siret}
        </td>
    </tr>
    <tr>
        <td>
            {l s='APE' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.ape}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Last password change' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.last_passwd_gen}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Active' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.active}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Deleted' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.deleted}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Profile created' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.date_add}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Profile updated' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.date_upd}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Group' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.group}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Gender' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.gender}
        </td>
    </tr>
    <tr>
        <td>
            {l s='Language' mod='gdprpro'}
        </td>
        <td>
            {$customerData.customer.language}
        </td>
    </tr>
</table>

<br pagebreak="true"/>
<h2>{l s='Orders' mod='gdprpro'}</h2>

<table class="minimalistBlack">
    <thead>
    <tr>
        <th>
            {l s='Payment method' mod='gdprpro'}
        </th>
        <th>
            {l s='Total paid' mod='gdprpro'}
        </th>
        <th>
            {l s='Delivery address' mod='gdprpro'}
        </th>
        <th>
            {l s='Invoice address' mod='gdprpro'}
        </th>
    </tr>
    </thead>
    {foreach $customerData.orders as $order}
        <tr>
            <td>
                {$order.payment}
            </td>
            <td>
                {$order.total_paid}
            </td>
            <td>
                {$order.address_delivery}
            </td>
            <td>
                {$order.address_invoice}
            </td>
        </tr>
    {/foreach}
</table>

<h2>{l s='Addresses' mod='gdprpro'}</h2>

<table class="minimalistBlack">
    <thead>
    <tr>
        <th>{l s='Alias' mod='gdprpro'}</th>
        <th>{l s='Name' mod='gdprpro'}</th>
        <th>{l s='Company' mod='gdprpro'}</th>
        <th>{l s='Address' mod='gdprpro'}</th>
        <th>{l s='Postcode' mod='gdprpro'}</th>
        <th>{l s='City' mod='gdprpro'}</th>
        <th>{l s='Country' mod='gdprpro'}</th>
        <th>{l s='Phone' mod='gdprpro'}</th>
        <th>{l s='Mobile' mod='gdprpro'}</th>
        <th>{l s='Vat number' mod='gdprpro'}</th>
        <th>{l s='DNI' mod='gdprpro'}</th>
    </tr>
    </thead>
    {foreach $customerData.addresses as $address}
        <tr>
            <td>{$address.alias}</td>
            <td>{$address.firstname} {$address.lastname}</td>
            <td>{$address.company}</td>
            <td>{$address.address1} {$address.address2}</td>
            <td>{$address.postcode}</td>
            <td>{$address.city}</td>
            <td>{$address.country}</td>
            <td>{$address.phone}</td>
            <td>{$address.phone_mobile}</td>
            <td>{$address.vat_number}</td>
            <td>{$address.dni}</td>
        </tr>
    {/foreach}
</table>

<h2>{l s='Connections' mod='gdprpro'}</h2>

<table class="minimalistBlack">
    <thead>
    <tr>
        <th>{l s='Page views' mod='gdprpro'}</th>
        <th>{l s='IP Address' mod='gdprpro'}</th>
        <th>{l s='Time spent on page' mod='gdprpro'}</th>
        <th>{l s='Date added' mod='gdprpro'}</th>
        <th>{l s='HTTP Referer' mod='gdprpro'}</th>
    </tr>
    </thead>
    {foreach $customerData.connections as $connection}
        <tr>
            <td>{$connection.pages}</td>
            <td>{$connection.ipaddress}</td>
            <td>{$connection.time}</td>
            <td>{$connection.date_add}</td>
            <td>{$connection.http_referer}</td>
        </tr>
    {/foreach}
</table>

<h2>{l s='Activities' mod='gdprpro'}</h2>

<table class="minimalistBlack">
    <thead>
    <tr>
        <th>{l s='Date' mod='gdprpro'}</th>
        <th>{l s='Subject' mod='gdprpro'}</th>
        <th>{l s='Data' mod='gdprpro'}</th>
    </tr>
    </thead>
    {if count($customerData.activities) == 0}
        <tr>
            <th colspan="3" class="text-center bg-info">
                <h4><i>{l s='No activities for this customer' mod='gdprpro'}</i></h4>
            </th>
        </tr>
    {/if}
    {foreach $customerData.activities as $activity}
        <tr>
            <td>{$activity->date_add}</td>
            <td>{$activity->activity_subject}</td>
            <td>{$activity->activity_data}</td>
        </tr>
    {/foreach}
</table>