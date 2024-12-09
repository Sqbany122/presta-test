<div id="opc_new_account">
    <div id="opc_new_account-overlay" class="overlay-opc" style="display: none;"></div>
    <h1 class="page-heading"><span class="heading-counter heading-counter-1">1</span>{l s='Account' mod='bestkit_opc'}</h1>
    <form action="{$link->getPageLink('authentication', true, NULL, "back=order-opc")|escape:false}" method="post" id="login_form" class="box box-opc">
        <fieldset>
            <h2 class="page-subheading">{l s='Already registered?' mod='bestkit_opc'}</h2>
            <p><a href="#" id="openLoginFormBlock">&raquo; {l s='Click here' mod='bestkit_opc'}</a></p>
            <div id="login_form_content">
                <!-- Error return block -->
                <div id="opc_login_errors" class="error" style="display:none;"></div>
                <!-- END Error return block -->
                <div class="form-group">
                    <label for="login_email">{l s='E-mail address' mod='bestkit_opc'}</label>
                    <input type="email" id="login_email" name="email" class="form-control" />
                </div>
                <div class="form-group">
                    <label for="login_passwd">{l s='Password' mod='bestkit_opc'}</label>
                    <input type="password" id="login_passwd" name="login_passwd" class="form-control" />
                </div>
                <p class="form-group">
                    <a href="{$link->getPageLink('password', true)|escape:false}" class="lost_password">{l s='Forgot your password?' mod='bestkit_opc'}</a>
                </p>
                <p class="submit">
                    {if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
                    <button class="btn btn-default button button-medium" id="SubmitLogin" name="SubmitLogin"><span><i class="icon-lock left"></i>{l s='Sign in' mod='bestkit_opc'}</span></button>
                </p>
            </div>
        </fieldset>
    </form>
    <form action="javascript:;" method="post" id="new_account_form" class="box box-opc" autocomplete="on" autofill="on">
        <fieldset>
            <h2 id="new_account_title" class="page-subheading">{l s='New Customer' mod='bestkit_opc'}</h2>
            <div id="opc_account_choice">
                <div class="item">
                    <h3 class="page-subheading">{l s='Instant Checkout' mod='bestkit_opc'}</h3>
                    <p class="submit">
                        <button class="btn btn-default button button-small exclusive" id="opc_guestCheckout"><span>{l s='Checkout as guest' mod='bestkit_opc'}</span></button>
                    </p>
                </div>

                <div class="item">
                    <h3 class="page-subheading">{l s='Create your account today and enjoy:' mod='bestkit_opc'}</h3>
                    <div class="form-group">
                        <ul class="bullet">
                            <li>{l s='Personalized and secure access' mod='bestkit_opc'}</li>
                            <li>{l s='Fast and easy check out' mod='bestkit_opc'}</li>
                            <li>{l s='Separate billing and shipping addresses' mod='bestkit_opc'}</li>
                        </ul>
                    </div>
                    <p class="submit">
                        <button class="btn btn-default button button-small exclusive" id="opc_createAccount"><span><i class="icon-user left"></i>{l s='Create an account' mod='bestkit_opc'}</span></button>
                    </p>
                </div>
            </div>
            <div id="opc_account_form">
                {$HOOK_CREATE_ACCOUNT_TOP|escape:false}
                <!-- Error return block -->
                <div id="opc_account_errors" class="alert alert-danger" style="display:none;"></div>
                <!-- END Error return block -->
                <!-- Account -->
                <input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
                <input type="hidden" id="opc_id_customer" name="opc_id_customer" value="{if isset($guestInformations) && $guestInformations.id_customer}{$guestInformations.id_customer}{else}0{/if}" />
                <input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
                <input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />

				{foreach from=$fields_customer key=k item=field_customer}
					{if $field_customer.name == 'email'}
						<div class="form-group required{if !$field_customer.active} hidden{/if}">
							<label for="email">{l s='Email' mod='bestkit_opc'} <sup>*</sup></label>
							<input type="email" class="form-control" id="email" name="email" value="{if isset($guestInformations) && $guestInformations.email}{$guestInformations.email}{elseif !$field_customer.active}{if $field_customer.default_value}{$field_customer.default_value}{else}{$smarty.now}@guest.com{/if}{/if}" />
						</div>
					{/if}
					
					{if $field_customer.name == 'passwd'}
						<div class="form-group required password is_customer_param{if !$field_customer.active} hidden{/if}">
							<label for="passwd">{l s='Password' mod='bestkit_opc'} <sup>*</sup></label>
							<input type="password" class="form-control" name="passwd" id="passwd" {if !$field_customer.active} value="{$smarty.now}"{/if} />
							<span class="form_info">{l s='(5 characters min.)' mod='bestkit_opc'}</span>
						</div>
					{/if}
					
					{if $field_customer.name == 'id_gender'}
						<div class="cleafix gender-line{if !$field_customer.active} hidden{/if}">
							<label>{l s='Gender' mod='bestkit_opc'}</label>
							<br>
							{foreach from=$genders key=k item=gender}
								<div class="radio-inline">
									<label for="id_gender{$gender->id_gender|escape:false}" class="top">
										<input type="radio" name="id_gender" id="id_gender{$gender->id_gender}" value="{$gender->id_gender}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id_gender}checked="checked"{/if} />
										{$gender->name|escape:false}
									</label>
								</div>
							{/foreach}
						</div>
					{/if}
					
					{if $field_customer.name == 'firstname'}
						<div class="form-group required{if !$field_customer.active} hidden{/if}">
							<label for="firstname">{l s='Firstname' mod='bestkit_opc'} <sup>*</sup></label>
							<input type="text" class="form-control" id="customer_firstname" name="customer_firstname" onblur="$('#firstname').val($(this).val());" value="{if isset($guestInformations) && $guestInformations.customer_firstname}{$guestInformations.customer_firstname}{elseif !$field_customer.active}{if $field_customer.default_value}{$field_customer.default_value}{else}Guest{/if}{/if}" />
						</div>
					{/if}
					
					{if $field_customer.name == 'lastname'}
						<div class="form-group required{if !$field_customer.active} hidden{/if}">
							<label for="lastname">{l s='Lastname' mod='bestkit_opc'} <sup>*</sup></label>
							<input type="text" class="form-control" id="customer_lastname" name="customer_lastname" onblur="$('#lastname').val($(this).val());" value="{if isset($guestInformations) && $guestInformations.customer_lastname}{$guestInformations.customer_lastname}{elseif !$field_customer.active}{if $field_customer.default_value}{$field_customer.default_value}{else}Guest{/if}{/if}" />
						</div>
					{/if}
					
					{if $field_customer.name == 'birthday'}
						<div class="form-group date-select{if !$field_customer.active} hidden{/if}">
							<label>{l s='Birthday' mod='bestkit_opc'}</label>
							<div class="row">
								<div class="col-xs-3">
									<select id="days" name="days" class="form-control">
										<option value="">-</option>
										{foreach from=$days item=day}
											<option value="{$day|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_day == $day)} selected="selected"{/if}>{$day|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
										{/foreach}
									</select>
									{*
										{l s='January' mod='bestkit_opc'}
										{l s='February' mod='bestkit_opc'}
										{l s='March' mod='bestkit_opc'}
										{l s='April' mod='bestkit_opc'}
										{l s='May' mod='bestkit_opc'}
										{l s='June' mod='bestkit_opc'}
										{l s='July' mod='bestkit_opc'}
										{l s='August' mod='bestkit_opc'}
										{l s='September' mod='bestkit_opc'}
										{l s='October' mod='bestkit_opc'}
										{l s='November' mod='bestkit_opc'}
										{l s='December' mod='bestkit_opc'}
									*}
								</div>
								<div class="col-xs-4">
									<select id="months" name="months" class="form-control">
										<option value="">-</option>
										{foreach from=$months key=k item=month}
											<option value="{$k|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_month == $k)} selected="selected"{/if}>{l s=$month mod='bestkit_opc'}&nbsp;</option>
										{/foreach}
									</select>
								</div>
								<div class="col-xs-4">
									<select id="years" name="years" class="form-control">
										<option value="">-</option>
										{foreach from=$years item=year}
											<option value="{$year|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_year == $year)} selected="selected"{/if}>{$year|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
										{/foreach}
									</select>
								</div>
							</div>
						</div>
					{/if}
					
					{if $field_customer.name == 'newsletter'}
						<div class="checkbox{if !$field_customer.active} hidden{/if}">
							<label for="newsletter">
								<input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($guestInformations) && $guestInformations.newsletter}checked="checked"{/if} />
								{l s='Sign up for our newsletter' mod='bestkit_opc'}
							</label>
						</div>
					{/if}
						
					{if $field_customer.name == 'optin'}
						<div class="checkbox{if !$field_customer.active} hidden{/if}">
							<label for="optin">
								<input type="checkbox"name="optin" id="optin" value="1" {if isset($guestInformations) && $guestInformations.optin}checked="checked"{/if} />
								{l s='Receive special offers from our partners' mod='bestkit_opc'}
							</label>
						</div>
					{/if}
						
					{if $field_customer.name == 'website'}
						<div class="form-group required{if !$field_customer.active} hidden{/if}">
							<label for="customer_website">{l s='Website' mod='bestkit_opc'} <sup>*</sup></label>
							<input type="text" class="form-control" id="customer_website" name="website" value="" />
						</div>
					{/if}
						
					{if $field_customer.name == 'company'}
						<div class="form-group{if !$field_customer.active} hidden{/if}">
							<label for="company">{l s='Company' mod='bestkit_opc'} </label>
							<input type="text" class="form-control" id="customer_company" name="customer_company" value="{if isset($guestInformations) && $guestInformations.company}{$guestInformations.company}{/if}" />
						</div>
					{/if}
						
					{if $field_customer.name == 'ape'}
						<div class="form-group{if !$field_customer.active} hidden{/if}">
							<label for="ape">{l s='APE' mod='bestkit_opc'} </label>
							<input type="text" class="form-control" id="ape" name="ape" value="" />
						</div>
					{/if}
						
					{if $field_customer.name == 'siret'}
						<div class="form-group{if !$field_customer.active} hidden{/if}">
							<label for="siret">{l s='Siret' mod='bestkit_opc'} </label>
							<input type="text" class="form-control" id="siret" name="siret" value="" />
						</div>
					{/if}
					
				{/foreach}

                {if $customer_groups && count($customer_groups)}
                    <div class="form-group">
                        <label for="customer_groups">{l s='Customer group' mod='bestkit_opc'} </label>
                        <select multiple="multiple" name="customer_groups[]" class="form-control" >
                        {foreach $customer_groups as $customer_group}
                            <option value="{$customer_group->id|intval}">{$customer_group->name}</option>
                        {/foreach}
                        </select>
                    </div>
                {/if}

				
				
                <div  id="opc_delivery_address" class="is_customer_param">
                    <h3 class="page-subheading">{l s='Delivery address' mod='bestkit_opc'}</h3>
					
					{foreach from=$fields_delivery key=k item=field_delivery}
						{if $field_delivery.name == 'firstname'}
							<div class="form-group required{if !$field_delivery.active} hidden{/if}">
								<label for="firstname">{l s='Firstname' mod='bestkit_opc'} <sup>*</sup></label>
								<input type="text" class="form-control" id="firstname" name="firstname" value="{if isset($guestInformations) && $guestInformations.firstname}{$guestInformations.firstname}{elseif !$field_delivery.active}{if $field_delivery.default_value}{$field_delivery.default_value}{else}Guest{/if}{/if}" />
							</div>
						{/if}
						
						{if $field_delivery.name == 'lastname'}
							<div class="form-group required{if !$field_delivery.active} hidden{/if}">
								<label for="lastname">{l s='Lastname' mod='bestkit_opc'} <sup>*</sup></label>
								<input type="text" class="form-control" id="lastname" name="lastname" value="{if isset($guestInformations) && $guestInformations.lastname}{$guestInformations.lastname}{elseif !$field_delivery.active}{if $field_delivery.default_value}{$field_delivery.default_value}{else}Guest{/if}{/if}" />
							</div>
						{/if}
						
						{if $field_delivery.name == 'company'}
							<div class="form-group required{if !$field_delivery.active} hidden{/if}">
								<label for="delivery_company">{l s='Company' mod='bestkit_opc'} </label>
								<input type="text" class="form-control" id="delivery_company" name="delivery_company" value="" />
							</div>
						{/if}
						
						{if $field_delivery.name == 'address1'}
							<div class="form-group required{if !$field_delivery.active} hidden{/if}">
								<label for="address1">{l s='Address' mod='bestkit_opc'} <sup>*</sup></label>
								<input type="text" class="form-control" name="address1" id="address1" value="{if isset($guestInformations) && $guestInformations.address1}{$guestInformations.address1}{elseif !$field_delivery.active}{if $field_delivery.default_value}{$field_delivery.default_value}{else}{l s='My address' mod='bestkit_opc'}{/if}{/if}" />
							</div>
						{/if}
						
						{if $field_delivery.name == 'address2'}
							<div class="form-group is_customer_param{if !$field_delivery.active} hidden{/if}">
								<label for="address2">{l s='Address (Line 2)' mod='bestkit_opc'}</label>
								<input type="text" class="form-control" name="address2" id="address2" value="" />
							</div>
						{/if}
						
						{if $field_delivery.name == 'postcode'}
							<div class="form-group required{if !$field_delivery.active} hidden{/if}">
								<label for="postcode">{l s='Zip / Postcode' mod='bestkit_opc'} <sup>*</sup></label>
								<input type="text" class="form-control" name="postcode" id="postcode" value="{if isset($guestInformations) && $guestInformations.postcode}{$guestInformations.postcode}{elseif !$field_delivery.active}{if $field_delivery.default_value}{$field_delivery.default_value}{else}{l s='220095' mod='bestkit_opc'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
							</div>
						{/if}
						
						{if $field_delivery.name == 'city'}
							<div class="form-group required{if !$field_delivery.active} hidden{/if}">
								<label for="city">{l s='City' mod='bestkit_opc'} <sup>*</sup></label>
								<input type="text" class="form-control" name="city" id="city" value="{if isset($guestInformations) && $guestInformations.city}{$guestInformations.city}{elseif !$field_delivery.active}{if $field_delivery.default_value}{$field_delivery.default_value}{else}City{/if}{/if}" />
							</div>
						{/if}
						
						{if $field_delivery.name == 'vat_number'}
							<div id="vat_number_block{if !$field_delivery.active} hidden{/if}" style="display:none;">
								<div class="form-group">
									<label for="vat_number">{l s='Vat number' mod='bestkit_opc'}</label>
									<input type="text" class="form-control" name="vat_number" id="vat_number" value="{if isset($guestInformations) && $guestInformations.vat_number}{$guestInformations.vat_number}{/if}" />
								</div>
							</div>
						{/if}
						
						{if $field_delivery.name == 'id_country'}
							{if $field_delivery.active}
								<div class="form-group select required">
									<label for="id_country">{l s='Country' mod='bestkit_opc'} <sup>*</sup></label>
									<select name="id_country" id="id_country" class="form-control">
										<option value="">-</option>
										{foreach from=$countries item=v}
										<option value="{$v.id_country}" {if (isset($guestInformations) AND $guestInformations.id_country == $v.id_country) OR (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
										{/foreach}
									</select>
								</div>
							{else}
								<input type="hidden" name="id_country" value="1" />
							{/if}
						{/if}
						
						{if $field_delivery.name == 'id_state'}
							<div class="form-group select required id_state" style="display:none;">
								<label for="id_state">{l s='State' mod='bestkit_opc'} <sup>*</sup></label>
								<select name="id_state" id="id_state" class="form-control">
									<option value="">-</option>
								</select>
							</div>
						{/if}
						
						{if $field_delivery.name == 'dni'}
							<div class="required form-group dni">
								<label for="dni">{l s='DNI' mod='bestkit_opc'} <sup>*</sup></label>
								<input class="form-control" data-validate="{$address_validation.$field_name.validate}" type="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{else}{if isset($address->dni)}{$address->dni|escape:'html':'UTF-8'}{/if}{/if}" />
								<span class="form_info">{l s='DNI / NIF / NIE' mod='bestkit_opc'}</span>
							</div>
						{/if}

                        {if $field_delivery.name == 'other'}
                            <div class="form-group textarea is_customer_param{if !$field_delivery.active} hidden{/if}">
                                <label for="other">{l s='Other' mod='bestkit_opc'} </label>
                                <textarea name="other" id="other" cols="36" rows="3" class="form-control"></textarea>
                            </div>
                        {/if}

                        {if $field_delivery.name == 'phone'}
                            <div class="form-group required {if !$field_delivery.active} hidden{/if}">
                                <label for="phone">{l s='Phone' mod='bestkit_opc'} <sup>*</sup></label>
                                <input type="tel" class="form-control" name="phone" id="phone" value="{if isset($guestInformations) && $guestInformations.phone}{$guestInformations.phone}{elseif !$field_delivery.active}{if $field_delivery.default_value}{$field_delivery.default_value}{else}123456789{/if}{/if}" />
                            </div>
                        {/if}

                        {if $field_delivery.name == 'phone_mobile'}
                            <div class="form-group {if !$field_delivery.active} hidden{/if}">
                                <label for="phone">{l s='Phone mobile' mod='bestkit_opc'} <sup>*</sup></label>
                                <input type="tel" class="form-control" name="phone_mobile" id="phone_mobile" value="{if isset($guestInformations) && $guestInformations.phone}{$guestInformations.phone}{elseif !$field_delivery.active}{if $field_delivery.default_value}{$field_delivery.default_value}{else}123456789{/if}{/if}" />
                            </div>
                        {/if}

					{/foreach}

                    <input type="hidden" name="alias" id="alias" value="{l s='My address' mod='bestkit_opc'}" />
                    {if $opcModuleObj->getConfig('billing')}
                        <div class="checkbox is_customer_param">
                            <label for="invoice_address">
                                <input type="checkbox" name="invoice_address" id="invoice_address" />
                                <b>{l s='Please use another address for invoice' mod='bestkit_opc'}</b>
                            </label>
                        </div>
                    {/if}
                </div>

            {if $opcModuleObj->getConfig('billing')}
                <div id="opc_invoice_address" class="is_customer_param">
                    <h3 class="page-subheading">{l s='Invoice address' mod='bestkit_opc'}</h3>

                    {foreach from=$fields_invoice key=k item=field_invoice}
                        {if $field_invoice.name == 'firstname'}
                            <div class="form-group required{if !$field_invoice.active} hidden{/if}">
                                <label for="firstname_invoice">{l s='Firstname' mod='bestkit_opc'} <sup>*</sup></label>
                                <input type="text" class="form-control" id="firstname_invoice" name="firstname_invoice" value="{if isset($guestInformations) && $guestInformations.firstname}{$guestInformations.firstname}{elseif !$field_invoice.active}{if $field_invoice.default_value}{$field_invoice.default_value}{else}Guest{/if}{/if}" />
                            </div>
                        {/if}

                        {if $field_invoice.name == 'lastname'}
                            <div class="form-group required{if !$field_invoice.active} hidden{/if}">
                                <label for="lastname_invoice">{l s='Lastname' mod='bestkit_opc'} <sup>*</sup></label>
                                <input type="text" class="form-control" id="lastname_invoice" name="lastname_invoice" value="{if isset($guestInformations) && $guestInformations.lastname}{$guestInformations.lastname}{elseif !$field_invoice.active}{if $field_invoice.default_value}{$field_invoice.default_value}{else}Guest{/if}{/if}" />
                            </div>
                        {/if}

                        {if $field_invoice.name == 'company'}
                            <div class="form-group required{if !$field_invoice.active} hidden{/if}">
                                <label for="company_invoice">{l s='Company' mod='bestkit_opc'} </label>
                                <input type="text" class="form-control" id="company_invoice" name="company_invoice" value="" />
                            </div>
                        {/if}

                        {if $field_invoice.name == 'vat_number'}
                            <div id="vat_number_block_invoice" class="is_customer_param{if !$field_invoice.active} hidden{/if}" style="display:none;">
                                <div class="form-group">
                                    <label for="vat_number_invoice">{l s='Vat number' mod='bestkit_opc'} </label>
                                    <input type="text" class="form-control" id="vat_number_invoice" name="vat_number_invoice" value="" />
                                </div>
                            </div>
                        {/if}

                        {if $field_invoice.name == 'dni'}
                            <div class="form-group dni_invoice{if !$field_invoice.active} hidden{/if}">
                                <label for="dni">{l s='DNI' mod='bestkit_opc'} </label>
                                <input type="text" class="form-control" name="dni_invoice" id="dni_invoice" value="{if isset($guestInformations) && $guestInformations.dni}{$guestInformations.dni|escape:false}{/if}" />
                                <span class="form_info">{l s='DNI / NIF / NIE' mod='bestkit_opc'}</span>
                            </div>
                        {/if}

                        {if $field_invoice.name == 'address1'}
                            <div class="form-group required{if !$field_invoice.active} hidden{/if}">
                                <label for="address1_invoice">{l s='Address' mod='bestkit_opc'} <sup>*</sup></label>
                                <input type="text" class="form-control" name="address1_invoice" id="address1_invoice" value="{if !$field_invoice.active}{if $field_invoice.default_value}{$field_invoice.default_value}{else}{l s='Address' mod='bestkit_opc'}{/if}{/if}" />
                            </div>
                        {/if}

                        {if $field_invoice.name == 'address2'}
                            <div class="form-group is_customer_param{if !$field_invoice.active} hidden{/if}">
                                <label for="address2_invoice">{l s='Address (Line 2)' mod='bestkit_opc'}</label>
                                <input type="text" class="form-control" name="address2_invoice" id="address2_invoice" value="" />
                            </div>
                        {/if}

                        {if $field_invoice.name == 'postcode'}
                            <div class="form-group required postcode{if !$field_invoice.active} hidden{/if}">
                                <label for="postcode_invoice">{l s='Zip / Postal code' mod='bestkit_opc'}<sup>*</sup></label>
                                <input type="text" class="form-control" name="postcode_invoice" id="postcode_invoice" value="{if !$field_invoice.active}{if $field_invoice.default_value}{$field_invoice.default_value}{else}{l s='220095' mod='bestkit_opc'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
                            </div>
                        {/if}

                        {if $field_invoice.name == 'city'}
                            <div class="form-group required">
                                <label for="city_invoice">{l s='City' mod='bestkit_opc'} <sup>*</sup></label>
                                <input type="text" class="form-control" name="city_invoice" id="city_invoice" value="" />
                            </div>
                        {/if}

                        {if $field_invoice.name == 'id_country'}
                            <div class="form-group required select">
                                <label for="id_country_invoice">{l s='Country' mod='bestkit_opc'} <sup>*</sup></label>
                                <select name="id_country_invoice" id="id_country_invoice" class="form-control">
                                    <option value="">-</option>
                                    {foreach from=$countries item=v}
                                        <option value="{$v.id_country}" {if ($sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        {/if}

                        {if $field_invoice.name == 'id_state'}
                            <div class="form-group required id_state_invoice select" style="display:none;">
                                <label for="id_state_invoice">{l s='State' mod='bestkit_opc'} <sup>*</sup></label>
                                <select name="id_state_invoice" id="id_state_invoice" class="form-control">
                                    <option value="">-</option>
                                </select>
                            </div>
                        {/if}

                        {if $field_invoice.name == 'phone'}
                            <div class="form-group required{if !$field_invoice.active} hidden{/if}">
                                <label for="phone_invoice">{l s='Phone' mod='bestkit_opc'} <sup>*</sup></label>
                                <input type="tel" class="form-control" name="phone_invoice" id="phone_invoice" value="{if $field_invoice.default_value}{$field_invoice.default_value}{/if}" />
                            </div>
                        {/if}

                        {if $field_invoice.name == 'phone_mobile'}
                            <div class="form-group required{if !$field_invoice.active} hidden{/if}">
                                <label for="phone_mobile_invoice">{l s='Phone mobile' mod='bestkit_opc'} <sup>*</sup></label>
                                <input type="tel" class="form-control" name="phone_mobile_invoice" id="phone_mobile_invoice" value="{if $field_invoice.default_value}{$field_invoice.default_value}{/if}" />
                            </div>
                        {/if}

                        {if $field_invoice.name == 'other'}
                            <div class="form-group textarea is_customer_param{if !$field_invoice.active} hidden{/if}">
                                <label for="other_invoice">{l s='Other' mod='bestkit_opc'}</label>
                                <textarea name="other_invoice" id="other_invoice" cols="26" rows="3" class="form-control">{if $field_invoice.default_value}{$field_invoice.default_value}{/if}</textarea>
                            </div>
                        {/if}
                    {/foreach}
                    <input type="hidden" name="alias_invoice" id="alias_invoice" value="{l s='My Invoice address' mod='bestkit_opc'}" />
                </div>
            {/if}
                {$HOOK_CREATE_ACCOUNT_FORM|escape:false}
                <p class="submit">
                    <button class="btn btn-default button button-medium" id="opc_back_to_create_or_guest"><span><i class="icon-chevron-sign-left left"></i>{l s='Return to choise' mod='bestkit_opc'}</span></button>
                    <button class="btn btn-default button button-medium" name="submitAccount" id="submitAccount"><span><i class="icon-save left"></i>{l s='Save' mod='bestkit_opc'}</span></button>
                </p>
                <div style="display:none;" id="opc_account_saved">
                    <p class="alert alert-success">{l s='Account informations saved successfully' mod='bestkit_opc'}</p>
                </div>
                <!-- END Account -->
            </div>
        </fieldset>
    </form>
</div>

{strip}
    {if isset($smarty.post.id_state) && $smarty.post.id_state}
        {addJsDef idSelectedState=$smarty.post.id_state|intval}
    {else if isset($address->id_state) && $address->id_state}
        {addJsDef idSelectedState=$address->id_state|intval}
    {else}
        {addJsDef idSelectedState=false}
    {/if}
    {if isset($smarty.post.id_country) && $smarty.post.id_country}
        {addJsDef idSelectedCountry=$smarty.post.id_country|intval}
    {else if isset($address->id_country) && $address->id_country}
        {addJsDef idSelectedCountry=$address->id_country|intval}
    {else}
        {addJsDef idSelectedCountry=false}
    {/if}
    {if isset($countries)}
        {addJsDef countries=$countries}
    {/if}
    {if isset($vatnumber_ajax_call) && $vatnumber_ajax_call}
        {addJsDef vatnumber_ajax_call=$vatnumber_ajax_call}
    {/if}
{/strip}