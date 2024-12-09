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
    {l s='Request my data' mod='gdprpro'}
{/block}

{block name='page_content'}
    <div class="div_request_my_data">
        <div class="div_gdpr_image"></div>
        <blockquote>
            <p>{l s='In accordance with the EU General Data Protection Regulation (GDPR), you have the right to request and receive your personal data stored by our company as a consequence of your usage of the website. If you would like to go through with this process, please click below to start the process.' mod='gdprpro'}</p>
        </blockquote>
        <div class="div_gdpr_buttons">
            <a class="btn btn-primary data-request"
               href="{$link->getModuleLink('gdprpro','datarequest',['type'=>'download'])}"
               id="delete-my-data">
			  <span class="link-item">
				<i class="material-icons">insert_drive_file</i>
                  {l s='Request my data' mod='gdprpro'}
			  </span>
            </a>
            <a class="btn btn-primary"
               href="{$pdfLink}" target="_blank">
			  <span class="link-item">
				<i class="material-icons">assignment_returned</i>
                  {l s='Download my data' mod='gdprpro'}
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
