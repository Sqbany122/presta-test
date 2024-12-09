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
{if isset($under16Enable) && $under16Enable}
    <div class="div_under_16 div_cookie_category">
        <span id="span_under_16" class="span_under_16 not_checked">
            {l s="I'm under 16" mod='gdprpro'}
        </span>
    </div>
{/if}