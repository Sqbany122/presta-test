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

<div class="panel kpi-container">
    <div class="panel-heading">
        <i class="icon-home"></i>&nbsp;&nbsp;{l s='Welcome' mod='gdprpro'}
    </div>
    <div class="kpi-refresh"></div>
    <div class="row">
        <div class="alert alert-success col-xs-12 col-sm-12 col-md-12 col-lg-12">
			{l s='Welcome and thank you for having purchased our module. Please read carefully the PDF documentation included with the module.' mod='gdprpro'}
		</div>
		
		<div class="image-container col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<div>
                <a target="blank" href="https://addons.prestashop.com/en/226_prestachamps">
                    <img class="bt-effect img-responsive" src="{$localPath}/views/img/bannerv2.jpg" alt="{l s='Discover' mod='gdprpro'}">
                </a>
            </div>
        </div>

        <div class="button-container col-xs-12 col-sm-12 col-md-6 col-lg-6">
			
            <div class="custom-text-container">
                {l s='We provide detailed documentation in five different languages, to ease the configuration of the module as much as possible.
                If you have any questions, we are at your disposal with any information you require.
                Don`t hesitate to leave a rating and a comment on the module. We value your opinions and feedback.
                Keep an eye on the module version and check if updates are available. If you need help with updating, just contact us.' mod='gdprpro'}
            </div>

            <div class="col-sm-6 col-lg-6 text-center">
                <button class="btn btn-default welcome-champs-button champs-button-documentation">
                    <i class="icon-folder-open"></i>
                    {l s='Documentation' mod='gdprpro'}
                </button>
                <div class="documentation-dropdown documentation-dropdown-languages">
                    <a href="{$localPath}documentation_en.pdf" target="_blank" class="lang">EN</a>
                    <a href="{$localPath}documentation_it.pdf" target="_blank" class="lang">IT</a>
                    <a href="{$localPath}documentation_es.pdf" target="_blank" class="lang">ES</a>
                    <a href="{$localPath}documentation_fr.pdf" target="_blank" class="lang">FR</a>
                    <a href="{$localPath}documentation_de.pdf" target="_blank" class="lang">DE</a>
                </div>

            </div>

            <div class="col-sm-6 col-lg-6 text-center">
                <a href="https://addons.prestashop.com/contact-form.php?id_product=31185" target="_blank">
                    <button class="btn btn-default welcome-champs-button champs-button-contact-support">
                        <i class="icon-headphones"></i>
                        {l s='Contact support' mod='gdprpro'}
                    </button>
                </a>
            </div>

            <div class="col-sm-6 col-lg-6 text-center">
                <a href="https://addons.prestashop.com/en/ratings.php" target="_blank">
                    <button class="btn btn-default welcome-champs-button champs-button-rate">
                        <i class="icon-star"></i>
                        {l s='Rate me' mod='gdprpro'}
                    </button>
                </a>
            </div>

            <div class="col-sm-6 col-lg-6 text-center">
                <button class="btn btn-default welcome-champs-button champs-button-info">
                    <i class="icon-info-circle"></i>
                    {l s='Version' mod='gdprpro'}: {$moduleVersion}
                </button>
            </div>

        </div>
    </div>
</div>