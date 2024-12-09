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
<div class="well" id="admin-gdpr-module-selector">
    <div class="btn-group" role="group">
        <a class="btn btn-success" onclick="saveForm()">
            <i class="icon-save"></i>
            {l s='Save' mod='gdprpro'}
        </a>
        <a class="btn btn-primary" href="https://www.eugdpr.org/" target="_blank" rel="noreferrer">
            <i class="icon-eur"></i>
            {l s='GDPR Legislation' mod='gdprpro'}
        </a>
        <a class="btn btn-default" href="{$link->getAdminLink('AdminModuleHooks')}">
            <i class="icon-user-secret"></i>
            {l s='Manage modules' mod='gdprpro'}
        </a>
    </div>
</div>