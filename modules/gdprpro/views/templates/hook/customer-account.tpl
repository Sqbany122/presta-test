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
{if (GdprPro::isPs17())}
    <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 data-request"
       href="{$link->getModuleLink('gdprpro','erasemydata')}">
      <span class="link-item">
        <i class="material-icons">delete</i>
          {l s='Erase my data' mod='gdprpro'}
      </span>
    </a>
    <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 data-request"
       href="{$link->getModuleLink('gdprpro','requestmydata')}">
      <span class="link-item">
        <i class="material-icons"><i class="material-icons">assignment_returned</i></i>
          {l s='Request my data' mod='gdprpro'}
      </span>
    </a>
{else}
    <li>
        <a href="{$link->getModuleLink('gdprpro','erasemydata')}" class="data-request">
            <i class="icon-trash"></i>
            <span>{l s='Erase my data' mod='gdprpro'}</span>
        </a>
    </li>
    <li>
        <a href="{$link->getModuleLink('gdprpro','requestmydata')}" class="data-request">
            <i class="icon-save"></i>
            <span>{l s='Download my data' mod='gdprpro'}</span>
        </a>
    </li>
{/if}
