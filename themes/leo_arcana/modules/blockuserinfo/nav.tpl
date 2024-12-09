
<!-- Block user information module NAV  -->
<div class="header_user_info">
	{if $is_logged}
	<div class="e-scale popup-over">
		<div data-toggle="dropdown" class="popup-title"><a href="#"><i class="fa fa-user"></i> {l s='Moje konto' mod='blockuserinfo'}</a></div>	
		<div class="popup-content">
			<ul class="links">

				{if $is_logged}
					<li>
						<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow"><span>{l s='Hello' mod='blockuserinfo'}, {$cookie->customer_firstname} {$cookie->customer_lastname}</span></a>
					</li>
				{/if}
				<li>
					<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='My account' mod='blockuserinfo'}"><i class="fa fa-user"></i>{l s='My Account' mod='blockuserinfo'}</a> - numer ID klienta: <b>{$cookie->id_customer}</b>
				</li>
				<!--<li>
					<a href="{$link->getPageLink(order, true)|escape:'html':'UTF-8'}" title="{l s='Checkout' mod='blockuserinfo'}" class="last"><i class="fa fa-share"></i>{l s='Checkout' mod='blockuserinfo'}</a>
				</li>
				<li>
					<a class="ap-btn-wishlist" id="wishlist-total" href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)|addslashes}" title="{l s='My wishlists' mod='blockuserinfo'}">
						<i class="fa fa-heart"></i>{l s='Wish List' mod='blockuserinfo'}<span class="ap-total ap-total-wishlist"></span>
					</a>
				</li>
				<li>
					<a class="ap-btn-compare" href="{$link->getPageLink('products-comparison')|escape:'html':'UTF-8'}" title="{l s='Compare' mod='blockuserinfo'}" rel="nofollow">
						<i class="fa fa-compress"></i>{l s='Compare' mod='blockuserinfo'}<span class="ap-total ap-total-compare"></span>
					</a>
				</li>!-->

				{if $is_logged}
					<li>
						<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">
							<i class="fa fa-unlock-alt"></i>{l s='Sign out' mod='blockuserinfo'}
						</a>
					</li>
				{else}
					<li>
						<a class="login" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Login to your customer account' mod='blockuserinfo'}">
							<i class="fa fa-unlock-alt"></i>{l s='Sign in' mod='blockuserinfo'}
						</a>
					</li>
				{/if}
			</ul>
		</div>
	</div>
	{else}
		<div class="header_info hidden-xl hidden-md hidden-lg"><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='login' mod='blockuserinfo'}</a>{l s=' or ' mod='blockuserinfo'}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='create an account' mod='blockuserinfo'}</a></div>
		<div class="header_info hidden-xs hidden-sp">{l s='Welcome visitor you can ' mod='blockuserinfo'}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='login' mod='blockuserinfo'}</a>{l s=' or ' mod='blockuserinfo'}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='create an account' mod='blockuserinfo'}</a></div>
	{/if}
</div>	