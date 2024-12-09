<div class="wrap-height">
{foreach from=$livechat_messages item=message}
	{if $message.is_admin}
	    <div class="media msg admin">
	        <div class="media-body">
	            <small class="pull-right time"><i class="fa fa-clock-o"></i> {$message.relative_date|escape:'html':'UTF-8'}</small>
	            <h5 class="media-heading">{$livechat_operator|escape:'html':'UTF-8'}</h5>
	            <small class="col-lg-10 word-wrap">{$message.message|escape:'html':'UTF-8'}</small>
	        </div>
	    </div>
	{else}
	    <div class="media msg user">
	        <div class="media-body">
	            <small class="pull-right time"><i class="fa fa-clock-o"></i> {$message.relative_date|escape:'html':'UTF-8'}</small>
	            <h5 class="media-heading">{$livechat_user.name|escape:'html':'UTF-8'}</h5>
	            <small class="col-lg-10 word-wrap">{$message.message|escape:'html':'UTF-8'}</small>
	        </div>
	    </div>
	{/if}
{foreachelse}
	<div class="no-user-messages">{l s='No user messages...' mod='bestkit_livechat'}</div>
{/foreach}
</div>
