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
{if isset($under18Enable) && $under18Enable}
    <div class="div_under_18 div_cookie_category">
        <span id="under-18" class="not_checked"
              style="border: 4px solid red; padding: 2px 11px 1px 11px; margin-right: -15px; font-weight: 800">
            {l s="I'm under 18" mod='gdprpro'}
        </span>
    </div>
{/if}
