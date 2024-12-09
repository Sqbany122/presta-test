/**
 * 2007-2019 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2019 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
var autoloadchatbox =false;
var autoStopRunAjax =false;
var playsound_enable=true;
var startTimerInteval =false;
var isAdminBusy = false;
var livechat = {
    get_browser_info:function()
    {
            var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 
            if(/trident/i.test(M[1])){
                tem=/\brv[ :]+(\d+)/g.exec(ua) || []; 
                return 'Internet explorer';// {name:'IE ',version:(tem[1]||'')};
                }   
            if(M[1]==='Chrome'){
                tem=ua.match(/\bOPR\/(\d+)/)
                if(tem!=null)   {
                    return 'Opera';//{name:'Opera', version:tem[1]};
                }
            }   
            M=M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
            if((tem=ua.match(/version\/(\d+)/i))!=null) {M.splice(1,1,tem[1]);}
            return M[0];
//            return {
//              name: M[0],
//              version: M[1]
//            };
    },
    stopRunAjax: function()
    {
        clearInterval(autoloadchatbox);
        autoloadchatbox=false;
    },
    playSound: function()
    {
        if($('#message_seen').val()!='1' && ETS_LC_USE_SOUND_FONTEND)
        {
            var x = document.getElementById("lg_ets_sound"); 
            if(x && $('.lc_chatbox .lc_sound').hasClass('enable'))
            {
                x.play();
            }
        }
    },
    getOldMessages:function()
    {
        if(!$('.lc_chatbox').hasClass('loaded') && !$('.lc_chatbox').hasClass('loading') )
        {
            $('.lc_chatbox').addClass('loading');
            $('.massage_more_loading').show();
            $.ajax({
                url: ETS_LC_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    getOldMessages: 1,
                    firstId: $('.lc_msg_board .lc_msg').length > 0 ? $('.lc_msg_board .lc_msg:first-child').attr('data-id-message') : 0,
                },
                success: function(json)
                { 
                    if(json.messages)
                    {
                        livechat.processMessages(json.messages);
                        if(json.loaded && !$('.lc_chatbox').hasClass('loaded'))
                            $('.lc_chatbox').addClass('loaded');
                        if(json.firstId)
                            $(".lc_messages").stop().animate({scrollTop: $('.lc_messages .lc_msg[data-id-message="'+json.firstId+'"]').position().top});
                    }
                    else
                    {
                        if(!$('.lc_chatbox').hasClass('loaded'))
                            $('.lc_chatbox').addClass('loaded');
                    }
                    $('.lc_chatbox').removeClass('loading');  
                    $('.massage_more_loading').hide();      
                }
            });
        }
    },
    displayEndChat : function()
    {
        $('.lc_msg_board').html('');
        $('.lc_chatbox .lc_introduction').removeClass('lc_hide');
        $('.lc_chatbox .lc_customer_info').removeClass('closed');
        $('.lc_chatbox .lc_update_info').addClass('lc_hide');
        $('.lc_chatbox .lc_messages').addClass('lc_hide');
        $('#id_departments').removeAttr('disabled');
        $('.lc_chatbox .lc_send').val(ETS_LC_TEXT_SEND_START_CHAT);
        $('.lc_chatbox').removeClass('is_admin_busy').removeClass('wait_support');
        $('.lc_text_area').addClass('start_chating');
        $('#clock_wait').html('');
        $('#lc_conversation_end_chat').val('1');
        if($('.lc_chatbox').hasClass('end_chat'))
        {
             $('#lc_form_livechat .star_content').html('<input class="star not_uniform" type="radio" name="criterion_livechat" value="1" data-toggle="tooltip" title="Terrible"/><input class="star not_uniform" type="radio" name="criterion_livechat" value="2" data-toggle="tooltip" title="Acceptable"/><input class="star not_uniform" type="radio" name="criterion_livechat" value="3" data-toggle="tooltip" title="Fairly Good"/><input class="star not_uniform" type="radio" name="criterion_livechat" value="4" data-toggle="tooltip" title="Good"/><input class="star not_uniform" type="radio" name="criterion_livechat" value="5" data-toggle="tooltip" title="Excellent"/>')
            $('input.star').rating();
        }
        $('.block_admin_accpeted').remove();
        if(startTimerInteval)
            clearInterval(startTimerInteval);
    },
    openChatWindow: function(refresh)
    {
        var message_delivered = parseInt($('.lc_chatbox input[name="message_delivered"]').val());
        var message_seen = parseInt($('.lc_chatbox input[name="message_seen"]').val());
        var message_writing = parseInt($('.lc_chatbox input[name="message_writing"]').val());
        var lc_ets_scroll=false;
        if($('.lc_messages').scrollTop() + $('.lc_messages').height()+13 >= $('.lc_messages_table-cell').height()) {
               lc_ets_scroll=true;
        }
        if(refresh && $('.lc_chatbox').hasClass('end_chat'))
            return false;
        $.ajax({
            url: ETS_LC_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                load_chat_box: 1,
                refresh:refresh,
                message_delivered:message_delivered,
                message_seen:message_seen,
                message_writing:message_writing,
                latestID: $('.lc_msg_board .lc_msg').length > 0 ? $('.lc_msg_board .lc_msg:last-child').attr('data-id-message') : 0,
                browser_name: livechat.get_browser_info(),
                current_url: window.location.href,
                product_page_product_id : $('body#product').length >0 && $('#product_page_product_id').length >0 ? $('#product_page_product_id').val() :0,
            },
            success: function(json)
            {
                if(json)
                {
                    if((!refresh || !json.has_conversation) && json.html)
                    {
                        if($('.lc_chatbox').length<=0 || ($('.lc_messages li.lc_msg').length>0 && $('.lc_chatbox').length>0))
                        {
                            $('.lc_chatbox').remove();
                            $('body').append(json.html);
                            livechat.scrollLiveChat();
                            $('.lc_messages').scroll(function(){
                                if($('.lc_messages').scrollTop()==0)
                                {
                                    livechat.getOldMessages();
                                }    
                            });
                            if (document.documentElement.clientWidth < 767 && $('.lc_chatbox').hasClass('lc_chatbox_open'))
                            {
                                $('.lc_chatbox').removeClass('lc_chatbox_open').addClass('lc_chatbox_closed');
                            }
                            if (document.documentElement.clientWidth < 767 && $('.lc_chatbox .lc_sound').length>0)
                            {
                                $('.lc_chatbox .lc_sound').removeClass('enable').addClass('disable');
                            }
                            if(json.wait_support && !json.isAdminBusy)
                            {
                                if($('#clock_wait').html()=='')
                                {
                                   livechat.startTimer(json.wait_support); 
                                }
                                $('.lc_chatbox').addClass('wait_support');
                            }
                            else
                               $('.lc_chatbox').removeClass('wait_support');
                        }
                    } 
                    else
                    {
                        if((json.end_chat_admin && !$('.lc_chatbox').hasClass('end_chat'))|| json.end_chat!='0')
                        {
                            if(ETS_LC_DISPLAY_RATING && json.end_chat_admin && !$('.lc_chatbox').hasClass('end_chat'))
                                $('.lc_chatbox').addClass('end_chat_rate');
                            else
                                $('.lc_chatbox').addClass('end_chat');
                            livechat.displayEndChat();
                        }
                        if(!$('.block_admin_accpeted').length && json.employee_accept && ($('.lc_chatbox').hasClass('is_admin_busy') || $('.lc_chatbox').hasClass('wait_support')))
                        {
                            $('.block_admin_busy').after('<div class="block_admin_accpeted">'+json.employee_accept+'</div>');
                        }
                        if(json.isAdminBusy)
                        {
                            $('.lc_chatbox').addClass('is_admin_busy');
                            $('.lc_chatbox .block_admin_busy').html(text_admin_busy+'<button class="lc_customer_end_chat">'+text_customer_end_chat+'</button>');
                        }    
                        else
                            $('.lc_chatbox').removeClass('is_admin_busy');
                        if(!json.upload_file)
                            $('.lc_chatbox .form_upfile').remove();
                        if(!json.isRequestAjax || json.end_chat!=0)
                        {
                            if(autoloadchatbox)
                            {
                                clearInterval(autoloadchatbox);
                                autoloadchatbox=false;
                            }
                        }
                        else
                        {
                            if(json.isRequestAjax!=isRequestAjax)
                            {
                                isRequestAjax = json.isRequestAjax;
                                if(autoloadchatbox)
                                {
                                    clearInterval(autoloadchatbox);
                                }
                                if(ETS_LC_TIME_OUT>0)
                                    autoloadchatbox = setInterval(livechat.openChatWindow, Math.ceil(ETS_LC_TIME_OUT*isRequestAjax),1);
                                else
                                    autoloadchatbox = setInterval(livechat.openChatWindow, Math.ceil(3000*isRequestAjax),1);
                            }
                        }
                        if(json.playsound_enable)
                            playsound_enable =true;
                        else
                            playsound_enable=false;
                        if(json.company.name && $('.lc_company_info .company-name').html()!=json.company.name)
                            $('.lc_company_info .company-name').html(json.company.name+'<span class="title_class"></span>');
                        if(json.company.logo && json.company.logo!=$('.lc_company_info .lc_company_logo img').attr('src'))
                            $('.lc_company_info .lc_company_logo img').attr('src',json.company.logo);    
                        if(json.isAdminOnline)
                        {
                            if(json.isAdminOnline=='online')
                            {
                                if(!$('.lc_chatbox').hasClass('lc_admin_online'))
                                    $('.lc_chatbox').removeClass('lc_admin_offline').removeClass('lc_admin_do_not_disturb').removeClass('lc_admin_invisible').addClass('lc_admin_online');
                                $('.lc_company_info .title_class').attr('title',online_text);
                            }
                            if(json.isAdminOnline=='do_not_disturb')
                            {
                                if(!$('.lc_chatbox').hasClass('lc_admin_do_not_disturb'))
                                    $('.lc_chatbox').removeClass('lc_admin_offline').removeClass('lc_admin_online').removeClass('lc_admin_invisible').addClass('lc_admin_do_not_disturb');
                                $('.lc_company_info .title_class').attr('title',busy_text);
                            }
                            if(json.isAdminOnline=='invisible')
                            {
                                if(!$('.lc_chatbox').hasClass('lc_admin_invisible'))
                                    $('.lc_chatbox').removeClass('lc_admin_offline').removeClass('lc_admin_online').removeClass('lc_admin_do_not_disturb').addClass('lc_admin_invisible');
                                $('.lc_company_info .title_class').attr('title',invisible_text);
                            }
                        }
                        else
                        {
                            if(!$('.lc_chatbox').hasClass('lc_admin_offline'))
                                $('.lc_chatbox').removeClass('lc_admin_online').removeClass('lc_admin_online').removeClass('lc_admin_do_not_disturb').removeClass('lc_admin_invisible').addClass('lc_admin_offline');
                            $('.lc_company_info .title_class').attr('title',offline_text);
                        }
                        if(json.employee_message_deleted)
                        {
                            var employee_message_deleted=json.employee_message_deleted.split(',');
                            $.each(employee_message_deleted,function(i,id){
                                if($('li.lc_msg[data-id-message="'+id+'"]').length)
                                    $('li.lc_msg[data-id-message="'+id+'"]').remove();
                            });
                        }
                        var employee_message_edited =json.employee_message_edited;
                        if(employee_message_edited)
                        {
                            $.each(employee_message_edited,function(i,msg){
                                if($('li.lc_msg[data-id-message="'+msg.id_message+'"]').length)
                                {
                                   $('li.lc_msg[data-id-message="'+msg.id_message+'"] .lc_msg_content').html(msg.message);
                                   if($('li.lc_msg[data-id-message="'+msg.id_message+'"] .lc_msg_edited').length)
                                        $('li.lc_msg[data-id-message="'+msg.id_message+'"] .lc_msg_edited').remove();
                                   $('li.lc_msg[data-id-message="'+msg.id_message+'"]').append('<div class="lc_msg_edited">'+(!ETS_LC_DISPLAY_TIME?edited_text:edited_at_text+' '+msg.datetime_edited)+'</div>'); 
                                }
                            });
                        }
                        if(json.isEmployeeSeen)
                        {
                            $('.message_status li').removeClass('show');
                            $('.seen_employee').addClass('show');
                        }
                        else
                        {
                            if(json.isEmployeeDelivered)
                            {
                                $('.message_status li').removeClass('show');
                                $('.delivered_employee').addClass('show');
                            }
                            else
                            {
                                if(json.isEmployeeSent)
                                {
                                    $('.message_status li').removeClass('show');
                                    $('.sent_employee').addClass('show');
                                }
                            }
                        }
                        if(json.isEmployeeWriting || json.lastMessageIsEmployee)
                        {
                            if(json.isEmployeeWriting)
                                $('.writing_employee').addClass('show');
                            else
                                $('.writing_employee').removeClass('show'); 
                            $('.message_status').hide();
                        }
                        else
                        {
                            $('.writing_employee').removeClass('show');
                            if(json.end_chat!=0 || !json.isRequestAjax)
                            {
                                $('.message_status').hide();
                            }
                            else
                            {
                                $('.message_status').show();
                            }
                                
                        }
                        if(json.end_chat!=0 || !json.isRequestAjax)
                        {
                            $('.lc_message_end_chat').show();
                            $('.writing_employee').removeClass('show');
                            if(json.end_chat!=0)
                            {
    
                                $('.employee_end').html(json.end_chat);
                                $('.employee_end').show();
                                $('.customer_end').hide();
                            }
                            else
                            {
                                $('.employee_end').hide();
                                if(json.isRequestAjax==0)
                                    $('.customer_end').show();
                                else
                                    $('.customer_end').hide();
                                
                            }
                            
                                
                        }    
                        
                        if(json.count_message_not_seen)
                        {
                            $('.lc_chatbox').addClass('has_new_message');
                            $('.lc_chatbox .lc_heading_count_message_not_seen').addClass('show');
                            $('.lc_chatbox .lc_heading_count_message_not_seen').html(json.count_message_not_seen);
                        }
                        else
                        {
                            $('.lc_chatbox').removeClass('has_new_message');
                            $('.lc_chatbox .lc_heading_count_message_not_seen').removeClass('show');
                            $('.lc_chatbox .lc_heading_count_message_not_seen').html(json.count_message_not_seen);
                        }
                        if(json.conversation_rate=='0' && $('.criterions_livechat input[name="criterion_livechat"]').val()!='0')
                        {
                            $('.criterions_livechat input[name="criterion_livechat"]').val('0');
                            $('.criterions_livechat .star').removeClass('star_on'); 
                        }
                    }
                    if(json.wait_support && !json.isAdminBusy )
                    {
                        if($('#clock_wait').html()=='')
                        {
                           livechat.startTimer(json.wait_support); 
                        }
                        $('.lc_chatbox').addClass('wait_support');
                    }
                    else
                       $('.lc_chatbox').removeClass('wait_support');
                    if(!json.messages && $('.lc_chatbox').hasClass('lc_chatbox_closed') && !$('.lc_chatbox').hasClass('lc_saved_chatbox_status') && !$('.lc_chatbox').hasClass('lc_saved_chatbox_status') && ETS_LC_AUTO_OPEN && (!ETS_LC_AUTO_OPEN_ONLINE_ONLY || ETS_LC_AUTO_OPEN_ONLINE_ONLY && $('.lc_chatbox').hasClass('lc_admin_online')))
                    {
                        if(!ETS_LC_AUTO_OPEN_CHATBOX_DELAY || ETS_LC_AUTO_OPEN_CHATBOX_DELAY<=0)
                        {
                            $('.lc_chatbox').removeClass('lc_chatbox_closed').addClass('lc_chatbox_open');
                            if (document.documentElement.clientWidth < 767) 
                                document.querySelector("meta[name=viewport]").setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
                        } 
                        else
                        {
                            setTimeout(function(){
                                $('.lc_chatbox').removeClass('lc_chatbox_closed').addClass('lc_chatbox_open');
                                if (document.documentElement.clientWidth < 767) 
                                    document.querySelector("meta[name=viewport]").setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
                            },ETS_LC_AUTO_OPEN_CHATBOX_DELAY*1000);
                        }
                    }
                    if($('.lc_customer_info input').length == $('.lc_customer_info input[disabled="disabled"]').length && $('.lc_update_info').length > 0)
                        $('.lc_update_info').remove(); 
                    $('input.star').rating();
                    $('.star a').each(function(){
                        $(this).after('<span>'+$(this).attr('title')+'</span>');
                        $(this).attr('title','');
                    });
                    //$('input[name="criterion_livechat"]').val('5');
                    //if(refresh && json.change_department==true && json.id_conversation)
                    //    livechat.changeDepartment(true);
                } 
                if(json.messages)
                {
                    livechat.processMessages(json.messages);
                    if(json.message_deleted)
                    {
                        var message_deleted=json.message_deleted.split(',');
                        $.each(message_deleted,function(i,id){
                            if($('li.lc_msg[data-id-message="'+id+'"]').length)
                                $('li.lc_msg[data-id-message="'+id+'"]').remove();
                        });
                    }
                    $('.lc_introduction').addClass('lc_hide');
                }
                //$('[data-toggle="tooltip"]').tooltip();   
                if(lc_ets_scroll)
                {
                    livechat.scrollLiveChat();
                } 
                livechat.lc_markdrap();                
            }
        });
    },
    actionEndChat : function(){
       $.ajax({
            url: ETS_LC_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                customer_end_chat: 1,
            },
            success: function(json)
            { 
                $('.lc_chatbox').removeClass('is_admin_busy').addClass('end_chat');
                livechat.displayEndChat();
            }
        });   
    },
    run: function()
    {
        livechat.openChatWindow(0);
        if(isRequestAjax!=0)
        {
            if(ETS_LC_TIME_OUT>0)
                autoloadchatbox = setInterval(livechat.openChatWindow, Math.ceil(ETS_LC_TIME_OUT*isRequestAjax),1);
            else
                autoloadchatbox = setInterval(livechat.openChatWindow, Math.ceil(3000*isRequestAjax),1);
        }
        $(document).on('click','textarea[name="message"]',function(){
            $('.lc_heading_count_message_not_seen').removeClass('show');
        }); 
        $(document).on('click','.customer_edit_message',function(){
            var id_message = $(this).attr('data-id-message');
            var $this=$(this);
            if(!autoloadchatbox)
            {
                if(ETS_LC_TIME_OUT>0)
                    autoloadchatbox = setInterval(livechat.openChatWindow, Math.ceil(ETS_LC_TIME_OUT*isRequestAjax),1);
                else
                    autoloadchatbox = setInterval(livechat.openChatWindow, Math.ceil(3000*isRequestAjax),1);
            }
            $('.lc_msg_editing').remove();
            $.ajax({
                url: ETS_LC_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    customer_edit_message: 1,
                    id_message: id_message,
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        alert(json.error);
                        $('.lc_text_area input[name="id_message"]').val('');
                    } 
                    else
                    {
                        if($('.lc_send').val()!=ETS_LC_TEXT_BUTTON_EDIT)
                                $('.lc_send').val(ETS_LC_TEXT_BUTTON_EDIT);
                        $('.lc_text_area input[name="id_message"]').val(id_message);
                        $('textarea[name="message"]').val(json.message);
                        $('textarea[name="message"]').focus();
                        $('textarea[name="lc_message_old"]').val(json.message);
                        if($('li.lc_msg[data-id-message="'+id_message+'"] .lc_msg_edited').length>0)
                            $('li.lc_msg[data-id-message="'+id_message+'"] .lc_msg_edited').hide();
                        if($('li.lc_msg[data-id-message="'+id_message+'"] .lc_msg_editing').length<=0)     
                            $('li.lc_msg[data-id-message="'+id_message+'"]').append('<div class="lc_msg_editing" title="'+editing_text+'">'+editing_text+'<span></span><span></span><span></span></div>');
                        
                    }
                }
            });
        });
        $(document).on('keyup','body',function(e){
            if(e.keyCode == 27) {
               if($('.lc_chatbox').hasClass('lc_chatbox_maximize'))
               {
                    $('.lc_chatbox').removeClass('lc_chatbox_maximize');
                    $('.lc_messages').css('height','');
                    $('.lc_customer_info').css('height','');
                    $('.lc_maximize').attr('title',maximize_text);
                    $('body').removeClass('lc_no_scroll');
               }
            }
        });

        $(document).on('click','.customer_delete_message',function(){
            var id_message = $(this).attr('data-id-message');
            var $this=$(this);
            if(!autoloadchatbox)
            {
                if(ETS_LC_TIME_OUT>0)
                    autoloadchatbox = setInterval(livechat.openChatWindow, Math.ceil(ETS_LC_TIME_OUT*isRequestAjax),1);
                else
                    autoloadchatbox = setInterval(livechat.openChatWindow, Math.ceil(3000*isRequestAjax),1);
            }
            $.ajax({
                url: ETS_LC_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    customer_delete_message: 1,
                    id_message: id_message,
                },
                success: function(json)
                { 
                    if(json.error)
                        alert(json.error);
                    else
                    {
                        $this.closest('.is_customer').remove();
                        if(id_message==$('.lc_text_area input[name="id_message"]').val())
                        {
                            $('.lc_text_area input[name="id_message"]').val('');
                            if($('.lc_send').val()!=ETS_LC_TEXT_SEND)
                                $('.lc_send').val(ETS_LC_TEXT_SEND);
                        }    
                    }
                        
                }
            });
        });
        $(document).on('click','.lc_send,.lc_update_info',function(){
            var $this= $(this);
            $('input[name="message_writing"]').val('0');
            if(($('.lc_text_area textarea[name="message"]').val().trim()=='' && $('input[name="message_file"]').val()=='' && $(this).hasClass('lc_send'))||($('.lc_text_area textarea[name="message"]').val().trim()==$('.lc_text_area textarea[name="lc_message_old"]').val().trim() && $('input[name="message_file"]').val()=='' && $('.lc_text_area input[name="id_message"]').val()))
            {  
                if($('.lc_text_area input[name="id_message"]').val())
                {
                    if($('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"] .lc_msg_editing').length)
                        $('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"] .lc_msg_editing').remove();
                    if($('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"] .lc_msg_edited').length)
                        $('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"] .lc_msg_edited').show();
                    $('.lc_text_area input[name="id_message"]').val('');
                    $('.lc_text_area textarea[name="lc_message_old"]').val('');
                    $('.lc_text_area textarea[name="message"]').val('');
                    if($('.lc_send').val()!=ETS_LC_TEXT_SEND)
                        $('.lc_send').val(ETS_LC_TEXT_SEND);
                }
                return false;
            } 
            if(parseInt($('.lc_chatbox').attr('data-id-conversation'))!=0 || parseInt($('.lc_chatbox').attr('data-id-conversation'))==0 && !$('.lc_chatbox').hasClass('lc_loading'))
            {
                if((parseInt($('.lc_chatbox').attr('data-id-conversation'))==0|| !$('.lc_customer_info').hasClass('closed')) && !$('.lc_chatbox').hasClass('lc_loading'))
                    $('.lc_chatbox').addClass('lc_loading');
                $('.lc_error').hide();
                $('.lc_error_start_chat').hide();
                $('.lc_error_start_chat_c').hide();
                $('.lc_message_end_chat').hide();
                if($('.lc_loading_send').length)
                {
                   $('.lc_loading_send').addClass('show');
                   $('textarea[name="message"]').attr('disabled','disabled'); 
                }           
                var formData = new FormData($(this).parents('form').get(0));
                formData.append('send_message', 1);
                formData.append('name', $('#lc_customer_name').length > 0 ?  $('#lc_customer_name').val() : '');
                formData.append('email', $('#lc_customer_email').length > 0 ?  $('#lc_customer_email').val() : '');
                formData.append('phone', $('#lc_customer_phone').length > 0 ?  $('#lc_customer_phone').val() : '');
                formData.append('message', $('.lc_text_area textarea[name="message"]').length > 0 ?  $('.lc_text_area textarea[name="message"]').val().trim() : '');
                formData.append('latestID', $('.lc_msg_board .lc_msg').length > 0 ? $('.lc_msg_board .lc_msg:last-child').attr('data-id-message') : 0);
                formData.append('updateCustomerInfo', $(this).hasClass('lc_update_info') && parseInt($('.lc_chatbox').attr('data-id-conversation'))!=0 ? 1 : 0);
                formData.append('captcha', $('.lc_captcha').hasClass('active') ? $('.lc_captcha_input').val() : '');
                formData.append('browser_name',livechat.get_browser_info());
                formData.append('id_message', $('.lc_text_area input[name="id_message"]').val());
                //formData.append('id_departments', $('#id_departments').val());
                formData.append('send_product_id', $('#send_product_id').length && $('#send_product_id:checked').length ? $('#send_product_id').val():0);
                formData.append('message_writing', 0);
                formData.append('lc_conversation_end_chat', $('#lc_conversation_end_chat').val());
                formData.append('current_url', window.location.href);
                formData.append('product_page_product_id', $('body#product').length >0 && $('#product_page_product_id').length >0 ? $('#product_page_product_id').val() :0);
                $('textarea[name="message"]').val('');
                $('textarea[name="message"]').focus();  
                $('.form_upfile_val').remove();
                $('input[name="message_file"]').val('');  
                $.ajax({
                    url: ETS_LC_URL_AJAX,
                    type: 'post',
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(json)
                    { 
                        if(json.error)
                        {
                            if($('.lc_loading_send').length)
                            {
                               $('.lc_loading_send').removeClass('show');
                            }
                            $('textarea[name="message"]').prop('disabled', false);
                            if($('.lc_msg_board li.lc_msg').length>0)
                            {
                                $('.lc_error').html(json.error);
                                $('.lc_error').show();
                            }
                            else
                            {
                                if($('.lc_error_start_chat_c .lc_warning_error').length>0)
                                {
                                    if($('.lc_error_start_chat_c .lc_warning_error').hasClass('show'))
                                    {
                                        $('.lc_error_start_chat_c').remove();
                                        $('.lc_error_start_chat').html(json.error);
                                        $('.lc_error_start_chat').show();
                                    }
                                    else
                                    {
                                        $('.lc_error_start_chat_c .lc_warning_error').addClass('show');
                                        $('.lc_customer_info_form > div > input:first').focus();
                                        $('.lc_error_start_chat_c').show();
                                        $('.lc_introduction').addClass('lc_hide');
                                        $('.lc_customer_info').removeClass('closed');
                                    }
                                        
                                }
                                else
                                {
                                    $('.lc_error_start_chat').html(json.error);
                                    $('.lc_error_start_chat').show();
                                }
                                $('.lc_chatbox').removeClass('lc_loading');
                            }
                            return false;
                        }
                        $('.lc_chatbox').removeClass('end_chat');
                        $('#id_departments').attr('disabled','disabled');
                        if(json.lc_conversation_end_chat)
                        {
                            if(ETS_LC_DISPLAY_RATING)
                                $('.lc_chatbox').addClass('end_chat_rate');
                            else
                                $('.lc_chatbox').addClass('end_chat');
                            $('textarea[name="message"]').val('');
                            $('.form_upfile_val').remove();
                            $('input[name="message_file"]').val('');
                            $('textarea[name="message"]').focus();
                            if($('.lc_loading_send').length)
                            {
                               $('.lc_loading_send').removeClass('show');
                               $('textarea[name="message"]').removeAttr('disabled'); 
                            }
                            livechat.displayEndChat();
                            return true;
                        }   
                        else
                            $('#lc_conversation_end_chat').val(''); 
                        var lc_ets_scroll=false;
                        if(ETS_DISPLAY_SEND_BUTTON && !$('.lc_text_area').hasClass('show_send_box'))
                            $('.lc_text_area').addClass('show_send_box');
                        if($('.lc_messages').scrollTop() + $('.lc_messages').height()+3 >= $('.lc_messages_table-cell').height()) {
                               lc_ets_scroll=true;
                        }
                        $('.message_status').show();
                        if(lc_ets_scroll)
                        {
                            livechat.scrollLiveChat();
                        }
                        if(json.isEmployeeSeen)
                        {
                            $('.message_status li').removeClass('show');
                            $('.seen_employee').addClass('show');
                        }
                        else
                        {
                           
                            if(json.isEmployeeDelivered)
                            {
                                $('.message_status li').removeClass('show');
                                $('.delivered_employee').addClass('show');
                            }
                            else
                            {
                                if(json.isEmployeeSent)
                                {
                                    
                                    $('.message_status li').removeClass('show');
                                    $('.sent_employee').addClass('show');
                                }
                            }
                        }
                        if(json.isEmployeeWriting || json.lastMessageIsEmployee)
                        {
                            if(json.isEmployeeWriting)
                                $('.writing_employee').addClass('show');
                            else
                                $('.writing_employee').removeClass('show'); 
                            $('.message_status').hide();
                        }
                        else
                        {
                            $('.writing_employee').removeClass('show');
                            $('.message_status').show();
                                
                        }
                        if($('.lc_chatbox').hasClass('lc_loading'))
                        {
                            if(!json.error)
                            {
                                $('.lc_text_area textarea[name="message"]').val('');
        
                            }
                            $('.lc_chatbox').removeClass('lc_loading');    
                        }                        
                        if(json.isAdminOnline)
                        {
                            if(json.isAdminOnline=='online')
                            {
                                if(!$('.lc_chatbox').hasClass('lc_admin_online'))
                                    $('.lc_chatbox').removeClass('lc_admin_offline').removeClass('lc_admin_do_not_disturb').removeClass('lc_admin_invisible').addClass('lc_admin_online');
                            }
                            if(json.isAdminOnline=='do_not_disturb')
                            {
                                if(!$('.lc_chatbox').hasClass('lc_admin_do_not_disturb'))
                                    $('.lc_chatbox').removeClass('lc_admin_offline').removeClass('lc_admin_online').removeClass('lc_admin_invisible').addClass('lc_admin_do_not_disturb');
                            }
                            if(json.isAdminOnline=='invisible')
                            {
                                if(!$('.lc_chatbox').hasClass('lc_admin_invisible'))
                                    $('.lc_chatbox').removeClass('lc_admin_offline').removeClass('lc_admin_online').removeClass('lc_admin_do_not_disturb').addClass('lc_admin_invisible');
                            }
                        }
                        else
                        {
                            if(!$('.lc_chatbox').hasClass('lc_admin_offline'))
                                $('.lc_chatbox').removeClass('lc_admin_invisible').removeClass('lc_admin_online').removeClass('lc_admin_do_not_disturb').addClass('lc_admin_offline');
                        }
                        if(json.openInfo)
                        {
                            $('.lc_messages').addClass('lc_hide');
                            $('.lc_text_area').addClass('lc_hide');
                            $('.lc_customer_info').removeClass('closed');
                        }
                        else if(!$('.lc_customer_info').hasClass('closed'))
                        {
                            $('.lc_customer_info').addClass('closed'); 
                        }                                                          
                        if(json.error)
                        {
                            if($('.lc_loading_send').length)
                            {
                               $('.lc_loading_send').removeClass('show');
                            }
                            $('textarea[name="message"]').prop('disabled', false);
                            if($('.lc_msg_board li.lc_msg').length>0)
                            {
                                $('.lc_error').html(json.error);
                                $('.lc_error').show();
                            }
                            else
                            {
                                if($('.lc_error_start_chat_c .lc_warning_error').length>0)
                                {
                                    if($('.lc_error_start_chat_c .lc_warning_error').hasClass('show'))
                                    {
                                        $('.lc_error_start_chat_c').remove();
                                        $('.lc_error_start_chat').html(json.error);
                                        $('.lc_error_start_chat').show();
                                    }
                                    else
                                    {
                                        $('.lc_error_start_chat_c .lc_warning_error').addClass('show');
                                        $('.lc_customer_info_form > div > input:first').focus();
                                        $('.lc_error_start_chat_c').show();
                                        $('.lc_introduction').addClass('lc_hide');
                                        $('.lc_customer_info').removeClass('closed');
                                    }
                                        
                                }
                                else
                                {
                                    $('.lc_error_start_chat').html(json.error);
                                    $('.lc_error_start_chat').show();
                                }
                                
                            }
                            
                        }  
                        else
                        {
                            $('.lc_customer_info').addClass('closed');
                            $('.lc_messages').removeClass('lc_hide');
                            $('.lc_text_area').removeClass('lc_hide');
                            $('#id_departments').removeClass('changed');
                            if($('.block_admin_accpeted').length)
                                $('.block_admin_accpeted').remove();
                            if(!autoloadchatbox)
                            {
                                if(ETS_LC_TIME_OUT>0)
                                    autoloadchatbox = setInterval(livechat.openChatWindow, Math.ceil(ETS_LC_TIME_OUT),1);
                                else
                                    autoloadchatbox = setInterval(livechat.openChatWindow, 3000,1);
                            }
                            $('.lc_customer_name,.lc_customer_email,.lc_customer_phone').removeClass('lc_hide');
                            if(json.messages)
                            {
                                if(json.wait_support && !json.isAdminBusy)
                                {
                                    if($('#clock_wait').html()=='')
                                    {
                                       livechat.startTimer(json.wait_support); 
                                    }
                                    $('.lc_chatbox').addClass('wait_support');
                                }
                                else
                                   $('.lc_chatbox').removeClass('wait_support');
                                if(json.isAdminBusy)
                                {
                                    $('.lc_chatbox').addClass('is_admin_busy');
                                    $('.lc_chatbox .block_admin_busy').html(text_admin_busy+'<button class="lc_customer_end_chat">'+text_customer_end_chat+'</button>');
                                }
                                else
                                    $('.lc_chatbox').removeClass('is_admin_busy');
                                livechat.processMessages(json.messages); 
                                $('.lc_introduction').addClass('lc_hide');
                            }
                            if($('.lc_loading_send').length)
                            {
                               $('.lc_loading_send').remove();
                            }
                            $('textarea[name="message"]').prop('disabled', false);
                            $('.lc_text_area').removeClass('start_chating');
                            if($('.lc_product_send').length)
                                $('.lc_product_send').remove();
                            if($('.lc_send').val()!=ETS_LC_TEXT_SEND)
                                $('.lc_send').val(ETS_LC_TEXT_SEND);
                            if($('.lc_text_area input[name="id_message"]').val()!='' && json.message_edited!='')
                            {
                                $('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"] .lc_msg_content').html(json.message_edited.message);
                                if($('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"] .lc_msg_edited').length)
                                    $('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"] .lc_msg_edited').remove();
                                $('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"]').append('<div class="lc_msg_edited">'+(!ETS_LC_DISPLAY_TIME?edited_text:edited_at_text+' '+json.message_edited.datetime_edited)+'</div>');
                                if($('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"] .lc_msg_editing').length)
                                    $('li.lc_msg[data-id-message="'+$('.lc_text_area input[name="id_message"]').val()+'"] .lc_msg_editing').remove();    
                                $('.lc_text_area input[name="id_message"]').val('');
                            }
                            if($('.lc_chatbox').hasClass('lc_admin_offline') && $('.lc_thankyou').length > 0 && !json.updateContactInfo)
                            {
                                $('.lc_chatbox').addClass('lc_display_thank');
                                if (document.documentElement.clientWidth < 767)
                                    {
                                        window.scrollTo(0,0);                                     
                                    }
                            }
                            if($('.lc_captcha').hasClass('active'))
                            {
                                $('.lc_text_area textarea[name="message"]').val('');                           
                            }
                            $('.lc_messages').removeClass('lc_hide');
                            $('.lc_sound').removeClass('lc_hide');
                            $('.criterion_contact').removeClass('contact_start_chat');
                            $('.lc_customer_info_toggle').removeClass('lc_hide');
                            $('.lc_update_info').removeClass('lc_hide');
                            if($('.criterions_livechat').hasClass('lc_hide'))
                            {
                                $('.criterions_livechat').removeClass('lc_hide');
                                $('.criterion_contact').removeClass('no_rate_customer');
                            }
                            
                        }
                        if($('.lc_captcha').hasClass('active'))
                            $('.lc_captcha_input').val('');
                        if((json.id_conversation && parseInt(json.id_conversation)!=parseInt($('.lc_chatbox').attr('data-id-conversation')) || json.updateContactInfo) && !json.error && !ETS_LC_UPDATE_CONTACT_INFO)
                        {
                            $('.lc_customer_info input[type="text"]').attr('disabled','disabled');
                            $('.lc_update_info').remove();
                        }
                        if(json.id_conversation && parseInt(json.id_conversation)!=parseInt($('.lc_chatbox').attr('data-id-conversation')))
                            $('.lc_chatbox').attr('data-id-conversation',json.id_conversation);
                        if(json.captcha)
                        {
                            if($('.lc_captcha_img').length > 0)
                                $('.lc_captcha_img').attr('src',json.captcha);
                            else
                                $('.lc_captcha').prepend('<img class="lc_captcha_img" src="'+json.captcha+'"/>');
                            $('.lc_captcha').addClass('active');
                            $('.lc_text_area').addClass('show_capcha');
                        }
                        else
                        {
                            $('.lc_captcha').removeClass('active');
                            $('.lc_text_area').removeClass('show_capcha');
                        }
                        livechat.resizeFormChat();    
                    },
                    error: function()
                    {     
                        if($('.lc_loading_send').length)
                        {
                           $('.lc_loading_send').removeClass('show');
                        }
                        $('textarea[name="message"]').prop('disabled', false);
                        $('.lc_chatbox').removeClass('lc_loading');
                        if($('.lc_msg_board li.lc_msg').length>0)
                        {
                            $('.lc_error').html('<div class="bootstrap"><div class="module_error alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><ul><li>Please type your message</li></ul></div></div>');
                            $('.lc_error').show();
                        }
                        else
                        {
                            $('.lc_error_start_chat').html('<div class="bootstrap"><div class="module_error alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><ul><li>Please type your message</li></ul></div></div>');
                            $('.lc_error_start_chat').show();
                        }
                    }
                });
                if(($('.lc_customer_info').length <= 0 || $('.lc_customer_info').hasClass('closed')) && !$(this).hasClass('lc_update_info') && !$('.lc_captcha').hasClass('active'))
                {
                    if($('.lc_msg_board li.lc_msg').length>0)
                        $('.lc_text_area textarea[name="message"]').val('');
                }
            }            
            return false;
        });
        $(document).on('click','.lc_send_another_msg',function(){
            $('.lc_chatbox').removeClass('lc_display_thank');
            livechat.scrollLiveChat();
        });
        $(document).on('click','.lc_sound',function(e){
            e.stopPropagation();
            var $this=$(this);
            $.ajax({
                url: ETS_LC_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    change_sound_conversation: 1,
                    status: $this.hasClass('enable')?0:1,
                },
                success: function(json)
                { 
                    if($this.hasClass('enable'))
                    {
                        $this.removeClass('enable').addClass('disable');
                        $this.attr('title',enable_sound_text);
                    }
                    else{
                        $this.removeClass('disable').addClass('enable');
                        $this.attr('title',disable_sound_text);
                    }
                }
            });
        });
        $(document).on('click','.more_load',function(){
           livechat.getOldMessages(); 
        });
        $(document).mouseup(function (e)
        {
                var container = $('body > .lc_chatbox');
                var container_message=$('.lc_chatbox textarea[name="message"]');
                if (!container.is(e.target)&& container.has(e.target).length === 0)
                {
                    $('body > .lc_chatbox').find('input[name="message_seen"]').val('0');
                }
                else
                {
                    $('body > .lc_chatbox').find('input[name="message_seen"]').val('1');
                }
                if (!container_message.is(e.target)&& container_message.has(e.target).length === 0)
                {
                    $('body > .lc_chatbox').find('input[name="message_writing"]').val('0');
                }
                var container_emotion = $('.lc_emotion');
                if (!container_emotion.is(e.target)&& container_emotion.has(e.target).length === 0)
                {
                    $('.lc_emotion').removeClass('show');
                }
        }); 
        $(document).on('keypress','.lc_chatbox textarea[name="message"]',function(e){
            $('body > .lc_chatbox').find('input[name="message_writing"]').val('1');
            if(e.which == 13) {
               $('body > .lc_chatbox').find('input[name="message_writing"]').val('0'); 
            }
        });
        if(ETS_LC_ENTER_TO_SEND)
        {
            $(document).keypress(function(e) {
                if(e.which == 13) {
                    if((!$('.lc_captcha').hasClass('active') || $('.lc_captcha_input').val().trim()!='') && $('.lc_text_area textarea[name="message"]').length > 0 && $('.lc_text_area textarea[name="message"]').is(':focus'))
                    {
                        $('.lc_send').click();
                        return false;   
                    }
                    else
                        if($('.lc_text_area textarea[name="message"]').length > 0 && $('.lc_text_area textarea[name="message"]').is(':focus'))
                            return false;
                }
            });
        }
        $(document).on('click','.lc_maximize',function(e){
           e.stopPropagation(); 
           var lc_window_height = $(window).height();
           var lc_text_area_height = $('.lc_text_area').height();
           var criterion_contact_height = $('.criterion_contact').height();
           var lc_company_info_height = $('.lc_company_info').height();
           var lc_messenger_box_height = '';
           if(!$(this).closest('.lc_chatbox').hasClass('lc_chatbox_maximize'))
           {
                $(this).closest('.lc_chatbox').addClass('lc_chatbox_maximize');
                $('body').addClass('lc_no_scroll');
                $(this).attr('title',minimize_text);
           } 
           else
           {
                $(this).closest('.lc_chatbox').removeClass('lc_chatbox_maximize');
                $('body').removeClass('lc_no_scroll');
                $(this).attr('title',maximize_text);
           }
           livechat.resizeFormChat();
                
        });

        
        $(document).on('click','.lc_heading',function(){
            if($('.lc_chatbox').hasClass('lc_chatbox_closed'))
            {
                $('.lc_chatbox').removeClass('lc_chatbox_closed').addClass('lc_chatbox_open');
                $('.lc_heading').attr('title','');
                if (document.documentElement.clientWidth < 767)
                {
                    document.querySelector("meta[name=viewport]").setAttribute('content','width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
                    $('.lc_chatbox').addClass('lc_chatbox_maximize');
                    livechat.resizeFormChat();
                }
                livechat.scrollLiveChat();
                $('#message_seen').val(1);
                $('.lc_heading_count_message_not_seen').removeClass('show');
                if($('.lc_chatbox').hasClass('lc_chatbox_maximize'))
                    $('body').addClass('lc_no_scroll');
                $('.lc_chatbox').css('bottom','');
                $('.lc_chatbox').css('left','');
                $('.lc_chatbox').css('top','');
                $.ajax({
                    url: ETS_LC_URL_AJAX,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        set_chatbox_status: 1,
                        status: $('.lc_chatbox').hasClass('lc_chatbox_closed') ? 'closed' : 'open',
                    },
                    success: function(json)
                    { 
                    }
                });
            }
            livechat.lc_markdrap();
        });  
        $(document).on('click','.lc_minimize',function(e){
            e.stopPropagation();
            if(!$('.lc_chatbox').hasClass('lc_chatbox_closed'))
            {
                $('.lc_chatbox').removeClass('lc_chatbox_open').addClass('lc_chatbox_closed');
                $('.lc_heading').attr('title',show_text);
                if (document.documentElement.clientWidth < 767)
                {
                    document.querySelector("meta[name=viewport]").setAttribute('content','width=device-width, initial-scale=1.0, maximum-scale=1.2, user-scalable=1');
                } 
                $('#message_seen').val(0);
                $('body').removeClass('lc_no_scroll');
                $('.lc_chatbox').css('bottom','');
                $('.lc_chatbox').css('left','');
                $('.lc_chatbox').css('top','');
                $.ajax({
                    url: ETS_LC_URL_AJAX,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        set_chatbox_status: 1,
                        status: $('.lc_chatbox').hasClass('lc_chatbox_closed') ? 'closed' : 'open',
                    },
                    success: function(json)
                    {
                        
                    }
                });
            }
        });
        $(document).on('click','.lc_close',function(e){
            if($(this).hasClass('lc_form_submit_close'))
                return false;
            e.stopPropagation();
            $('.lc_chatbox').remove();
            $('body').removeClass('lc_no_scroll');
            if (document.documentElement.clientWidth < 767) 
                document.querySelector("meta[name=viewport]").setAttribute('content','width=device-width, initial-scale=1.0, maximum-scale=1.2, user-scalable=1');
        });    
        $(document).on('click','.lc_customer_info_toggle',function(e){
            e.stopPropagation();
            if($('.lc_customer_info').hasClass('closed'))
            {
                $('.lc_customer_info').removeClass('closed');
                $('.lc_chatbox').addClass('show_customer_info');
                $('.lc_messages').addClass('lc_hide');
                $('.lc_text_area').addClass('lc_hide');
            } 
            else
            {
                $('.lc_customer_info').addClass('closed');
                $('.lc_chatbox').removeClass('show_customer_info');
                $('.lc_messages').removeClass('lc_hide');
                $('.lc_text_area').removeClass('lc_hide');
            }
            livechat.resizeFormChat();
                
        }); 
        $(document).on('click','.lc_captcha_refesh',function(){
            if($('.lc_captcha_img').length > 0)
                $('.lc_captcha_img').attr('src',$(this).attr('data-captcha-img')+'&rand='+Math.random());
        });
        if (document.documentElement.clientWidth < 767)
        {
            $(document).on('click','.lc_emotion',function(e){
                if ($(this).hasClass('show')){
                    $(this).removeClass('show'); 
                } else {
                    $(this).addClass('show');
                }
            });
        }
        $(document).on('click','.lc_emotion li',function(e){
            e.stopPropagation();
            var message = $('textarea[name="message"]').val();
            message +=$(this).attr('data-emotion');
            $('textarea[name="message"]').val(message);
            $('textarea[name="message"]').focus();
            if (document.documentElement.clientWidth < 767)
            {
                $('.lc_emotion').removeClass('show');
            }
        });
        $(document).on('click','.no-rate',function(){
            if($('.lc_chatbox').hasClass('end_chat_rate'))
            {
                $('.lc_chatbox').removeClass('end_chat_rate').addClass('end_chat');
                livechat.displayEndChat();
            }
        });
        $(document).on('click','.criterions_livechat .star',function(){
           $.ajax({
                url: ETS_LC_URL_AJAX,
                type: 'post',
                data: {
                    set_rating: 1,
                    rating:$('input[name="criterion_livechat"]').val(),
                    browser_name : livechat.get_browser_info(),
                    current_url: window.location.href,
                },
                success: function(json)
                {       
                    if($('.lc_chatbox').hasClass('end_chat_rate'))
                    {
                        $('.lc_chatbox').removeClass('end_chat_rate').addClass('end_chat');
                        livechat.displayEndChat();
                    }
                }
            }); 
        });
    },
    processMessages: function(messages)
    {
        if(messages)
        {
            $.each(messages,function(i,msg){
                var msgHtml = '<li class="lc_msg '+(msg.id_employee != 0 ? 'is_employee'+(msg.employee_name?' has_name_emplode':'') : 'is_customer'+(msg.customer_name?' has_name_customer':''))+'" data-id-message="'+msg.id_message+'">'
                +'<div class="lc_sender">'+(msg.id_employee != 0 ? (msg.employee_avata?'<div class="avata'+(ETS_LC_AVATAR_IMAGE_TYPE=='square'?' lc_avatar_square':'')+'"><img src="'+msg.employee_avata+'" title="'+msg.employee_name+'"></div>':'')+(msg.employee_name?'<span title="'+msg.employee_name+'">'+msg.employee_name+'</span>':''): (msg.customer_avata?'<div class="avata'+(ETS_LC_AVATAR_IMAGE_TYPE=='square'?' lc_avatar_square':'')+'"><img src="'+msg.customer_avata+'" title="'+msg.customer_name+'"></div>':'')+(msg.customer_name?'<span title="'+msg.customer_name+'">'+msg.customer_name+'</span>':''))+'</div>'                
                +(ETS_LC_DISPLAY_TIME ? '<div class="lc_msg_time">'+msg.datetime_added+'</div>' : '')
                +'<div class="lc_msg_content">'+msg.message+'</div>'
                +(msg.id_employee==0 && (ETS_LC_ENABLE_EDIT_MESSAGE||ETS_LC_ENABLE_DELETE_MESSAGE)?'<div class="lc_action_message">'+(ETS_LC_ENABLE_DELETE_MESSAGE?'<span title="'+delete_text+'" class="customer_delete_message" data-id-message="'+msg.id_message+'">'+delete_text+'</span>':'')+(ETS_LC_ENABLE_EDIT_MESSAGE?'<span title="'+edit_text+'" class="customer_edit_message" data-id-message="'+msg.id_message+'">'+edit_text+'</span>':'')+'</div>':'')
                +(msg.edited==1?'<div class="lc_msg_edited">'+(!ETS_LC_DISPLAY_TIME?edited_text:edited_text+' '+msg.datetime_edited)+'</div>':'')
                +'</li>';    
//                if(msg.id_employee != 0 && msg.employee_avata && msg.employee_avata!=$('.lc_company_logo img').attr('src'))
//                {
//                   $('.lc_company_logo img').attr('src',msg.employee_avata); 
//                }
//                if(msg.id_employee != 0 && msg.employee_name && msg.employee_name!=$('.lc_company_info .company-name').html())
//                {
//                   $('.lc_company_info .company-name').html(msg.employee_name+'<span class="title_class"></span>'); 
//                }        
                if($('.lc_msg_board > .lc_msg').length <= 0)
                {
                    $('.lc_msg_board').append(msgHtml);
                    if(msg.id_employee!=0)
                    {
                        livechat.playSound();
                        $('.block_admin_accpeted').remove();
                    }
                } 
                else if($('.lc_msg_board > .lc_msg[data-id-message="'+msg.id_message+'"]').length <= 0)
                {
                    var msgAdded = false;
                    $($('.lc_msg_board > .lc_msg').get().reverse()).each(function(){                        
                        if(parseInt($(this).attr('data-id-message')) < parseInt(msg.id_message))
                        {
                            $(this).after(msgHtml);
                            if(msg.id_employee!=0)
                            {
                                livechat.playSound();
                                $('.block_admin_accpeted').remove();
                            }
                            msgAdded = true;
                            return false;   
                        }
                    });
                    if(!msgAdded)
                    {
                        $('.lc_msg_board').prepend(msgHtml);
                        //if(msg.id_employee!=0)
//                        {
//                            livechat.playSound();
//                        }
                    }
                            
                }                    
            });   
        } 
    },
    scrollLiveChat: function()
    {
        if($('.lc_msg_board .lc_msg').length)
            $(".lc_messages").stop().animate({scrollTop: $('.lc_messages').scrollTop() + $('.lc_msg_board .lc_msg:last-child').position().top+20});
    },
    resizeFormChat:function()
    {
        if($('.lc_messages .lc_msg_board .lc_msg').length)
        {
            var height_window= $(window).height();
            var height_lc_message = height_window-$('div.lc_heading').height()-$('.lc_company_info').height() -$('.criterion_contact').height();
            //if(!$('.lc_introduction').hasClass('lc_hide'))
//            {
//                alert('lc_introduction='+$('.lc_introduction').height());
//            }    
            if($('.lc_error_start_chat:not(:hidden)').length)
            {
                height_lc_message -= $('.lc_error_start_chat').height();
            }
            if($('.lc_error_start_chat_c:not(:hidden)').length)
            {
                height_lc_message -= $('.lc_error_start_chat_c').height();
            } 
            if($('.lc_error:not(:hidden)').length)
            {
                height_lc_message -= $('.lc_error').height();
            }
            if(!$('.lc_text_area').hasClass('lc_hide'))
                height_lc_message -=$('.lc_text_area').height();  
            if($('.blok_wait_support:not(:hidden)').length)
            {
                height_lc_message -= $('.blok_wait_support').height();
            }
            if($('.block_admin_accpeted:not(:hidden)').length)
                height_lc_message -= $('.block_admin_accpeted').height();
            if($('.block_admin_busy:not(:hidden)').length)
            {
                height_lc_message -= $('.block_admin_busy').height();
            }
            if($('.lc_chatbox').hasClass('lc_chatbox_maximize'))
            {
                $('.lc_messages').css('height',height_lc_message+'px');
                if(!$('.lc_customer_info').hasClass('closed'))
                {
                    $('.lc_customer_info').css('height',height_lc_message+'px');
                    $('.lc_messages').css('height','');
                }  
                else
                {
                    $('.lc_messages').css('height',height_lc_message+'px');
                    $('.lc_customer_info').css('height','');
                }
                    
            }
            else
            {
                $('.lc_messages').css('height','');
                $('.lc_customer_info').css('height','');
            } 
        }
        
    },
    authPopup : function (provider)
    {
        if (ETS_LC_URL_OAUTH != '' && provider != '')
        {
            var fixURL = ETS_LC_URL_OAUTH;
            if (ETS_LC_URL_OAUTH.indexOf('?') !== -1) {
                fixURL += '&provider='+ provider
            } else {
                fixURL += '?provider='+ provider
            }
            if (fixURL)
            {
                window.open(fixURL, 'authWindow', 'width=800,height=auto,scrollbars=yes');
            }
            return false;
        }
    },
    changeDepartment: function (change_department)
    {
        if(change_department)
        {
            $('.lc_chatbox .lc_messages').addClass('lc_hide');
            $('.lc_customer_info').removeClass('closed');
            if($('.lc_customer_name').length)
                $('.lc_customer_name').addClass('lc_hide');
            if($('.lc_customer_email').length)
                $('.lc_customer_email').addClass('lc_hide');
            if($('.lc_customer_phone').length)
                $('.lc_customer_phone').addClass('lc_hide');
            $('.lc_update_info').addClass('lc_hide');
            $('.lc_introduction').removeClass('lc_hide');
            if($('#id_departments').length && !$('#id_departments').hasClass('changed'))
            {
                $('#id_departments').removeAttr('disabled');
                if($('#id_departments').val()!='0')
                {
                    $('#id_departments option').removeAttr('selected');
                    $('#id_departments').val('0');
                    $('#id_departments').change();
                }
                
            }
        }
    },
    lc_markdrap:function(){
        if($('.lc_chatbox.lc_chatbox_open').length || $('.lc_chatbox.lc_bubble_alert').length)
        {
            var left_max= $(window).width()-$('.lc_chatbox').width();
            var top_max= $(window).height()-$('.lc_chatbox').height();
            if($('.lc_chatbox').attr('data-left')<0)
            {
                $('.lc_chatbox').attr('data-left',0);
                $('.lc_chatbox').css('left','0px');
            }   
            if($('.lc_chatbox').attr('data-left') >left_max)
            {
                
                $('.lc_chatbox').attr('data-left',left_max);
                $('.lc_chatbox').css('left',left_max+'px');
            }
            if($('.lc_chatbox').attr('data-top') > top_max)
            {
                $('.lc_chatbox').attr('data-top',top_max);
                $('.lc_chatbox').css('top',top_max+'px');
            }
            if($('.lc_chatbox').attr('data-top') <0)
                $('.lc_chatbox').css('top',0);
            var click = {
                x: 0,
                y: 0
            };
            $(".lc_chatbox").draggable({
                cursor: "grabbing",
                connectToSortable: "body",
                containment: "body",
                scroll: false,
                handle: ".lc_heading",
                start: function( event, ui ) {
                    click.x = event.clientX;
                    click.y = event.clientY;
                    $(this).css('bottom','auto');
                },
                drag: function(event, ui) {
                    var original = ui.originalPosition;
                    var left = event.clientX - click.x + original.left;
                    var top=event. clientY - click.y + original.top;
                    var max_left = $(window).width()-$('.lc_chatbox').width();
                    var max_top = $(window).height()-$('.lc_chatbox').height();
                    if(left>max_left)
                        left=max_left;
                    if(top>max_top)
                        top=max_top;
                    ui.position = {
                        left: left >0 ? left :0,
                        top:  top >0 ? top :0,
                    }; 
                },
                stop: function(event,ui){
                    var original = ui.originalPosition;
                    var left = event.clientX - click.x + original.left;
                    var top = event. clientY - click.y + original.top;
                    var max_left = $(window).width()-$('.lc_chatbox').width();
                    var max_top = $(window).height()-$('.lc_chatbox').height();
                    if(left>max_left)
                        left=max_left;
                    if(top>max_top)
                        top=max_top;
                    $('.lc_chatbox').attr('data-left',left >0 ? left : 0);
                    $('.lc_chatbox').attr('data-top',top > 0 ? top :0);
                    $.ajax({
                        url: ETS_LC_URL_AJAX,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            set_chatbox_position: 1,
                            top: top >0 ? top :0,
                            left: left >0 ? left:0
                        },
                    });
                }
            });
        }   
    },
    startTimer : function(duration) {
        var timer = duration, minutes, seconds;
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        time_display = minutes + ":" + seconds;
        if (--timer > 0 &&  !$('.lc_chatbox').hasClass('is_admin_busy')) {
            $('#clock_wait').html(time_display);
            $('.lc_chatbox .block_admin_busy').html('');
        }
        else if(timer==0 || $('.lc_chatbox').hasClass('is_admin_busy'))
        {
            $('.lc_chatbox').removeClass('wait_support').addClass('is_admin_busy');
            $('#clock_wait').html('');
            $('.lc_chatbox .block_admin_busy').html(text_admin_busy+'<button class="lc_customer_end_chat">'+text_customer_end_chat+'</button>');
        }
        if(startTimerInteval)
            clearInterval(startTimerInteval);
        startTimerInteval =setInterval(function () {
            minutes = parseInt(timer / 60, 10)
            seconds = parseInt(timer % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            time_display = minutes + ":" + seconds;
            if (--timer > 0 && !$('.lc_chatbox').hasClass('is_admin_busy')) {
                $('#clock_wait').html(time_display);
                $('.lc_chatbox .block_admin_busy').html('');
            }
            else if(timer==0 || $('.lc_chatbox').hasClass('is_admin_busy'))
            {
                $('.lc_chatbox').removeClass('wait_support').addClass('is_admin_busy');
                $('#clock_wait').html('');
                $('.lc_chatbox .block_admin_busy').html(text_admin_busy+'<button class="lc_customer_end_chat">'+text_customer_end_chat+'</button>');
            }
        }, 1000);
    },
    readURL : function(input)
    {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if($(input).closest('.col-md-9').find('.customer_avata').length <= 0)
                {
                    $(input).closest('.col-md-9').append('<div class="customer_avata"><img src="'+e.target.result+'"/> </div>');
                }
                else
                {
                    $(input).closest('.col-md-9').find('.customer_avata img').eq(0).attr('src',e.target.result);
                }                        
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
}
$(document).ready(function(){
    livechat.run();
    $( window ).resize(function() {
      livechat.resizeFormChat();
      livechat.scrollLiveChat();
    });
    if (document.documentElement.clientWidth < 767)
    {
        $(document).on('click','.lc_text_area input,.lc_text_area textarea',function(){
            setTimeout(livechat.resizeFormChat(),500);
        });
    }
    $(document).on('click','.lc_sound.disable',function(){
        if (document.documentElement.clientWidth < 767)
        { 
            document.getElementById("lg_ets_sound").play();
        }
        $('.lc_list_customer_chat .lc_sound').removeClass('disable').addClass('enable');
   }); 
   $(document).on('click','.lc_sound.enable',function(){
        $('.lc_list_customer_chat .lc_sound').removeClass('enable').addClass('disable');
   });
   $(document).on('click', '.lc_social_item', function () {
        if ($(this).data('auth') != ''){
            livechat.authPopup($(this).data('auth'));
        }
   });
   $(document).on('click','.lc_customer_end_chat',function(){
        livechat.actionEndChat();
        return false;
   }); 
   $(document).on('change','#id_departments',function(){
        $(this).addClass('changed');
   });
   $(document).on('change','input[name="message_file"]',function(){
        var filename = $(this).val().split('\\').pop();
        filesize= this.files[0].size/1048576;
        if(!ETS_LC_MAX_FILE_MS || filesize<=ETS_LC_MAX_FILE_MS)
        {
            if($(this).parents('.form_upfile').eq(0).next('.form_upfile_val').length)
            {
                $(this).parents('.form_upfile').eq(0).next('.form_upfile_val').addClass('show').find('.file_name').html(filename+'<div class="uploading"> ('+uploading+')</div>');
            }
            else
            {
                $(this).parents('.form_upfile').eq(0).after('<div class="form_upfile_val show"><div class="file_name">'+filename+'<div class="uploading"> ('+uploading+')</div></div><button class="delete_file_message" title="Delete">'+delete_text+'</button></div>');
            }
        }
        else
        {
            $(this).val('');
            alert(invalid_file_max_size);
        }
        if($(this).val()=='')
        {
            $(this).parent().next('.form_upfile_val').remove();
        }
        $('.lc_text_area textarea[name="message"]').focus();  
    });
    $(document).on('click','.delete_file_message',function(){
        $(this).parents('.form_upfile_val').eq(0).removeClass('show').prev('.form_upfile').find('input').val('');
        $(this).parents('.form_upfile_val').eq(0).removeClass('show').remove();
    });
});