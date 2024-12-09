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
<tr>
    <td class="wrapper"
        style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
        <table border="0" cellpadding="0" cellspacing="0"
               style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
            <tr>
                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">

                    <h2>{l s='Customer data' mod='gdprpro'}</h2>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Firstname' mod='gdprpro'}:</b> {$customerData.customer.firstname}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Lastname' mod='gdprpro'}:</b> {$customerData.customer.lastname}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Email' mod='gdprpro'}:</b> {$customerData.customer.email}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Newsletter' mod='gdprpro'}:</b> {if $customerData.customer.newsletter == 1}{l s='Subscribed' mod='gdprpro'}{$customerData.customer.newsletter} {else} {l s='Not subscribed' mod='gdprpro'}{/if}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Newsletter registration IP' mod='gdprpro'}:</b> {$customerData.customer.ip_registration_newsletter}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Newsletter registration date' mod='gdprpro'}:</b> {$customerData.customer.newsletter_date_add}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Website' mod='gdprpro'}:</b> {$customerData.customer.website}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Siret' mod='gdprpro'}:</b> {$customerData.customer.siret}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='APE' mod='gdprpro'}:</b> {$customerData.customer.ape}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Last password change' mod='gdprpro'}:</b> {$customerData.customer.last_passwd_gen}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Active' mod='gdprpro'}:</b> {$customerData.customer.active}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Deleted' mod='gdprpro'}:</b> {$customerData.customer.deleted}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Profile created' mod='gdprpro'}:</b> {$customerData.customer.date_add}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Profile updated' mod='gdprpro'}:</b> {$customerData.customer.date_upd}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='Group' mod='gdprpro'}:</b> {$customerData.customer.group}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='gender' mod='gdprpro'}:</b> {$customerData.customer.gender}
                    </p>
                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0;">
                        <b>{l s='language' mod='gdprpro'}:</b> {$customerData.customer.language}
                    </p>
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
                            <th>{l s='State' mod='gdprpro'}</th>
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
                        {foreach $customerData.activities as $activity}
                            <tr>
                                <td>{$activity->date_add}</td>
                                <td>{$activity->activity_subject}</td>
                                <td>{$activity->activity_data}</td>
                            </tr>
                        {/foreach}
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
<!-- END MAIN CONTENT AREA -->