<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<div id="bestkit_livechat_form" style="display:none;">
	<div class="">
	    <div class="row">
	        <div class="col-lg-5">
	            <div class="btn-panel btn-panel-conversation">
	                <a href="" class="btn  col-lg-4 send-message-btn open-chat-button" role="button"><i class="fa fa-arrow-down"></i> {l s='Show chat' mod='bestkit_livechat'}</a>
	                <a href="" class="btn  col-lg-8  send-message-btn pull-right open-chat-button new-count" role="button"><i class="fa fa-warning"></i> {l s='You have' mod='bestkit_livechat'} <span id="total_new_messages">{$bestkit_livechat.total_new_messages}</span> {l s='new' mod='bestkit_livechat'} {if $bestkit_livechat.total_new_messages eq 1}{l s='message' mod='bestkit_livechat'}{else}{l s='messages' mod='bestkit_livechat'}{/if}!</a>
	            </div>
	        </div>
	
	        <div class="col-lg-offset-1 col-lg-5">
	            <div class="btn-panel btn-panel-msg">
	
	                <a href="{$bestkit_livechat.configure_url|escape:false}" class="btn  col-lg-4  send-message-btn pull-right" role="button"><i class="fa fa-gears"></i> {l s='Chat Settings' mod='bestkit_livechat'}</a>
	            </div>
	        </div>
	    </div>
	    <div class="row chat-content" style="display:none">
	        <div class="conversation-wrap col-lg-3">
				<div class="media conversation maket" data-userkey="">
	                <div class="pull-left">
	                   <span class="all_messages"></span>
	                   <br>
	                   <small></small><br/>
	                   <small class="new"><span class="new_messages"></span> - {l s='new' mod='bestkit_livechat'}!</small>
	                </div>
	                <div class="media-body">
	                    <a class="delete-conversation" href="">X</a>
	                    <h5 class="media-heading user-name"></h5>
	                    <small class="user-email"></small><br/>
	                </div>
	            </div>
				{foreach from=$bestkit_livechat.users item=user}
		            <div class="media conversation" data-userkey="{$user.user_key|escape:false}">
		                <div class="pull-left">
		                   <span class="all_messages">{$user.all_messages|escape:'html':'UTF-8'}</span>
		                   <br>
		                   <small class="msg-count-title">{if $user.all_messages eq 1}{l s='message' mod='bestkit_livechat'}{else}{l s='messages' mod='bestkit_livechat'}{/if}</small><br/>
		                   	<small class="new"{if !$user.new_messages} style="display:none"{/if}><span class="new_messages">{$user.new_messages}</span> - {l s='new' mod='bestkit_livechat'}!</small>
		                </div>
		                <div class="media-body">
		                    <a class="delete-conversation" href="">X</a>
		                    <h5 class="media-heading user-name">{$user.name|escape:'html':'UTF-8'}</h5>
		                    <small class="user-email">{$user.email|escape:'html':'UTF-8'}</small><br/>
		                </div>
		            </div>
				{foreachelse}
					<div class="no-users">{l s='No users...' mod='bestkit_livechat'}</div>
				{/foreach}
	        </div>
	        <div class="message-wrap col-lg-8">
	            <div class="msg-wrap">
					{if !count($bestkit_livechat.users)}
						<div class="no-user-messages">{l s='No user messages...' mod='bestkit_livechat'}</div>
					{/if}
	            </div>
	            <div class="send-wrap ">
	                <textarea class="form-control send-message" rows="3" maxlength="255" placeholder="{l s='Write a reply...' mod='bestkit_livechat'}"></textarea>
	            </div>
	            <div class="btn-panel">
	                <a id="send_message" class=" col-lg-4 text-right btn send-message-btn pull-right" role="button"><i class="fa fa-plus"></i> {l s='Send Message' mod='bestkit_livechat'}</a>
	            </div>
	        </div>
	    </div>
	</div>
	<script>
		var bestkit_livechat = {
			controller: '{$link->getAdminLink("AdminBestkitLivechat", false)|escape:false}',
			last_message_time: {$bestkit_livechat.last_message_time|escape:false}
		}
		
		var bestkit_livechat_tr = {
			hide_chat: '{l s='Hide chat' mod='bestkit_livechat'}',
			show_chat: '{l s='Show chat' mod='bestkit_livechat'}',
			delete_conversation: '{l s='Are you sure to want delete the conversation?' mod='bestkit_livechat'}',
			no_users: '{l s='No users...' mod='bestkit_livechat'}',
			no_user_messages: '{l s='No user messages...' mod='bestkit_livechat'}',
			message: '{l s='message' mod='bestkit_livechat'}',
			messages: '{l s='messages' mod='bestkit_livechat'}',
			choose_conversation: '{l s='Please choose a conversation!' mod='bestkit_livechat'}',
		}
	</script>
</div>