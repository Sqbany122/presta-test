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

<div id="pch-tabs-container">
	<div class="row">
		{* ******** BEGIN - LEFT MENU ******** *}
		<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
			<div class="list-group toggleTabs">
			
				{foreach from=$configTabs key=key item=tab}
					<a class="list-group-item{if $tab.isActive} active{/if}{if isset($tab.subTabs) && $tab.subTabs} has-sub{/if}{if $tab.content} t-pane{/if}" {if isset($tab.subTabs) && $tab.subTabs}data-toggle="collapse"{/if} {if $tab.content}data-target="{$tab.id|escape:'htmlall':'UTF-8'}"{/if} {if isset($tab.subTabs) && $tab.subTabs}href="#{$tab.id|escape:'htmlall':'UTF-8'}"{elseif isset($tab.aHref)}href="{$tab.aHref|escape:'htmlall':'UTF-8'}"{/if}>
                        <i class="icon {$tab.icon|escape:'htmlall':'UTF-8'}"></i>
                        {$tab.title|escape:'htmlall':'UTF-8'}
						{if isset($tab.subTabs) && $tab.subTabs}
							<span class="pull-right"><i class="icon-caret-down"></i></span>
						{/if}
                    </a>
					{if isset($tab.subTabs) && $tab.subTabs}
						<div id="{$tab.id|escape:'htmlall':'UTF-8'}" class="panel-collapse collapse{if $tab.isActive} in{/if}">	
							{foreach from=$tab.subTabs item=subtab}
								<a class="list-group-item{if $subtab.isActive} active{/if}{if $subtab.content} t-pane{/if}" {if $subtab.content}data-target="{$subtab.id|escape:'htmlall':'UTF-8'}"{/if} {if isset($subtab.aHref)}href="{$subtab.aHref|escape:'htmlall':'UTF-8'}"{/if}>
									<i class="submenu icon {$subtab.icon|escape:'htmlall':'UTF-8'}"></i>
									{$subtab.title|escape:'htmlall':'UTF-8'}
								</a>
							{/foreach}
						</div>	
					{/if}
                {/foreach}
			</div>
		</div>
	
		{* ******** END - LEFT MENU ******** *}

		{* ******** BEGIN - TAB CONTENT ******** *}
		<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
			<div class="tab-content-container">
				{foreach from=$configTabs key=key item=tab}
					{if $tab.content}
						<div id="content-tab-{$tab.id|escape:'htmlall':'UTF-8'}" class="t-content{if $tab.isActive} active{/if}">
							{* HTML No need for escape*}
							{$tab.content}
						</div>
					{/if}
					{if isset($tab.subTabs) && $tab.subTabs}
						{foreach from=$tab.subTabs item=subtab}
							{if $subtab.content}
								<div id="content-tab-{$subtab.id|escape:'htmlall':'UTF-8'}" class="t-content{if $subtab.isActive} active{/if}">
									{* HTML No need for escape*}
									{$subtab.content}
								</div>
							{/if}
						{/foreach}
					{/if}
				{/foreach}
				</div>
			</div>
		</div>
		{* ******** END - TAB CONTENT ******** *}
	</div>
</div>		