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
{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='Delete my data' mod='gdprpro'}
{/block}

{block name='page_content'}
<div class="div_delete_my_data">
	<div class="div_gdpr_image"></div>
    <blockquote>
		<p>{l s='In accordance with the EU General Data Protection Regulation (GDPR), you have the right to request the anonymization of any personal data held on the website. Please note that this process is irreversible and cannot be undone. We recommend that you first request a download of any personal data held and request anonymization after you have analyzed this data.' mod='gdprpro'}</p>
		<p>{l s='If you are sure you want to proceed with this process, you can start by clicking below.' mod='gdprpro'}</p>
    </blockquote>
	<div class="div_gdpr_buttons">
        <a class="btn btn-danger data-request"
           href="{$link->getModuleLink('gdprpro','datarequest',['type'=>'delete'])}"
           id="delete-my-data">
          <span class="link-item">
            <i class="material-icons">delete_forever</i>
              {l s='Delete my data' mod='gdprpro'}
          </span>
        </a>
	</div>
</div>	
{literal}
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function (event) {
            $('.data-request').click(function (event) {
                    event.preventDefault();
                    var result = confirm("{/literal}{l s='Are you sure?' mod='gdprpro'}{literal}");
                    if (result) {
                        $.ajax({
                            method: "POST",
                            url: $(this).attr('href')
                        }).done(function (msg) {
                            alert("{/literal}{l s='Request sent' mod='gdprpro'}{literal}");
                        });
                    }
                }
            );
        });
    </script>
{/literal}
{/block}
