{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !isset($content_only) || !$content_only}
								
						</section>
						{*if isset($right_column_size) && !empty($right_column_size)}
						<!-- Right -->
						<section id="right_column" class="column sidebar col-md-{$right_column_size|intval}">
								{$HOOK_RIGHT_COLUMN}
						</section>
						{/if*} 
				{if isset($fullwidth_hook.displayHome) AND $fullwidth_hook.displayHome == 0}
					</div>
					</div>
				{else}
					</div>
				{/if}
            </section>
			<!-- Footer -->
			<footer id="footer" class="footer-container">       
				{if isset($fullwidth_hook.displayFooter) AND $fullwidth_hook.displayFooter == 0}
				<div class="container">  	
				{/if}
						{$HOOK_FOOTER}							
				{if isset($fullwidth_hook.displayFooter) AND $fullwidth_hook.displayFooter == 0}
				 
				</div>
				{/if}
            </footer>
		</section><!-- #page -->
		<div id="back-top"><a href="#" class="fa fa-angle-up"></a></div>
{/if}
{include file="$tpl_dir./global.tpl"}
{if isset($LEO_PANELTOOL) && $LEO_PANELTOOL}
    {include file="$tpl_dir./info/paneltool.tpl"}
{/if}

{*literal}
<style type="text/css">
	#exitpopup { text-align:center; position:relative;width:600px; height:auto; margin:0px auto; display:none; position:fixed; color:#ffffff; z-index:999999; background:rgb(20, 20, 20); background:rgba(0, 0, 0, 0.8); }
	#exitpopup h1 { margin-top:0px;	padding-top:0px; }	
	#exitpopup p { text-align:left;	}
@media screen and (max-width: 720px) {
	#exitpopup img, #exitpopup {width:100%!important;height:auto; top:15px!important; left:0px!important; }
}
</style>
<div style="display: none; left:0px; top:0px; width:100%; height:100%; position:fixed; background:#000000; opacity: .8; filter:alpha(opacity=0.8); z-index:999998;" id="exitpopup_bg"></div>
<div id="exitpopup">
	<a href="#" id="exit_click" style="border-radius: 50% 50%;width:32px;height:32px;position:absolute;top:-8px;right:-8px;display:block;background:#fff url('/img/close-x.png');background-size:100% 100%;"></a>
	<a href="/moje-konto" target="_blank"><img src="/img/cms/banner_pegaz_akcja_promo.jpg" style="max-height:600px;width:auto;" alt="Przegląd zimowy" /></a>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('#exitpopup').css('left', (window.innerWidth/2 - $('#exitpopup').width()/2));
	$('#exitpopup').css('top', (window.innerHeight/2 - 300));
	$('#exitpopup_bg').click(function(){
		$('#exitpopup_bg').fadeOut();
		$('#exitpopup').slideUp();
	});
	$('#exit_click').click(function(){
		$('#exitpopup_bg').fadeOut();
		$('#exitpopup').slideUp();
	});
	
	
	if (!localStorage.getItem('popupcookies1234a')) {	
		setTimeout(function() {
				$('#exitpopup_bg').fadeIn();
				$('#exitpopup').fadeIn();
				localStorage.setItem('popupcookies1234a', 'true'); 
				localStorage.setItem('popuphow', '1');
			}, 500);
	}
});
</script>
{/literal*}

	</body>
</html>
