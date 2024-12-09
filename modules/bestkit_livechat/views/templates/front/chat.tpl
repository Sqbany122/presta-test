<div class="container{if $bestkit_livechat.is_logged eq false} need_login{/if}" id="bestkit_livechat">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary" style="border-color:{$bestkit_livechat.color|escape:false}">
                <div class="panel-heading" id="accordion" style="background-color:{$bestkit_livechat.color|escape:false};border-color:{$bestkit_livechat.color|escape:false};">
                    <span class="glyphicon glyphicon-comment"></span> {l s='Live Chat' mod='bestkit_livechat'} - <b>{l s='We are' mod='bestkit_livechat'} {if $bestkit_livechat.is_online}{l s='Online' mod='bestkit_livechat'}{else}{l s='Offline' mod='bestkit_livechat'}{/if}</b>
                    <div class="btn-group pull-right">
                        <a type="button" class="btn btn-default btn-xs chat-opener">
                            <span class="glyphicon glyphicon-chevron-down"></span>
                        </a>
                    </div>
                </div>
            <div class="panel-collapse in" id="collapseOne">
                <div class="panel-body">
                    <ul class="chat">
                        <li class="left clearfix" id="first_message"><span class="chat-img pull-left">
                            <img src="https://placehold.it/50/55C1E7/fff&text=U" alt="{l s='User Avatar' mod='bestkit_livechat'}" class="img-circle" />
                        </span>
                            <div class="chat-body clearfix">
                                <div class="header">
                                    <strong class="primary-font">{$bestkit_livechat.operator_name|escape:'html':'UTF-8'}</strong> <small class="pull-right text-muted">
                                        <span class="glyphicon glyphicon-time"></span></small>
                                </div>
                                <p class="word-wrap">
                                    {$bestkit_livechat.first_message|escape:'html':'UTF-8'}
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="panel-footer">
                	{*if $bestkit_livechat.is_logged eq false*}
	                    <div class="input-group login">
	                        <input id="btn-input-name" maxlength="25" type="text" class="form-control input-sm" placeholder="{l s='Your name...' mod='bestkit_livechat'}" />
	                        <input id="btn-input-email" maxlength="30" type="text" class="form-control input-sm" placeholder="{l s='Your email...' mod='bestkit_livechat'}" />
	                        
	                        <div class="g-recaptcha" data-sitekey="{if $bestkit_livechat.recaptcha_sitekey}{$bestkit_livechat.recaptcha_sitekey}{else}none_key{/if}"></div>
	                    </div>
                    {*/if*}
                    
                    <div class="input-group">
                        <input id="btn-input" maxlength="255" type="text" class="form-control input-sm" placeholder="{l s='Wpisz tutaj swoją wiadomość' mod='bestkit_livechat'}" />
                        <span class="input-group-btn">
                            <button class="btn btn-warning btn-sm" id="btn-chat">
                                {l s='Wyślij' mod='bestkit_livechat'}
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <script>
    	var bestkit_livechat = {
	    	controller: '{$link->getModuleLink("bestkit_livechat", "chat")|escape:false}',
	    	sound_url: '{$bestkit_livechat.sound_url|escape:false}',
		    first_message: '{l s='Your conversation has been removed by operator.' mod='bestkit_livechat' js=1}',
		    captcha: '{l s='Please click by Captcha!' mod='bestkit_livechat' js=1}',
		    interval: {$bestkit_livechat.interval|intval}
    	}
    </script>
</div>