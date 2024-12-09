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

(function($){
	$(document).ready(function(){

		function validateEmail(email) {
		    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		    return re.test(email);
		}

		var sound = document.createElement('audio');
        sound.setAttribute('src', bestkit_livechat.sound_url);

		var isSending = false;
		var chat = $('#bestkit_livechat');

		var resize = function(){
			if (chat.offset().left < parseInt(chat.css('right'))) {
				var right = $('body').width() - (chat.width());
				right = parseInt(right / 2);
				chat.css({'right': right+'px'});
			}
		}

		resize();
		$( window ).resize(function(){
			resize();
		});

		var setContent = function(html, playSound){
			var count = chat.find('.chat li:not(#first_message)').length;
			$('#bestkit_livechat .chat li:not(#first_message), #bestkit_livechat .chat script').remove();
			chat.find('.chat #first_message').after(html);
			var currCount = chat.find('.chat li:not(#first_message)').length;
			if (count < currCount) {
				if (playSound) {
					sound.play();
					chat.find('.chat').parent().animate(
						{scrollTop: chat.find('.chat').height()}, 
						500,
						function(){
							/*chat.find('li:last').effect("shake", {
								times: 2,
								distance: 10
							}, 50);*/
						}
					);

					if (!chat.hasClass('opened')) {
						chat.find('#accordion').click();
					}
				} else {
					chat.find('.chat').parent().animate({scrollTop: chat.find('.chat').height()}, 500);
				}
			}

			if (count > currCount) {
				chat.addClass('need_login');
				chat.find('#first_message .chat-body p').text(bestkit_livechat.first_message);
				grecaptcha.reset();
				sound.play();
			}
		}

		var isFirst = true;
		var updateAllMessages = function(content){
			if (content == '') {
				if (!isSending) {
					$.ajax({
						url: bestkit_livechat.controller,
						type: 'POST',
						success: function(html){
							if (isFirst) {
								setContent(html, false);
								isFirst = false;
							} else {
								setContent(html, true);
							}
						},
						complete: function(){
							isSending = false;
						}
					});
				}
			} else {
				setContent(content, false);
			}
		}

		if (!chat.hasClass('need_login')) {
			updateAllMessages('');
		}

		chat.find('#accordion').click(function(){
			var height = '+=299';
			if ($('#bestkit_livechat').hasClass('opened')) {
				height = '-=299';
			}

			chat.animate({'height': height}, 400, function(){
				$(this).removeClass('opened').removeClass('closed');
				if (height == '+=299') {
					$(this).addClass('opened');
				} else {
					$(this).addClass('closed');
				}
			});
		});

		var input = chat.find('#btn-input');
		input.keypress(function(e){
			if (e.which == 13) {
				chat.find('#btn-chat').click();
				return false;
			}
		});

		chat.find('#btn-chat').click(function(){
			if (!isSending) {
				var name = '';
				var email = '';
				var need_login = 0;
				if (chat.hasClass('need_login')) {
					var errors = false;
					name = encodeURIComponent(chat.find('#btn-input-name').val());
					email = chat.find('#btn-input-email').val();
					if (name == '') {
						chat.find('#btn-input-name').focus()/*.effect("shake", {
							times: 2,
							distance: 5
						}, 50);*/
						return false;
					}

					if (email == '' || !validateEmail(email)) {
						chat.find('#btn-input-email').focus()/*.effect("shake", {
							times: 2,
							distance: 5
						}, 50);*/
						return false;
					}
					
					if (chat.find('#g-recaptcha-response').length == 0) {
						alert('Please set up reCAPTCHA to security reason!');
						return false;
					}

					if (chat.find('#g-recaptcha-response').val().length == 0) {
						alert(bestkit_livechat.captcha);
						return false;
					}

					need_login = 1;
				}

				var message = encodeURIComponent($.trim(input.val()));
				if (message.length > 0) {
					isSending = true;
					input.attr('disabled', true);
					var captcha = encodeURIComponent(chat.find('#g-recaptcha-response').val());
					$.ajax({
						url: bestkit_livechat.controller,
						type: 'POST',
						data: {'message': message, 'name': name, 'email': email, 'need_login': need_login, 'captcha': captcha},
						success: function(content){
							if (content == 'captcha_error') {
								alert(bestkit_livechat.captcha);
							} else {
								updateAllMessages(content);
								input.val('');
								if (need_login) {
									chat.removeClass('need_login');
								}
							}
						},
						complete: function(){
							isSending = false;
							input.attr('disabled', false);
							input.focus();
						}
					});
				} else {
					input.focus();/*.parent().effect("shake", {
							times: 2,
							distance: 5
						}, 50);*/
				}
			}
		});
		
		setInterval(function(){
			updateAllMessages('');
		}, bestkit_livechat.interval);
	});
})(jQuery);