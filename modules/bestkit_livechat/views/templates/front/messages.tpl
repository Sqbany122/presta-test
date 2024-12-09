{foreach from=$livechat_messages item=message}
	{if $message.is_admin}
	    <li class="left clearfix"><span class="chat-img pull-left">
	        <img src="https://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
	    </span>
	        <div class="chat-body clearfix">
	            <div class="header">
	                <strong class="primary-font">{bestkit_livechat::getConfig('operator_name')|escape:'html':'UTF-8'}</strong> <small class="pull-right text-muted">
	                    <span class="glyphicon glyphicon-time"></span>{$message.relative_date|escape:'html':'UTF-8'}</small>
	            </div>
	            <p class="word-wrap">{$message.message|escape:'html':'UTF-8'}</p>
	        </div>
	    </li>
	{else}
        <li class="right clearfix"><span class="chat-img pull-right">
            <img src="https://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-circle" />
        </span>
            <div class="chat-body clearfix">
                <div class="header">
                    <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>{$message.relative_date|escape:'html':'UTF-8'}</small>
                    <strong class="pull-right primary-font">{$livechat_user.name|escape:'html':'UTF-8'}</strong>
                </div>
                <p class="word-wrap">{$message.message|escape:'html':'UTF-8'}</p>
            </div>
        </li>
	{/if}
{/foreach}
<script>
	$('#bestkit_livechat .panel-heading b').text('{l s='We are' mod='bestkit_livechat'} {if $is_online}{l s='Online' mod='bestkit_livechat'}{else}{l s='Offline' mod='bestkit_livechat'}{/if}');
</script>