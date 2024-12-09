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
<table class="table table-condensed table-bordered">
    <tr>
        <td>{l s='Firstname' mod='gdprpro'}</td>
        <td>{$customerData.customer.firstname}</td>
    </tr>
    <tr>
        <td>{l s='Lastname' mod='gdprpro'}</td>
        <td>{$customerData.customer.lastname}</td>
    </tr>
    <tr>
        <td>{l s='Email' mod='gdprpro'}</td>
        <td>{$customerData.customer.email}</td>
    </tr>
    <tr>
        <td>{l s='Newsletter' mod='gdprpro'}</td>
        <td>
            {if $customerData.customer.newsletter == 1}Subscribed{$customerData.customer.newsletter} {else} Not subscribed{/if}
        </td>
    </tr>
    <tr>
        <td>{l s='Newsletter registration IP' mod='gdprpro'}</td>
        <td>{$customerData.customer.ip_registration_newsletter}</td>
    </tr>
    <tr>
        <td>{l s='Newsletter registration date' mod='gdprpro'}</td>
        <td>{$customerData.customer.newsletter_date_add}</td>
    </tr>
    <tr>
        <td>{l s='Website' mod='gdprpro'}</td>
        <td>{$customerData.customer.website}</td>
    </tr>
    <tr>
        <td>{l s='Siret' mod='gdprpro'}</td>
        <td>{$customerData.customer.siret}</td>
    </tr>
    <tr>
        <td>{l s='APE' mod='gdprpro'}</td>
        <td>{$customerData.customer.ape}</td>
    </tr>
    <tr>
        <td>{l s='Last password change' mod='gdprpro'}</td>
        <td>{$customerData.customer.last_passwd_gen}</td>
    </tr>
    <tr>
        <td>{l s='Active' mod='gdprpro'}</td>
        <td>{$customerData.customer.active}</td>
    </tr>
    <tr>
        <td>{l s='Deleted' mod='gdprpro'}</td>
        <td>{$customerData.customer.deleted}</td>
    </tr>
    <tr>
        <td>{l s='Profile created' mod='gdprpro'}</td>
        <td>{$customerData.customer.date_add}</td>
    </tr>
    <tr>
        <td>{l s='Profile updated' mod='gdprpro'}</td>
        <td>{$customerData.customer.date_upd}</td>
    </tr>
    <tr>
        <td>{l s='Customer group' mod='gdprpro'}</td>
        <td>{$customerData.customer.group}</td>
    </tr>
    <tr>
        <td>{l s='Gender' mod='gdprpro'}</td>
        <td>{$customerData.customer.gender}</td>
    </tr>
    <tr>
        <td>{l s='Language' mod='gdprpro'}</td>
        <td>{$customerData.customer.language}</td>
    </tr>
</table>
<h2>{l s='Orders' mod='gdprpro'}</h2>

<table class="table table-condensed table-bordered">
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
    {if count($customerData.orders) == 0}
        <tr>
            <th colspan="4" class="text-center bg-info">
                <h4><i>{l s='No orders for this customer' mod='gdprpro'}</i></h4>
            </th>
        </tr>
    {/if}
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

<table class="table table-condensed table-bordered">
    <thead>
    <tr>
        <th>{l s='Alias' mod='gdprpro'}</th>
        <th>{l s='Name' mod='gdprpro'}</th>
        <th>{l s='Company' mod='gdprpro'}</th>
        <th>{l s='Address' mod='gdprpro'}</th>
        <th>{l s='Postcode' mod='gdprpro'}</th>
        <th>{l s='City' mod='gdprpro'}</th>
        <th>{l s='State' mod='gdprpro'}</th>
        <th>{l s='Country' mod='gdprpro'}</th>
        <th>{l s='Phone' mod='gdprpro'}</th>
        <th>{l s='Mobile' mod='gdprpro'}</th>
        <th>{l s='VAT number' mod='gdprpro'}</th>
        <th>{l s='DNI' mod='gdprpro'}</th>
    </tr>
    </thead>
    {if count($customerData.addresses) == 0}
        <tr>
            <th colspan="12" class="text-center bg-info">
                <h4><i>{l s='No addresses for this customer' mod='gdprpro'}</i></h4>
            </th>
        </tr>
    {/if}
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

<table class="table table-condensed table-bordered">
    <thead>
    <tr>
        <th>{l s='Page views' mod='gdprpro'}</th>
        <th>{l s='IP Address' mod='gdprpro'}</th>
        <th>{l s='Time spent on page' mod='gdprpro'}</th>
        <th>{l s='Date added' mod='gdprpro'}</th>
        <th>{l s='HTTP Referer' mod='gdprpro'}</th>
    </tr>
    </thead>
    {if count($customerData.connections) == 0}
        <tr>
            <th colspan="5" class="text-center bg-info">
                <h4><i>{l s='No connections for this customer' mod='gdprpro'}</i></h4>
            </th>
        </tr>
    {/if}
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


<h2>Activities</h2>

<table class="table table-condensed table-bordered">
    <thead>
    <tr>
        <th>Date</th>
        <th>Subject</th>
        <th>Data</th>
    </tr>
    </thead>
    {if count($activities) == 0}
        <tr>
            <th colspan="3" class="text-center bg-info">
                <h4><i>{l s='No activities for this customer' mod='gdprpro'}</i></h4>
            </th>
        </tr>
    {/if}
    {foreach $activities as $activity}
        <tr>
            <td>{$activity->date_add}</td>
            <td>{$activity->activity_subject}</td>
            <td>{$activity->activity_data}</td>
        </tr>
    {/foreach}
</table>