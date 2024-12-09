/**
* 2007-2014 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

var bestKitLiveChatJs = function($){
	var standard_title = $('title').text();
	var title = standard_title;
	
	var changedTitle = false;
	setInterval(function(){
		if (changedTitle) {
			changedTitle = false;
			$('title').text(standard_title);
		} else {
			changedTitle = true;
			$('title').text(title);
		}
	}, 1500);

	form = $('#bestkit_livechat_form');
	$('#content').prepend(form);
	form.show();

	var sound = document.createElement('audio');
    sound.setAttribute('src', '../modules/bestkit_livechat/sound/sound.mp3');

	$('a.open-chat-button').click(function(){
		$('.chat-content').stop().toggle(400, function(){
			if ($(this).is(':visible')) {
				$('a.open-chat-button:first').html('<i class="fa fa-arrow-up"></i> '+bestkit_livechat_tr.hide_chat);
			} else {
				$('a.open-chat-button:first').html('<i class="fa fa-arrow-down"></i> '+bestkit_livechat_tr.show_chat);
			}
		});
		return false;
	});

	var setContent = function(content){
		var count = form.find('.wrap-height').height();
		form.find('.msg-wrap').html(content);
		if (count != form.find('.wrap-height').height()) {
			form.find('.msg-wrap').animate({scrollTop: form.find('.wrap-height').height()}, 500);
		}
	}

	var is_ajax = false;

	form.find('.media.conversation').live('click', function(){
		var user_key = $(this).data('userkey');
		if (!is_ajax && user_key.length > 0) {
			form.find('.media.conversation').removeClass('active');
			$(this).addClass('active');
			is_ajax = true;
			$.ajax({
				'url': bestkit_livechat.controller,
				'type': 'POST',
				'data': {'liveChatAction': 'getMessages', 'user_key': user_key},
				'success': function(html){
					setContent(html);
				},
				'complete': function(){
					is_ajax = false;
				}
			});
		}
	});

	form.find('.conversation .delete-conversation').live('click', function(){
		var block = $(this).parents('.conversation:first');
		var user_key = block.data('userkey');
		if (is_ajax || user_key.length == 0 || !confirm(bestkit_livechat_tr.delete_conversation)) {
			return false;
		}

		is_ajax = true;
		var nextBlock;
		if (block.hasClass('active')) {
			nextBlock = block.next();
			if (nextBlock.length == 0) {
				nextBlock = block.prev();
			}
		} else {
			nextBlock = form.find('.conversation.active');
		}

		$.ajax({
			'url': bestkit_livechat.controller,
			'type': 'POST',
			'data': {'liveChatAction': 'deleteChat', 'user_key': user_key},
			'success': function(){
				block.remove();
				if (nextBlock.length == 0 || (nextBlock.length == 1 && nextBlock.hasClass('maket'))) {
					form.find('.conversation-wrap .maket').after('<div class="no-users">'+bestkit_livechat_tr.no_users+'</div>');
					setContent('<div class="no-user-messages">'+bestkit_livechat_tr.no_user_messages+'</div>');
				} else {
					is_ajax = false;
					nextBlock.click();
				}
			},
			'complete': function(){
				is_ajax = false;
			}
		});

		return false;
	});

	form.find('.media.conversation:last').click();

	form.find('#send_message').click(function(){
		var conversation = form.find('.media.conversation.active');
		if (conversation.length == 0) {
			alert(bestkit_livechat_tr.choose_conversation);
			return false;
		}

		var textarea = form.find('textarea');
		var message = encodeURIComponent($.trim(textarea.val()));
		var user_key = conversation.data('userkey');

		if (is_ajax || message.length == 0 || user_key.length == 0) {
			return false;
		}

		is_ajax = true;
		textarea.attr('disabled', true);
		$.ajax({
			'url': bestkit_livechat.controller,
			'type': 'POST',
			'data': {'liveChatAction': 'sendMessage', 'user_key': user_key, 'message': message},
			'success': function(html){
				setContent(html);
				textarea.val('');
			},
			'complete': function(){
				is_ajax = false;
				textarea.attr('disabled', false);
			}
		});
	});

	setInterval(function(){
		if (is_ajax) {
			return false;
		}

		is_ajax = true;
		var activeUser = form.find('.media.conversation.active').data('userkey');

		$.ajax({
			'url': bestkit_livechat.controller,
			'type': 'POST',
			'dataType': 'json',
			'data': {'liveChatAction': 'updating', 'activeUser': activeUser},
			'success': function(json){
				setContent(json.active_chat);
				$('#total_new_messages').text(json.total_new_messages);
				if (parseInt(json.total_new_messages) > 0) {
					title = form.find('.new-count').text();
				} else {
					title = standard_title;
				}

				if (json.last_message_time > bestkit_livechat.last_message_time) {
					sound.play();
					bestkit_livechat.last_message_time = json.last_message_time;
				}

				for (var i in json.chats) {
					var chat = json.chats[i];
					var block = form.find('.media.conversation[data-userkey="'+chat.user_key+'"]');
					if (block.length == 0) {
						block = form.find('.media.conversation:last').clone();
						block.removeClass('active');
						block.removeClass('maket');
						block.attr('data-userkey', chat.user_key);
						block.find('.user-name').text(chat.name);
						block.find('.user-email').text(chat.email);
						form.find('.conversation-wrap').append(block);
					}

					block.find('.all_messages').text(chat.all_messages);
					if (parseInt(chat.all_messages) > 1) {
						block.find('.msg-count-title').text(bestkit_livechat_tr.messages);
					} else {
						block.find('.msg-count-title').text(bestkit_livechat_tr.message);
					}

					if (parseInt(chat.new_messages) > 0) {
						block.find('.new_messages').text(chat.new_messages).parent().show();
					} else {
						block.find('.new_messages').parent().hide();
					}
				}
				
				if (json.chats.length > 0) {
					form.find('.no-users').remove();
				}
			},
			'complete': function(){
				is_ajax = false;
			}
		});
	}, 5000);
}


if (typeof jQuery == 'undefined') {
	window.onload = function(){
		bestKitLiveChatJs(jQuery);
	}
} else {
	$(document).ready(function(){
		bestKitLiveChatJs(jQuery);
	});
}

