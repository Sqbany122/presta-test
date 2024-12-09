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
var playSounded =false;
var number_form_chat;
var autoLoadMasegeInFormChat=false;
var loadListCustomerChat =false;
var count_conversation=20;
var made_messages = [];
var livechat = {
    playSound: function()
    {
        if(ETS_LC_USE_SOUND_BACKEND && $('.lc_list_customer_chat .lc_sound').hasClass('enable'))
        {
            document.getElementById("lg_ets_sound").volume=1;
            document.getElementById("lg_ets_sound").play();
        }
    },
    getOldMessages:function(id_conversation)
    {
        if(!$('.chatbox_employe_'+id_conversation).hasClass('loaded') && !$('.chatbox_employe_'+id_conversation).hasClass('loading') )
        {
            $('.chatbox_employe_'+id_conversation).addClass('loading');
            $('.chatbox_employe_'+id_conversation).find('.massage_more_loading').show();
            firstId= $('.chatbox_employe_'+id_conversation+' .lc_msg_board .lc_msg').length > 0 ? $('.chatbox_employe_'+id_conversation+' .lc_msg_board .lc_msg:first-child').attr('data-id-message') : 0;
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    getOldMessages: 1,
                    firstId: firstId,
                    id_conversation:id_conversation,
                },
                success: function(json)
                { 
                    if(json.messages&& json.firstId)
                    {
                        livechat.processOldMessages(json.messages,id_conversation);
                        if(json.loaded && !$('.chatbox_employe_'+id_conversation).hasClass('loaded'))
                            $('.chatbox_employe_'+id_conversation).addClass('loaded');
                        if(json.firstId)
                            $(".chatbox_employe_"+id_conversation+" .lc_messages").stop().animate({scrollTop: $(".chatbox_employe_"+id_conversation+" .lc_messages .lc_msg[data-id-message='"+json.firstId+"']").position().top});
                    }
                    else
                    {
                        if(!$('.chatbox_employe_'+id_conversation).hasClass('loaded'))
                            $('.chatbox_employe_'+id_conversation).addClass('loaded');
                    }
                    $('.chatbox_employe_'+id_conversation).removeClass('loading');  
                    $('.chatbox_employe_'+id_conversation).find('.massage_more_loading').hide();      
                }
            });
        }
    },
    processOldMessages:function(messages,id_conversation){
        if(messages)
        {
            $.each(messages,function(i,msg){
                var msgHtml = '<li class="lc_msg '+(msg.id_employee != 0 ? 'is_employee'+(msg.employee_name?' has_name_emplode':'') : 'is_customer'+(msg.customer_name?' has_name_customer':''))+'" data-id-message="'+msg.id_message+'">'
                +'<div class="lc_sender">'+(msg.id_employee != 0 ? (msg.employee_avata?'<div class="avata'+(ETS_LC_AVATAR_IMAGE_TYPE=='square'?' lc_avatar_square':'')+'"><img src="'+msg.employee_avata+'" title="'+msg.employee_name+'"></div>':'')+'<span title="'+msg.employee_name+'">'+msg.employee_name+'</span>': (msg.customer_avata?'<div class="avata'+(ETS_LC_AVATAR_IMAGE_TYPE=='square'?' lc_avatar_square':'')+'"><img src="'+msg.customer_avata+'" title="'+msg.customer_name+'"></div>':'')+'<span title="'+msg.customer_name+'">'+msg.customer_name+'</span>')+'</div>'                
                +(ETS_LC_DISPLAY_TIME ? '<div class="lc_msg_time">'+msg.datetime_added+'</div>' : '')
                +'<div class="lc_msg_content">'+msg.message+'</div>'
                +(msg.id_employee!=0 && (ETS_LC_ENABLE_EDIT_MESSAGE||ETS_LC_ENABLE_DELETE_MESSAGE)?'<div class="lc_action_message">'+(ETS_LC_ENABLE_DELETE_MESSAGE?'<span title="'+delete_text+'" class="employee_delete_message" data-id-message="'+msg.id_message+'">'+delete_text+'</span>':'')+(ETS_LC_ENABLE_EDIT_MESSAGE?'<span title="'+edit_text+'" class="employee_edit_message" data-id-message="'+msg.id_message+'">'+edit_text+'</span>':'')+'</div>':'')
                +(msg.edited==1?'<div class="lc_msg_edited">'+(!ETS_LC_DISPLAY_TIME?edited_text:edited_at_text+' '+msg.datetime_edited)+'</div>':'')
                +'</li>';               
                if($('.chatbox_employe_'+id_conversation+' .lc_msg_board > .lc_msg').length <= 0)
                {
                    $('.chatbox_employe_'+id_conversation+' .lc_msg_board').append(msgHtml);
                }
                else if($('.chatbox_employe_'+id_conversation+' .lc_msg_board > .lc_msg[data-id-message="'+msg.id_message+'"]').length <= 0)
                {
                    var msgAdded = false;
                    $($('.chatbox_employe_'+id_conversation+' .lc_msg_board > .lc_msg').get().reverse()).each(function(){                        
                        if(parseInt($(this).attr('data-id-message')) < parseInt(msg.id_message))
                        {
                            $(this).after(msgHtml);
                            msgAdded = true;
                            return false;   
                        }
                    });
                    if(!msgAdded)
                    {
                        $('.chatbox_employe_'+id_conversation+' .lc_msg_board').prepend(msgHtml); 
                    }
                           
                }                    
            });
        }
         
    },
    processConvertations:function(conversations)
    {
        if(conversations)
        {
            $.each(conversations,function(i,conversation){
                if($('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').length>0)
                {
                    if(conversation.online)
                    {
                        if(!$('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').closest('li').hasClass('online'))
                        {
                            $('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').closest('li').addClass('online');
                            $('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').find('.icon-offline').addClass('icon-online').removeClass('icon-offline');
                        }
                    }
                    else
                    {
                        if($('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').closest('li').hasClass('online'))
                        {
                            $('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').closest('li').removeClass('online');
                            $('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').find('.icon-online').addClass('icon-offline').removeClass('icon-online');
                        }
                    }
                    if(conversation.wait_accept)
                        $('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').parent().addClass('wait_accept');
                    else
                        $('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').parent().removeClass('wait_accept');
                    if(conversation.has_changed)
                        $('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').parent().addClass('has_changed');
                    else
                        $('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').parent().removeClass('has_changed');
                    $('.list_customer li .conversation-item[data-id="'+conversation.id_conversation+'"]').addClass('exist');
                }
            });
            if($('.conversation-item:not(.exist)').length)
            {
                $('.conversation-item:not(.exist)').parent().remove();
            }
            $('.conversation-item').removeClass('exist');
        }
    },
    actionEndchat: function(id_conversation)
    {
        $('.chatbox_employe_'+id_conversation).addClass('end_chat');
        $('.chatbox_employe_'+id_conversation+' .accept_conversation').remove();
        //$('.chatbox_employe_'+id_conversation+' .lc_send_box').remove();
        //$('#id_departments_'+id_conversation).attr('disabled','disabled');
//        $('button.change_department').attr('disabled','disabled');
    },
    processMessages: function(jsonData) 
    {
        var lc_ets_scroll=false;
        var h=$('.chatbox_employe_'+jsonData.id_conversation+' .lc_msg_board').height();
        if($('.chatbox_employe_'+jsonData.id_conversation+' .massage_more_loading:not(:hidden)').length)
        {
            h += $('.chatbox_employe_'+jsonData.id_conversation+' .massage_more_loading:not(:hidden)');
        }
        if($('.chatbox_employe_'+jsonData.id_conversation+' .more_load:not(:hidden)').length)
        {
            h += $('.chatbox_employe_'+jsonData.id_conversation+' .more_load').height();
        }
        if(!$('.chatbox_employe_'+jsonData.id_conversation+' .lc_error').hasClass('lc_hide'))
        {
            h += $('.chatbox_employe_'+jsonData.id_conversation+' .lc_error').height();
        }
        
        if($('.chatbox_employe_'+jsonData.id_conversation+' .message_status:not(:hidden)').length)
        {
            h += $('.chatbox_employe_'+jsonData.id_conversation+' .message_status').height();
        }
        if($('.chatbox_employe_'+jsonData.id_conversation+' .lc_message_end_chat:not(:hidden)').length)
        {
            h += $('.chatbox_employe_'+jsonData.id_conversation+' .lc_message_end_chat').height();
        }
        if($('.chatbox_employe_'+jsonData.id_conversation+' .writing_customer:not(:hidden)').length)
        {
            h += $('.chatbox_employe_'+jsonData.id_conversation+' .writing_customer').height();
        }
        var h_m=$('.chatbox_employe_'+jsonData.id_conversation+' .lc_messages').scrollTop() + $('.chatbox_employe_'+jsonData.id_conversation+' .lc_messages').height()+30;
        if( h_m>= h ) {
            lc_ets_scroll=true;
        }
        if(jsonData.waiting_acceptance)
            $('.chatbox_employe_'+jsonData.id_conversation).addClass('waiting_acceptance');
        else
            $('.chatbox_employe_'+jsonData.id_conversation).removeClass('waiting_acceptance');
        if(!jsonData.wait_accept)
            $('.chatbox_employe_'+jsonData.id_conversation).removeClass('wait_accept');
        if(jsonData.has_changed)
        {
            $('.chatbox_employe_'+jsonData.id_conversation).addClass('has_changed');
            if($('.chatbox_employe_'+jsonData.id_conversation+' .accept_conversation .alert').length)
            {
                $('.chatbox_employe_'+jsonData.id_conversation+' .accept_conversation .alert').html(jsonData.has_changed+' has tranferred this chat to you');
            }
            else
            {
                $('.chatbox_employe_'+jsonData.id_conversation+' .accept_conversation .accept_submit').before('<p class="alert alert-warning">'+jsonData.has_changed+' has tranferred this chat to you</p>')
            }
            if(jsonData.wait_accept)
                $('.chatbox_employe_'+jsonData.id_conversation).addClass('wait_accept');
        } 
        else
            $('.chatbox_employe_'+jsonData.id_conversation).removeClass('has_changed');
        if(jsonData.messages)
        {
            if(jsonData.isCustomerOnline)
            {
                if($('.chatbox_employe_'+jsonData.id_conversation+' .lc_chatbox_form').hasClass('customer_offline'))
                {
                    $('.chatbox_employe_'+jsonData.id_conversation+' .lc_chatbox_form').removeClass('customer_offline').addClass('customer_online');
                }
            }
            else
            {
                if($('.chatbox_employe_'+jsonData.id_conversation+' .lc_chatbox_form').hasClass('customer_online'))
                {
                    $('.chatbox_employe_'+jsonData.id_conversation+' .lc_chatbox_form').removeClass('customer_online').addClass('customer_offline');
                }
            }
            var messages=jsonData.messages;
            $.each(messages,function(i,msg){
                var msgHtml = '<li class="lc_msg '+(msg.id_employee != 0 ? 'is_employee'+(msg.employee_name?' has_name_emplode':'') : 'is_customer'+(msg.customer_name?' has_name_customer':''))+'" data-id-message="'+msg.id_message+'">'
                +'<div class="lc_sender">'+(msg.id_employee != 0 ? (msg.employee_avata?'<div class="avata'+(ETS_LC_AVATAR_IMAGE_TYPE=='square'?' lc_avatar_square':'')+'"><img src="'+msg.employee_avata+'" title="'+msg.employee_name+'"></div>':'')+(msg.employee_name?'<span title="'+msg.employee_name+'">'+msg.employee_name+'</span>':''): (msg.customer_avata?'<div class="avata'+(ETS_LC_AVATAR_IMAGE_TYPE=='square'?' lc_avatar_square':'')+'"><img src="'+msg.customer_avata+'" title="'+msg.customer_name+'"></div>':'')+(msg.customer_name?'<span title="'+msg.customer_name+'">'+msg.customer_name+'</span>':''))+'</div>'                
                +(ETS_LC_DISPLAY_TIME ? '<div class="lc_msg_time">'+msg.datetime_added+'</div>' : '')
                +'<div class="lc_msg_content">'+msg.message+'</div>'
                +(msg.id_employee!=0 && (ETS_LC_ENABLE_EDIT_MESSAGE||ETS_LC_ENABLE_DELETE_MESSAGE)?'<div class="lc_action_message">'+(ETS_LC_ENABLE_DELETE_MESSAGE?'<span title="'+delete_text+'" class="employee_delete_message" data-id-message="'+msg.id_message+'">'+delete_text+'</span>':'')+(ETS_LC_ENABLE_EDIT_MESSAGE?'<span title="'+edit_text+'" class="employee_edit_message" data-id-message="'+msg.id_message+'">'+edit_text+'</span>':'')+'</div>':'')
                +(msg.edited==1?'<div class="lc_msg_edited">'+(!ETS_LC_DISPLAY_TIME?edited_text:edited_at_text+' '+msg.datetime_edited)+'</div>':'')
                +'</li>';               
                if($('.chatbox_employe_'+jsonData.id_conversation+' .lc_msg_board > .lc_msg').length <= 0)
                {
                    $('.chatbox_employe_'+jsonData.id_conversation+' .lc_msg_board').append(msgHtml);
                }
                else if($('.chatbox_employe_'+jsonData.id_conversation+' .lc_msg_board > .lc_msg[data-id-message="'+msg.id_message+'"]').length <= 0)
                {
                    var msgAdded = false;
                    $($('.chatbox_employe_'+jsonData.id_conversation+' .lc_msg_board > .lc_msg').get().reverse()).each(function(){                        
                        if(parseInt($(this).attr('data-id-message')) < parseInt(msg.id_message))
                        {
                            $(this).after(msgHtml);
                            msgAdded = true;
                            return false;   
                        }
                    });
                    if(!msgAdded)
                    {
                        $('.chatbox_employe_'+jsonData.id_conversation+' .lc_msg_board').prepend(msgHtml); 
                    }
                           
                }                    
            });
            var message_edited =jsonData.message_edited;
            if(message_edited)
            {
                $.each(message_edited,function(i,msg){
                    if($('li.lc_msg[data-id-message="'+msg.id_message+'"]').length)
                    {
                       $('li.lc_msg[data-id-message="'+msg.id_message+'"] .lc_msg_content').html(msg.message); 
                       if($('li.lc_msg[data-id-message="'+msg.id_message+'"] .lc_msg_edited').length)
                            $('li.lc_msg[data-id-message="'+msg.id_message+'"] .lc_msg_edited').remove();
                       $('li.lc_msg[data-id-message="'+msg.id_message+'"]').append('<div class="lc_msg_edited">'+(!ETS_LC_DISPLAY_TIME?edited_text:edited_at_text+' '+msg.datetime_edited)+'</div>');
                    }
                });
            }
            if(lc_ets_scroll)
            {
                livechat.scrollLiveChat(jsonData.id_conversation);
            }     
        }
        if(jsonData.message_deleted)
        {
            var message_deleted=jsonData.message_deleted.split(',');
            $.each(message_deleted,function(i,id){
                if($('li.lc_msg[data-id-message="'+id+'"]').length)
                    $('li.lc_msg[data-id-message="'+id+'"]').remove();
            });
        }
        if(jsonData.customer_rated!='0' && ETS_LC_DISPLAY_RATING)
        {
            var customer_rated = $('.chatbox_employe_'+jsonData.id_conversation+' input[name="customer_rated"]').val();
            $('.chatbox_employe_'+jsonData.id_conversation+' .list_star_fly').removeClass('star_run');
            $('.chatbox_employe_'+jsonData.id_conversation+' .criterions_livechat').removeClass('lc_hide');
            $('.chatbox_employe_'+jsonData.id_conversation+' .made_message_rate_box').removeClass('no_display_rate');
            if(jsonData.customer_rated!=customer_rated)
            {
                for(var i=0;i<jsonData.customer_rated;i++)
                {
                    
                    $('.chatbox_employe_'+jsonData.id_conversation+' .info_show_expand .star_content .star:eq('+i+')').addClass('star_on');
                    $('.chatbox_employe_'+jsonData.id_conversation+' .lc_text_area .star_content .star:eq('+i+')').addClass('star_on');
                }
                if(jsonData.customer_rated<5)
                {
                    for(var i=jsonData.customer_rated;i<5;i++)
                    {
                        $('.chatbox_employe_'+jsonData.id_conversation+' .lc_text_area .star_content .star:eq('+i+')').removeClass('star_on');
                        $('.chatbox_employe_'+jsonData.id_conversation+' .info_show_expand .star_content .star:eq('+i+')').removeClass('star_on');
                    }
                }
                $('.chatbox_employe_'+jsonData.id_conversation+' input[name="customer_rated"]').val(jsonData.customer_rated);
                $('.chatbox_employe_'+jsonData.id_conversation+' .list_star_fly').addClass('star_run');
            }
        }
        if(jsonData.isCustomerSeen)
        {
            $('.chatbox_employe_'+jsonData.id_conversation+' .message_status li').removeClass('show');
            $('.chatbox_employe_'+jsonData.id_conversation+' .seen_customer').addClass('show');
        }
        else
        {
            if(jsonData.isCustomerDelivered)
            {
                $('.chatbox_employe_'+jsonData.id_conversation+' .message_status li').removeClass('show');
                $('.chatbox_employe_'+jsonData.id_conversation+' .delivered_customer').addClass('show');
            }
            else
            {
                if(jsonData.isCustomerSent)
                {
                    $('.chatbox_employe_'+jsonData.id_conversation+' .message_status li').removeClass('show');
                    $('.chatbox_employe_'+jsonData.id_conversation+' .sent_customer').addClass('show');
                }
            }
        }
        if(jsonData.isCustomerWriting || !jsonData.lastMessageIsEmployee)
        {
            if(jsonData.isCustomerWriting)
            {
                $('.chatbox_employe_'+jsonData.id_conversation+' .writing_customer').addClass('show');
            }  
            else
            {
                $('.chatbox_employe_'+jsonData.id_conversation+' .writing_customer').removeClass('show');
            }
                
            $('.chatbox_employe_'+jsonData.id_conversation+' .message_status').hide();
        }
        else
        {
            $('.chatbox_employe_'+jsonData.id_conversation+' .writing_customer').removeClass('show');
            if(jsonData.end_chat || !jsonData.isRequestAjax)
            {
                $('.chatbox_employe_'+jsonData.id_conversation+' .message_status').hide();
            }
            else
            {
                $('.chatbox_employe_'+jsonData.id_conversation+' .message_status').show();
            }
                
        }
        if(jsonData.end_chat)
        {
            livechat.actionEndchat(jsonData.id_conversation);
        }    
        if(jsonData.end_chat || !jsonData.isRequestAjax)
        {
            $('.chatbox_employe_'+jsonData.id_conversation+' .lc_message_end_chat').show();
            if(jsonData.end_chat)
            {
                $('.chatbox_employe_'+jsonData.id_conversation+' .employee_end').html(jsonData.end_chat);
                $('.chatbox_employe_'+jsonData.id_conversation+' .employee_end').show();
                $('.chatbox_employe_'+jsonData.id_conversation+' .customer_end').hide();
                $('.chatbox_employe_'+jsonData.id_conversation+' .cl_end_chat').hide();
            }  
            else
            {
                $('.chatbox_employe_'+jsonData.id_conversation+' .employee_end').hide();
                if(!jsonData.isRequestAjax)
                    $('.chatbox_employe_'+jsonData.id_conversation+' .customer_end').show();
                else
                    $('.chatbox_employe_'+jsonData.id_conversation+' .customer_end').hide();
            }    
        }
        else
        {
            $('.chatbox_employe_'+jsonData.id_conversation+' .lc_message_end_chat').hide();
            $('.chatbox_employe_'+jsonData.id_conversation+' .cl_end_chat').show();
        }
                 
        if(jsonData.count_message_not_seen)
        {
            $('.chatbox_employe_'+jsonData.id_conversation+' .lc_heading_count_message_not_seen').addClass('show');
            $('.chatbox_employe_'+jsonData.id_conversation+' .lc_heading_count_message_not_seen').html(jsonData.count_message_not_seen);
        }
        else
        {
            $('.chatbox_employe_'+jsonData.id_conversation+' .lc_heading_count_message_not_seen').removeClass('show');
            $('.chatbox_employe_'+jsonData.id_conversation+' .lc_heading_count_message_not_seen').html(jsonData.count_message_not_seen);
        }
            
    },
    loadMoreCustomerChat : function()
    {
        var customer_all=0;
        var customer_archive=0;
        if($('.tab_content_customer').hasClass('loading') ||$('.tab_content_customer').hasClass('loaded'))
            return false;
        if($('.lc_list_customer_chat').hasClass('list_customer_all') || $('.lc_list_customer_chat').hasClass('list_customer_search'))
        {
           customer_all=1;
           customer_archive=0;
        }
        else
        {
            if($('.lc_list_customer_chat').hasClass('list_customer_archive'))
            {
                customer_all=0;
                customer_archive=1;
            }
        } 
        $('.tab_content_customer').addClass('loading');
        $('.tab_content_customer .massage_more_loading').show();
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                load_more_customer_chat: 1,
                customer_all:customer_all,
                customer_archive:customer_archive,
                customer_search:$('#input_search_customer_chat').length && $('#input_search_customer_chat').val().length>=1 ? $('#input_search_customer_chat').val():'',
                count_conversation : 20,
                refresh:$('.lc_list_customer_chat').length >0 ? 1 : 0,
                lastID_Conversation: $('.lc_list_customer_chat').length >0 ? $('.lc_list_customer_chat .list_customer li:last-child .conversation-item').attr('data-id'):0,
            },
            success: function(json)
            {
                $('.tab_content_customer .massage_more_loading').hide();
                $('.tab_content_customer').removeClass('loading');
                if(json.loaded)
                    $('.tab_content_customer').addClass('loaded');
                $('.tab_content_customer .list_customer').append(json.list_more_customer);
                
            }
         });
        
    },
    displayListCustomerChat: function(auto)
    {
        if(($('.list_chatbook_employee .lc_chatbox_employe').length && auto)||($('.lc_list_customer_chat').length && $('#ets_lc_status_employee').val()=='offline' && auto))
            return ;
        if($('.tab_content_customer').hasClass('loading'))
            return false;
        var customer_all=0;
        var customer_archive=0;
        if($('.lc_list_customer_chat').hasClass('list_customer_all') || $('.lc_list_customer_chat').hasClass('list_customer_search'))
        {
           customer_all=1;
           customer_archive=0;
        }
        else
        {
            if($('.lc_list_customer_chat').hasClass('list_customer_archive'))
            {
                customer_all=0;
                customer_archive=1;
            }
        } 
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                load_list_customer_chat: 1,
                customer_all:customer_all,
                customer_archive:customer_archive,
                customer_search:$('#input_search_customer_chat').length && $('#input_search_customer_chat').val().length>=1 ? $('#input_search_customer_chat').val():'',
                refresh:$('.lc_list_customer_chat').length >0 ? 1 : 0,
                count_conversation : $('.lc_list_customer_chat .list_customer li').length > count_conversation ? $('.lc_list_customer_chat .list_customer li').length :count_conversation,
                lastID_Conversation: $('.lc_list_customer_chat').length>0 ? $('.lc_list_customer_chat .list_customer li:first-child .conversation-item').attr('data-id'):0,
                lastID_message: $('.lc_list_customer_chat').length> 0 ? $('.lc_list_customer_chat .list_customer li:first-child .conversation-item').attr('data-id-message'):0,
                auto:auto,
            },
            success: function(json)
            {
                if(!json)
                    return '';
                if(json.html)
                {
                    if($('.lc_list_customer_chat').length>0)
                    {
                        if(!auto)
                        {
                            $('.lc_list_customer_chat .tab_content_customer .list_customer').html(json.html);
                            if($('.lc_chatbox_employe.active_expand').length)
                            {
                                $('.conversation-item[data-id="'+$('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val()+'"]').addClass('active_expand');
                            }
                            $('.toogle-hide-left .total_message').html(json.totalMessageNoSeen);
                        }
                        else
                        {
                            if(json.reload_list)
                            {
                                if(json.reload_list==1)
                                {
                                    if($('.lc_list_customer_chat .list_customer li:first-child .conversation-item').attr('data-id')==json.last_message.id_conversation)
                                    {
                                        $('.lc_list_customer_chat .list_customer li:first-child .conversation-item .lc_msg_time').html(json.last_message.datetime_added);
                                        $('.lc_list_customer_chat .list_customer li:first-child .conversation-item .message_content').html(json.last_message.message);
                                        if(json.last_message.count_message_not_seen>0)
                                        {
                                            if($('.lc_list_customer_chat .list_customer li:first-child .conversation-item .count_message_not_seen').length)
                                                $('.lc_list_customer_chat .list_customer li:first-child .conversation-item .count_message_not_seen').html(json.last_message.count_message_not_seen);
                                            else
                                             {
                                                $('.lc_list_customer_chat .list_customer li:first-child .conversation-item .lc_msg_time').after('<span class="count_message_not_seen">'+json.last_message.count_message_not_seen+'</span>');
                                             }   
                                        }
                                        $('.lc_list_customer_chat .list_customer li:first-child .conversation-item').attr('data-id-message',json.last_message.id_message);
                                        if(json.conversations)
                                            livechat.processConvertations(json.conversations);
                                    }
                                    else
                                    {
                                        $('.lc_list_customer_chat .tab_content_customer .list_customer').html(json.html);
                                        if($('.lc_chatbox_employe.active_expand').length)
                                        {
                                            $('.conversation-item[data-id="'+$('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val()+'"]').addClass('active_expand');
                                        }
                                    }
                                        
                                }
                                else{
                                    $('.lc_list_customer_chat .tab_content_customer .list_customer').html(json.html);
                                    if($('.lc_chatbox_employe.active_expand').length)
                                    {
                                        $('.conversation-item[data-id="'+$('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val()+'"]').addClass('active_expand');
                                    }
                                }
                                
                            }
                            else
                            {
                                if(json.conversations)
                                    livechat.processConvertations(json.conversations);
                            }
                            
                        }
                        
                    }
                    else
                    {
                        
                        $('body').append(json.html);
                        if(ETS_CLOSE_CHAT_BOX_BACKEND_TYPE=='lc_small_bubble')
                        {
                            if($('#header_infos #header_search').length)
                                $('#header_infos #header_search').after('<div class="lc_list_customer_chat_small_bubble '+$('.lc_list_customer_chat').attr('class')+'"><div class="lc_admin_info"></div></div>');
                            else
                                $('#header_infos #header_quick').after('<div class="lc_list_customer_chat_small_bubble '+$('.lc_list_customer_chat').attr('class')+'"><div class="lc_admin_info"></div></div>');
                            $('.lc_list_customer_chat_small_bubble .lc_admin_info').html($('.lc_list_customer_chat .lc_heading.lc_heading_online').clone());
                            $('.lc_list_customer_chat_small_bubble .lc_admin_info').append($('.lc_list_customer_chat .lc_heading.lc_heading_offline').clone());
                            $('.lc_list_customer_chat_small_bubble .lc_admin_info').append($('.lc_list_customer_chat .lc_heading.lc_heading_do_not_disturb').clone());
                            $('.lc_list_customer_chat_small_bubble .lc_admin_info').append($('.lc_list_customer_chat .lc_heading.lc_heading_invisible').clone());
                            setTimeout(function(){
                                $('.lc_list_customer_chat_small_bubble .lc_heading').each(function(){
                                    
                                    var color = $( this ).css( "background-color" );
                                    $(this).css('color',color);
                                    $(this).css('background-color','');
                                    $(this).find('.toogle-hide-left').css('background-color','');
                                });
                            },2000);
                        }
                        $('.tab_content_customer').scroll(function(){
                            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight && !$('.tab_content_customer').hasClass('loaded')) {
                                 livechat.loadMoreCustomerChat();  
                            }
                        }); 
                        if(!ETS_CONVERSATION_DISPLAY_ADMIN)
                        {
                            $('body').addClass('hide_chatbox_employe_not_active');
                            livechat.lc_markdrap();
                            $('.lc_ticket_recently_list').slick('refresh'); 
                        }   
                        if(ETS_CONVERSATION_LIST_TYPE=='floating'){
                            $('body').addClass('lc_floating');
                            $('.lc_ticket_recently_list').slick('refresh');   
                        }                         
                        else {
                            $('body').addClass('lc_fixed');
                            $('.lc_ticket_recently_list').slick('refresh');   
                        }
                        if (document.documentElement.clientWidth < 767)
                        {
                            $('body').addClass('hide_chatbox_employe_not_active');
                            livechat.lc_markdrap();
                            $('.lc_ticket_recently_list').slick('refresh'); 
                            $('.lc_list_customer_chat').addClass('lc_left_hide');
                            $('.toogle-hide-left').attr('title',open_text);
                            $('.lc_heading').attr('title',open_text);
                        }
                        $('.lc_list_customer_chat').removeClass('lc_hide');
                        $('body').addClass('body_'+ETS_CLOSE_CHAT_BOX_BACKEND_TYPE);
                        if (document.documentElement.clientWidth < 767 && $('.lc_list_customer_chat .lc_sound').length>0)
                        {
                            $('.lc_list_customer_chat .lc_sound').removeClass('enable').addClass('disable');
                        }
                    }
                }
                else
                {
                    if(json.conversations)
                        livechat.processConvertations(json.conversations);
                }
                setTimeout(function(){
                    if($('.lc_ticket_recently_list').length)
                        $('.lc_ticket_recently_list').slick('refresh');
                }, 500);
                if($('.toogle-hide-left .total_message').html()!=json.totalMessageNoSeen)
                    livechat.playSound();
                $('.toogle-hide-left .total_message').html(json.totalMessageNoSeen);
                if(json.totalMessageNoSeen)
                    $('.total_message').show();
                else
                    $('.total_message').hide();
                
                if(json.level_request!=level_request)
                {
                    level_request =json.level_request;
                    if(autoLoadMasegeInFormChat)
                        clearInterval(autoLoadMasegeInFormChat);
                    if(loadListCustomerChat)
                        clearInterval(loadListCustomerChat);
                    autoLoadMasegeInFormChat = setInterval(livechat.loadMasegeInFormChat,ETS_LC_TIME_OUT_BACK_END>0?ETS_LC_TIME_OUT_BACK_END*level_request:3000*level_request);
                    loadListCustomerChat = setInterval(livechat.displayListCustomerChat,ETS_LC_TIME_OUT_BACK_END>0?ETS_LC_TIME_OUT_BACK_END*level_request:3000*level_request,1);
                } 
                if(!loadListCustomerChat)
                {
                    loadListCustomerChat = setInterval(livechat.displayListCustomerChat,ETS_LC_TIME_OUT_BACK_END>0?ETS_LC_TIME_OUT_BACK_END*level_request:3000*level_request,1);
                }
                                    
            }
        });
    },
    openNewFromChat: function(id_conversation)
    {
        refresh= $('body .chatbox_employe_'+id_conversation).length ? 1 : 0;
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
			cache: false,
            data: {
                load_chat_box: 1,
                id_conversation:id_conversation,
                message_delivered:1,
                message_seen:1,
                message_writing:0,
                refresh: refresh,
            },
            success: function(json)
            {
                if(json.checkDepartment)
                {
                    alert(json.checkDepartment);
                    $('.conversation_item_'+id_conversation).parent().remove();
                    if($('.chatbox_employe_'+id_conversation).length)
                        $('.chatbox_employe_'+id_conversation).remove();
                }
                if(!refresh)
                {
                    if(json.html)
                    {
                        if($('body .list_chatbook_employee').length)
                        {
                           if(!$('body .chatbox_employe_'+id_conversation).length)
                           {
                               $('body .list_chatbook_employee').append(json.html); 
                           }
                        }
                        else
                        {
                            $('body').append('<div class="list_chatbook_employee'+(is_lc_RTL?' lc_chatbox_rtl':'')+' '+ETS_CLOSE_CHAT_BOX_BACKEND_TYPE+'">'+json.html+'</div>');
                            if(is_lc_RTL)
                                $('body').addClass('lc_body_rtl');

                        } 
                        if($('body .lc_chatbox_employe.active').length>=number_form_chat)
                        {
                                                        
                            $('body .lc_chatbox_employe.active').first().removeClass('active');
                        }
                        $('body .lc_chatbox_employe').removeClass('active_expand');
                        $('body .chatbox_employe_'+id_conversation).addClass('active').addClass('active_expand');
                        $('.conversation-item').removeClass('active_expand');
                        $('.conversation_item_'+id_conversation).addClass('active_expand');
                        $('.lc_messages').scroll(function(){
                            if($(this).scrollTop()==0)
                            {
                                id_conversation_scroll = $(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
                                livechat.getOldMessages(id_conversation_scroll);
                            }    
                        });  
                        livechat.scrollLiveChat(id_conversation);
                        livechat.displayFromDepartment(id_conversation);
                    }
                    if (document.documentElement.clientWidth < 767)
                    {
                        $('.chatbox_employe_'+id_conversation).addClass('lc_chatbox_maximize');
                        $('body').removeClass('lc-expand-admin');
                        $('body').addClass('lc_no_scroll');
                        livechat.resizeFormChat(id_conversation);
                    }
                    $('.lc_chatbox_employe').removeClass('show');
                    $('.conversation_item_'+id_conversation+' .count_message_not_seen').remove();
                    $('.toogle-hide-left .total_message').html(json.totalMessageNoSeen);
                    $('.message_seen').each(function(){
                       $(this).val('0'); 
                    });
                    $('body .chatbox_employe_'+id_conversation+' textarea[name="message"]').focus();
                    $('body .chatbox_employe_'+id_conversation+' .message_seen').val('1');
                    if(json.totalMessageNoSeen)
                    {
                        $('.total_message').show();
                    }
                    else
                        $('.total_message').hide();
                    livechat.resizeBodyFormChat(id_conversation); 
                }
                else
                {
                    if(json.messages)
                    {
                        livechat.processMessages(json);
                        if($('body .lc_chatbox_employe.active').length>=number_form_chat && !$('body .chatbox_employe_'+id_conversation).hasClass('active'))
                        {
                            $('body .lc_chatbox_employe.active').first().removeClass('active');
                            $('body .chatbox_employe_'+id_conversation).addClass('active');
                        }
                    }
                    if($('body .chatbox_employe_'+id_conversation).hasClass('lc_chatbox_closed'))
                        $('body .chatbox_employe_'+id_conversation).removeClass('lc_chatbox_closed').addClass('lc_chatbox_open');
                    $('body .lc_chatbox_employe').removeClass('active_expand');
                    $('body .chatbox_employe_'+id_conversation).addClass('active').addClass('active_expand');
                    $('.conversation-item').removeClass('active_expand');
                    $('.conversation_item_'+id_conversation).addClass('active_expand');
                    
                }
                livechat.updateDisplayFormChatBox();
               // $('[data-toggle="tooltip"]').tooltip();
                $('.conversation_item_'+id_conversation).removeClass('notclick');  
                $('.lc_list_customer_chat').removeClass('lc_clicked'); 
                if($('.lc_loading_expand').length)
                    $('.lc_loading_expand').remove();              
            },
            error: function(xhr, status, error)
            {
                $('.lc_list_customer_chat').removeClass('lc_clicked');  
                $('.conversation_item_'+id_conversation).removeClass('notclick');          
            }
        });
    },
    loadMasegeInFormChat:function()
    {
        if($('.lc_list_customer_chat').length && $('#ets_lc_status_employee').val()=='offline')
            return false;
        if($('.list_chatbook_employee .lc_chatbox_employe').length)
        {
            var ids_conversation='';
            var messages_delivered='';
            var messages_seen='';
            var messages_writing='';
            $('.list_chatbook_employee .lc_chatbox_employe').each(function(){
                ids_conversation +=$(this).find('input[name="id_conversation"]').val()+',';
                messages_delivered +=$(this).find('input[name="message_delivered"]').val()+',';
                messages_seen +=$(this).find('input[name="message_seen"]').val()+',';
                messages_writing +=$(this).find('input[name="message_writing"]').val()+',';
            });
            var customer_all=0;
            var customer_archive=0;
            if($('.lc_list_customer_chat').hasClass('list_customer_all') || $('.lc_list_customer_chat').hasClass('list_customer_search'))
            {
               customer_all=1;
               customer_archive=0;
            }
            else
            {
                if($('.lc_list_customer_chat').hasClass('list_customer_archive'))
                {
                    customer_all=0;
                    customer_archive=1;
                }
            }
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                cache: false,
                data: {
                    load_chat_boxs: 1,
                    ids_conversation:ids_conversation,
                    customer_all:customer_all,
                    customer_archive:customer_archive,
                    customer_search:$('#input_search_customer_chat').length && $('#input_search_customer_chat').val().length>=3 ? $('#input_search_customer_chat').val():'',
                    messages_delivered:messages_delivered,
                    messages_seen:messages_seen,
                    messages_writing:messages_writing,
                    refresh:1,
                    count_conversation : $('.lc_list_customer_chat .list_customer li').length > count_conversation ? $('.lc_list_customer_chat .list_customer li').length :count_conversation,
                    lastID_Conversation: $('.lc_list_customer_chat').length>0?$('.lc_list_customer_chat .list_customer li:first-child .conversation-item').attr('data-id'):0,
                    lastID_message: $('.lc_list_customer_chat').length>0?$('.lc_list_customer_chat .list_customer li:first-child .conversation-item').attr('data-id-message'):0,
                },
                success: function(json)
                {
                    if(json.conversations)
                    {
                        $(json.conversations).each(function(){
                            livechat.processMessages(this.conversation); 
                            $('.chatbox_employe_'+this.conversation.id_conversation).addClass('exist');
                        });
                        if($('.waiting_acceptance:not(.exist)').length)
                            $('.waiting_acceptance:not(.exist) .block_waiting_acceptance').html(chatbox_changed);
                        $('.waiting_acceptance').removeClass('exist');
                    }
                    if(json.reload_list)
                    {
                        if(json.reload_list==2)
                        {
                            $('.lc_list_customer_chat .tab_content_customer .list_customer').html(json.list_customer_html);
                            if($('.lc_chatbox_employe.active_expand').length)
                            {
                                $('.conversation-item[data-id="'+$('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val()+'"]').addClass('active_expand');
                            }
                        }  
                        else
                        {
                            if($('.lc_list_customer_chat .list_customer li:first-child .conversation-item').attr('data-id')==json.last_message.id_conversation)
                            {
                                $('.lc_list_customer_chat .list_customer li:first-child .conversation-item .lc_msg_time').html(json.last_message.datetime_added);
                                $('.lc_list_customer_chat .list_customer li:first-child .conversation-item .message_content').html(json.last_message.message);
                                if(json.last_message.count_message_not_seen>0)
                                {
                                    if($('.lc_list_customer_chat .list_customer li:first-child .conversation-item .count_message_not_seen').length)
                                        $('.lc_list_customer_chat .list_customer li:first-child .conversation-item .count_message_not_seen').html(json.last_message.count_message_not_seen);
                                    else
                                     {
                                        $('.lc_list_customer_chat .list_customer li:first-child .conversation-item .lc_msg_time').after('<span class="count_message_not_seen">'+json.last_message.count_message_not_seen+'</span>');
                                     }   
                                }
                                $('.lc_list_customer_chat .list_customer li:first-child .conversation-item').attr('data-id-message',json.last_message.id_message);
                                if(json.listconversations)
                                    livechat.processConvertations(json.listconversations);
                            }
                            else
                            {
                                $('.lc_list_customer_chat .tab_content_customer .list_customer').html(json.list_customer_html);
                                if($('.lc_chatbox_employe.active_expand').length)
                                {
                                    $('.conversation-item[data-id="'+$('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val()+'"]').addClass('active_expand');
                                }
                            }   
                        }
                    }
                    else
                    {
                        if(json.listconversations)
                            livechat.processConvertations(json.listconversations);
                    }
                    if($('.toogle-hide-left .total_message').html()!=json.totalMessageNoSeen)
                        livechat.playSound();
                    $('.toogle-hide-left .total_message').html(json.totalMessageNoSeen); 
                    if(json.totalMessageNoSeen>0)
                        $('.total_message').show();
                    else
                    {
                        $('.total_message').hide();
                    }
                    if(json.level_request!=level_request)
                    {
                        level_request =json.level_request;
                        if(autoLoadMasegeInFormChat)
                            clearInterval(autoLoadMasegeInFormChat);
                        if(loadListCustomerChat)
                            clearInterval(loadListCustomerChat);
                        autoLoadMasegeInFormChat = setInterval(livechat.loadMasegeInFormChat,ETS_LC_TIME_OUT_BACK_END>0?ETS_LC_TIME_OUT_BACK_END*level_request:3000*level_request);
                        loadListCustomerChat = setInterval(livechat.displayListCustomerChat,ETS_LC_TIME_OUT_BACK_END>0?ETS_LC_TIME_OUT_BACK_END*level_request:3000*level_request,1);
                    } 
                    //if(json.status_employee && $('#ets_lc_status_employee').val()!=json.status_employee)
//                    {
//                        $('#ets_lc_status_employee option').each(function(){
//                           if($(this).attr('value')==json.status_employee)
//                                $(this).attr('selected','selected');
//                           else
//                               $(this).attr('selected','');  
//                        });
//                        $('#ets_lc_status_employee').change();
//                    }                   
                }
            }); 
        }
    },
    updateDisplayFormChatBox:function()
    {
        if($('body .lc_chatbox_employe:not(.active)').length>0)
        {
            var show =false;
            $('.list_chatbook_employee').addClass('list_chatbook_employee_hide');
            if(!$('body .list_chatbox_customer_extra').length)
            {
                $('.list_chatbook_employee .lc_chatbox_employe').first().before('<span class="list_chatbox_customer_extra"></span>');
            }
            var i=-1;
            $('body .lc_chatbox_employe:not(.active)').each(function(){
               if(i>=0)
               {
                    var button = 30*i -10;
                    $(this).css('bottom',button+'px');
               }
               else
                 $(this).css('bottom','');
               i++;
               if($(this).hasClass('show'))
                    show=true;
            });
            $('.list_chatbox_customer_extra').html('<span class="number_extra_customer_chatbox'+(show?' lc_hide':'')+'">'+$('body .lc_chatbox_employe:not(.active)').length+'</span>');
        }
        else
        {
            $('.list_chatbook_employee').removeClass('list_chatbook_employee_hide');
            $('.list_chatbox_customer_extra').remove();
        }
    },
    getMap: function(latitude,longitude)
    {
        var myCenter = new google.maps.LatLng(parseFloat(latitude),parseFloat(longitude));
        var mapCanvas = document.getElementById("liveChatGoogleMap");
        var mapOptions = {center: myCenter, zoom: 9};
        var map = new google.maps.Map(mapCanvas, mapOptions);
        var marker = new google.maps.Marker({position:myCenter});
        marker.setMap(map);
        google.maps.event.addListener(marker,'click',function() {
            map.setZoom(12);
            map.setCenter(marker.getPosition());
        });
    },
    scrollLiveChat:function(id_conversation)
    {
        if($('.chatbox_employe_'+id_conversation+' .lc_msg_board .lc_msg').length)
            $('.chatbox_employe_'+id_conversation+' .lc_messages').animate({scrollTop: $('.chatbox_employe_'+id_conversation+' .lc_messages').scrollTop() + $('.chatbox_employe_'+id_conversation+' .lc_msg_board .lc_msg:last-child').position().top+300});  
    },
    resizeFormChat:function(id_conversation)
    {
        if($('.chatbox_employe_'+id_conversation).closest('.lc_chatbox_employe').hasClass('lc_chatbox_maximize'))
        {
            var height_window= $(window).height();
            if($('.chatbox_employe_'+id_conversation).closest('.lc_chatbox_employe').hasClass('wait_accept'))
                var height_content = $(window).height()-$('.chatbox_employe_'+id_conversation+' .accept_conversation').height();
            else
                var height_content = $(window).height()-$('.chatbox_employe_'+id_conversation+' .lc_text_area').height();
            var height_lc_message = height_content- $('.chatbox_employe_'+id_conversation+' .lc_heading').height();
//            if($('.chatbox_employe_'+id_conversation+' .lg_status_group').hasClass('show'))
//            {
//                height_lc_message -=$('.chatbox_employe_'+id_conversation+' .lg_status_group').height();
//            }
//            if($('.chatbox_employe_'+id_conversation+' .lc_customer_info').hasClass('show'))
//            {
//                height_lc_message -=$('.chatbox_employe_'+id_conversation+' .lc_customer_info').height();
//            }
            $('.chatbox_employe_'+id_conversation+' .lc_messages').css('height',height_lc_message+'px');
          
        } 
       else
       {
            $('.chatbox_employe_'+id_conversation+' .lc_messages').css('height','');
         
       }
        
    },
    resizeBodyFormChat:function(id_conversation)
    {
        if($('body').hasClass('lc-expand-admin'))
        {
            var height_window= $(window).height();
            var height_content = $(window).height()-$('.chatbox_employe_'+id_conversation+' .lc_text_area').height();
            var height_lc_message = height_content- $('.chatbox_employe_'+id_conversation+' .lc_heading').height();
            if($('.chatbox_employe_'+id_conversation+' .block_waiting_acceptance:not(:hidden)').length)
            {
                height_lc_message -= $('.chatbox_employe_'+id_conversation+' .block_waiting_acceptance').height();
            }
            if($('.chatbox_employe_'+id_conversation).closest('.lc_chatbox_employe').hasClass('wait_accept'))
                height_lc_message -= ($('.chatbox_employe_'+id_conversation+' .accept_conversation').height()+7);
            $('.chatbox_employe_'+id_conversation+' .lc_messages').css('height',height_lc_message+'px');
            if($('.chatbox_employe_'+id_conversation+' .info_show_expand .lg_group_departments').length==0 && $('.chatbox_employe_'+id_conversation+' .lg_status_group .lg_group_departments').length)
            {
                $('.chatbox_employe_'+id_conversation+' .info_show_expand .info_show_address').after('<div class="lg_group_departments">'+$('.chatbox_employe_'+id_conversation+' .lg_status_group .lg_group_departments').html()+'</div>');
                $('.chatbox_employe_'+id_conversation+' .lg_status_group .lg_group_departments').remove();
            }
            $('.lc_chatbox_employe.active_expand').removeClass('lc_chatbox_closed');
        } 
        else
        {
            $('.chatbox_employe_'+id_conversation+' .lc_messages').css('height','');
            if($('.chatbox_employe_'+id_conversation+' .lg_status_group .lg_group_departments').length==0 && $('.chatbox_employe_'+id_conversation+' .info_show_expand .lg_group_departments').length)
            {
                $('.chatbox_employe_'+id_conversation+' .lg_status_group .delete_conversation').after('<div class="lg_group_departments">'+$('.chatbox_employe_'+id_conversation+' .info_show_expand .lg_group_departments').html()+'</div>');
                $('.chatbox_employe_'+id_conversation+' .info_show_expand .lg_group_departments').remove();
            }
        
        }
    },
    lc_markdrap:function(){
        if($('.hide_chatbox_employe_not_active .toogle-hide-left').length)
        {
            var left_max= $(window).width()-$('.lc_heading_online .toogle-hide-left').width();
            var top_max= $(window).height()-$('.lc_heading_online .toogle-hide-left').height();
            if($('.lc_heading_online .toogle-hide-left').attr('data-left')<0)
            {
                $('.hide_chatbox_employe_not_active .toogle-hide-left').attr('data-left',0);
                $('.hide_chatbox_employe_not_active .toogle-hide-left').css('left','0px');
            }   
            if($('.lc_heading_online .toogle-hide-left').attr('data-left') >left_max)
            {
                
                $('.hide_chatbox_employe_not_active .toogle-hide-left').attr('data-left',left_max);
                $('.hide_chatbox_employe_not_active .toogle-hide-left').css('left',left_max+'px');
            }
            if($('.lc_heading_online .toogle-hide-left').attr('data-top') > top_max)
            {
                $('.hide_chatbox_employe_not_active .toogle-hide-left').attr('data-top',top_max);
                $('.hide_chatbox_employe_not_active .toogle-hide-left').css('top',top_max+'px');
            }
            if($('.lc_heading_online .toogle-hide-left').attr('data-top') <0)
                $('.hide_chatbox_employe_not_active .toogle-hide-left').css('top',0);
            var click = {
                x: 0,
                y: 0
            };
            $( ".hide_chatbox_employe_not_active .toogle-hide-left").draggable({
                cursor: "grabbing",
                connectToSortable: "body",
                containment: "body",
                scroll: false,
                start: function( event, ui ) {
                    click.x = event.clientX;
                    click.y = event.clientY;
                },
                drag: function(event, ui) {
                    var original = ui.originalPosition;
                    var left = event.clientX - click.x + original.left;
                    var top=event. clientY - click.y + original.top;
                    var max_left = $(window).width()-65;
                    var max_top = $(window).height()-65;
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
                    var top=event. clientY - click.y + original.top;
                    var max_left = $(window).width()-65;
                    var max_top = $(window).height()-65;
                    if(left>max_left)
                        left=max_left;
                    if(top>max_top)
                        top=max_top;
                    $( ".hide_chatbox_employe_not_active .toogle-hide-left").attr('data-left',left >0 ? left : 0);
                    $( ".hide_chatbox_employe_not_active .toogle-hide-left").attr('data-top',top > 0 ? top :0);
                    $( ".hide_chatbox_employe_not_active .toogle-hide-left").css('left',(left> 0 ? left :0)+'px');
                    $( ".hide_chatbox_employe_not_active .toogle-hide-left").css('top',(top> 0 ? top :0)+'px');
                    $.ajax({
                        url: ETS_LC_MODULE_URL_AJAX,
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
    displayMessage:function(message){
        $('.lc_custom_loading .squaresWaveG').hide();
        $('.lc_custom_loading .lc_custom_text_loading').hide();
        $('body').append('<div class="alert lc-alert-success"><span class="success_table"><span><i class="fa fa-check"></i>'+message+'</span></span></div>');
        $('body').removeClass('lc_loading');
        setTimeout(function(){ $('.lc-alert-success').remove(); }, 1000);
        setTimeout(function(){$('.lc_custom_loading .squaresWaveG').show(); $('.lc_custom_loading .lc_custom_text_loading').show(); }, 1500);
    },
    displayFromDepartment: function(id_conversation)
    {
        if($('.chatbox_employe_'+id_conversation+' .id_departments').length)
        {
            var id_departments= $('.chatbox_employe_'+id_conversation+' .id_departments').val();
            if(id_departments==-1 || $('.chatbox_employe_'+id_conversation+' .id_departments option[selected="selected"]').hasClass('all_employees'))
                $('.chatbox_employe_'+id_conversation+' .id_employee option').show();
            else
            {
                $('.chatbox_employe_'+id_conversation+' .id_employee .chonse_department').hide();
                $('.chatbox_employe_'+id_conversation+' .id_employee .chonse_department.department_'+id_departments).show();
            } 
           
        }
    },
    run: function()
    {
        livechat.displayListCustomerChat(0);
        $('.lc_list_customer_chat').addClass($('#ets_lc_status_employee').val());
        autoLoadMasegeInFormChat = setInterval(livechat.loadMasegeInFormChat, ETS_LC_TIME_OUT_BACK_END>0?ETS_LC_TIME_OUT_BACK_END*level_request:3000*level_request);
        $(document).on('click','textarea[name="message"]',function(){
            $(this).closest('.lc_chatbox_employe').find('.lc_heading_count_message_not_seen').removeClass('show');
        });
        $(document).on('click','.lc_togger_customer_info',function(e){
            e.stopPropagation();
            $(this).closest('.lc_chatbox_employe').find('.lc_customer_info').toggleClass('show');
            if($(this).closest('.lc_chatbox_employe').find('.lc_customer_info').hasClass('show'))
                $(this).closest('.lc_chatbox_form').find('.lg_status_group').removeClass('show');
        });
        $(document).on('click','.employee_edit_message',function(){
            var id_message = $(this).attr('data-id-message');
            var $this=$(this);
            $('.lc_msg_editing').remove();
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    employee_edit_message: 1,
                    id_message: id_message,
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        alert(json.error);
                        $this.closest('.lc_chatbox_employe').find('input[name="id_message"]').val();
                    } 
                    else
                    {
                        if($this.closest('.lc_chatbox_employe').find('.lc_send').val()!=ETS_LC_TEXT_BUTTON_EDIT)
                            $this.closest('.lc_chatbox_employe').find('.lc_send').val(ETS_LC_TEXT_BUTTON_EDIT);
                        $this.closest('.lc_chatbox_employe').find('input[name="id_message"]').val(id_message);
                        $this.closest('.lc_chatbox_employe').find('textarea[name="message"]').val(json.message);
                        $this.closest('.lc_chatbox_employe').find('textarea[name="message"]').focus();
                        $this.closest('.lc_chatbox_employe').find('textarea[name="lc_message_old"]').val(json.message);
                        if($('li.lc_msg[data-id-message="'+id_message+'"] .lc_msg_edited').length>0)
                            $('li.lc_msg[data-id-message="'+id_message+'"] .lc_msg_edited').hide();
                        if($('li.lc_msg[data-id-message="'+id_message+'"] .lc_msg_editing').length<=0)     
                            $('li.lc_msg[data-id-message="'+id_message+'"]').append('<div class="lc_msg_editing" title="'+editing_text+'">'+editing_text+'<span></span><span></span><span></span></div>');
                    }
                }
            });
        });
        $(document).on('click','.employee_delete_message',function(){
            var id_message = $(this).attr('data-id-message');
            var $this=$(this);
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    employee_delete_message: 1,
                    id_message: id_message,
                },
                success: function(json)
                { 
                    if(json.error)
                        alert(json.error);
                    else
                    {
                        if(id_message==$this.closest('.lc_chatbox_employe').find('input[name="id_message"]').val())
                        {
                           $this.closest('.lc_chatbox_employe').find('input[name="id_message"]').val('');
                           if($this.closest('.lc_chatbox_employe').find('.lc_send').val()!=ETS_LC_TEXT_SEND)
                                $this.closest('.lc_chatbox_employe').find('.lc_send').val(ETS_LC_TEXT_SEND);
                        }
                        $this.closest('.is_employee').remove();
                    }
                        
                }
            });
        });
        $(document).on('click','.pre_made_messages li.made_message',function(){
            var id_conversation = $(this).closest('.pre_made_messages').attr('data-id-conversation');
            var message= $('.lc_chatbox_employe.chatbox_employe_'+id_conversation).find('textarea[name="message"]').val()+$(this).find('.content-message').html();
            $('.lc_chatbox_employe.chatbox_employe_'+id_conversation).find('textarea[name="message"]').val(message);
            $('.lc_chatbox_employe.chatbox_employe_'+id_conversation).find('textarea[name="message"]').focus();
            $('.pre_made_messages_box').removeClass('show');
        });
        $(document).on('click','.load_made_messages',function(e){
            var $this=$(this);
            var id_conversation = $(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
            $('body').addClass('lc_loading');
            if($('body .pre_made_messages_box .pre_made_messages').length<=0)
            {
                $.ajax({
                    url: ETS_LC_MODULE_URL_AJAX,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        load_made_messages: 1,
                    },
                    success: function(json)
                    { 
                        $('body').append('<div class="pre_made_messages_box show"><div class="lc_wapper_pre_message"><div class="lc_close_pre_made"></div>'+json.html+'</div></div>')
                        $('body .pre_made_messages_box .pre_made_messages').attr('data-id-conversation',id_conversation);
                        $('body').removeClass('lc_loading');
                    }
                });
            }
            else
            {
                $('body .pre_made_messages_box').addClass('show');
                $('body .pre_made_messages_box .pre_made_messages').attr('data-id-conversation',id_conversation);
                $('body').removeClass('lc_loading');
            }
                
        });
        $(document).on('click','.lc_setting_customer',function(e){
            e.stopPropagation();
            $(this).closest('.lc_chatbox_form').find('.lg_status_group').toggleClass('show');
            if($(this).closest('.lc_chatbox_form').find('.lg_status_group').hasClass('show'))
            {
                $(this).closest('.lc_chatbox_form').find('.lc_customer_info').removeClass('show');
            }
        });
        
        $(document).on('click','.lc_chatbox_employe .more_load',function(){
            id_conversation_scroll = $(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
            livechat.getOldMessages(id_conversation_scroll);
        });
        $(document).on('click','.tab_content_customer .more_load',function(){
            livechat.loadMoreCustomerChat();
        });
        $(document).mouseup(function (e)
        {
            if($('.list_chatbook_employee .lc_chatbox_employe').length)
            {
                $('.list_chatbook_employee .lc_chatbox_employe').each(function(){
                    var id_conversation =$(this).find('input[name="id_conversation"]').val();
                    var container = $(".chatbox_employe_"+id_conversation);
                    if (!container.is(e.target)&& container.has(e.target).length === 0)
                    {
                        $(".chatbox_employe_"+id_conversation).find('input[name="message_seen"]').val('0');
                    }
                    else
                    {
                        $(".chatbox_employe_"+id_conversation).find('input[name="message_seen"]').val('1');
                        $(".chatbox_employe_"+id_conversation).find('.lc_heading_count_message_not_seen').removeClass('show');
                        if($('.conversation_item_'+id_conversation+' .count_message_not_seen').length>0)
                        {
                            $('.conversation_item_'+id_conversation+' .count_message_not_seen').remove();
                        }
                    }
                        
                    var container_message = $(".chatbox_employe_"+id_conversation+' textarea[name="message"]');
                    if (!container_message.is(e.target)&& container_message.has(e.target).length === 0)
                    {
                        $(".chatbox_employe_"+id_conversation).find('input[name="message_writing"]').val('0');
                    }
                });
                var container_form_mail = $('.lc_send_mail_form_content');
                if (!container_form_mail.is(e.target)&& container_form_mail.has(e.target).length === 0)
                {
                    $('.lc_send_mail_form_wapper').removeClass('show');
                    $('body').removeClass('show_popup_send_mail');
                }
            };
            var container_map = $('.lc_wapper_map');
            if (!container_map.is(e.target)&& container_map.has(e.target).length === 0)
            {
                $('.lc_googelmap_box').removeClass('show');
            }
            var container_pre_message = $('.lc_wapper_pre_message');
            if (!container_pre_message.is(e.target)&& container_pre_message.has(e.target).length === 0)
            {
                $('.pre_made_messages_box').removeClass('show');
            }
            var container_emotion = $('.lc_emotion');
            if (!container_emotion.is(e.target)&& container_emotion.has(e.target).length === 0)
            {
                $('.lc_emotion').removeClass('show');
            }
            var container_pop_table=$('.pop_table');
            if (!container_pop_table.is(e.target)&& container_pop_table.has(e.target).length === 0)
            {
                $('.pop_table').parent().removeClass('show').hide();
            }
        });
        $(document).on('click','body .lc_chatbox_employe:not(.active)',function(e){
            if($('body .lc_chatbox_employe.active').length>=number_form_chat)
            {              
                $('body .lc_chatbox_employe.active').first().removeClass('active');
            } 
            $(this).addClass('active');
            if($(this).hasClass('lc_chatbox_closed'))
                $(this).removeClass('lc_chatbox_closed');
            livechat.updateDisplayFormChatBox();
            $('body .lc_chatbox_employe:not(.active)').addClass('show');
        });
        $(document).on('click','body .lc_chatbox_employe .lc_close',function(e){
                e.stopPropagation();
        });
        $(document).on('keypress','.lc_text_area textarea',function(e){
            $(this).closest('.lc_text_area').find('input[name="message_writing"]').val('1');
            if(e.which == 13) { 
                if($(this).next('.list-made-messages').find('li.active').length)
                {
                    $(this).val($(this).next('.list-made-messages').find('li.active').html());
                    $(this).next('.list-made-messages').remove();
                    return false;
                }
                if(ETS_LC_ENTER_TO_SEND)
                {
                    $(this).closest('.lc_text_area').find('.lc_send').click();
                    return false;    
                }
            } 
        });
        $(document).on('focusout','.lc_text_area textarea',function(){
            if($(this).next('.list-made-messages').length && !$(this).next('.list-made-messages').is(':hover'))
            {
                $(this).next('.list-made-messages').remove();
            }
        });
        $(document).keyup(function(e) { 
            if(e.keyCode == 27) {
                if(document.documentElement.clientWidth < 767)
                {
                    $('.lc_chatbox_employe').remove();
                }
               $('.lc_googelmap_box').removeClass('show');
               $('.pre_made_messages_box').removeClass('show');
               if($('.lc_chatbox_maximize.lc_chatbox_employe').length)
               {
                    $('.lc_chatbox_maximize.lc_chatbox_employe').each(function(){
                        $(this).removeClass('lc_chatbox_maximize');
                        $(this).closest('.lc_chatbox_employe').find('.lc_messages').css('height','');
                    });
                    $('.lc_maximize').attr('title',maximize_text);
               }
               $('body').removeClass('lc_no_scroll');
               $('.lc_send_mail_form_wapper').removeClass('show');
               $('body').removeClass('show_popup_send_mail');
               $('body').removeClass('lc-expand-admin');
               if($('.lc_form_change_to_ticket_popup').length)
                    $('.lc_form_change_to_ticket_popup').hide();
            }
        });
        if(ETS_ENABLE_PRE_MADE_MESSAGE)
        {
            $(document).on('click','.item-made-messages',function(){
                $(this).parent().prev().val($(this).html());
                $(this).parent().remove();
            });
            $(document).on('keyup','.lc_text_area textarea[name="message"]',function(e){
                if(e.keyCode==38 || e.keyCode==39 || e.keyCode==40 || e.keyCode==41 )
                {
                    if(e.keyCode==40)
                    {
                        if($('.list-made-messages .item-made-messages').length)
                        {
                            if($('.list-made-messages li.item-made-messages.active').length <= 0)
                                $('.list-made-messages > li:first-child').addClass('active');
                            else if($('.list-made-messages > li.item-made-messages.active').next('li').length > 0)
                            {
                                $('.list-made-messages > li.item-made-messages.active').removeClass('active').next('li').addClass('active');
                            }
                            else
                            {
                                $('.list-made-messages > li.item-made-messages.active').removeClass('active');
                                $('.list-made-messages > li:first-child').addClass('active');
                            }
                        }
                    }
                    if(e.keyCode==38)
                    {
                        if($('.list-made-messages .item-made-messages').length)
                        {
                            if($('.list-made-messages li.item-made-messages.active').length <= 0)
                                $('.list-made-messages > li:last-child').addClass('active');
                            else if($('.list-made-messages > li.item-made-messages.active').prev('li').length > 0)
                            {
                                $('.list-made-messages > li.item-made-messages.active').removeClass('active').prev('li').addClass('active');
                            }
                            else
                            {
                                $('.list-made-messages > li.item-made-messages.active').removeClass('active');
                                $('.list-made-messages > li:last-child').addClass('active');
                            }
                        }
                    }
                    if(e.keyCode == '38' || e.keyCode == '40')
                    {
                        if($('.list-made-messages > li.active').length > 0)
                        {                    
                            $(".list-made-messages").stop().animate({scrollTop: $('.list-made-messages').scrollTop() + $('.list-made-messages > li.active').position().top});
                        }  
                        return false; 
                    }
                    
                }
                var id_conversation = $(this).closest('.lc_text_area').find('input[name="id_conversation"]').val();
                var $this = $(this);
                if(ets_made_messages && $this.val())
                {               
                    if($this.next('.list-made-messages').length)
                        $this.next('.list-made-messages').html('');
                    for (var i in ets_made_messages)
                    {
                        if (ets_made_messages[i]['short_code'].indexOf($this.val()) == 0)
                        {
                            if($this.next('.list-made-messages').length)
                            {
                                $this.next('.list-made-messages').append('<li class="item-made-messages" >'+ets_made_messages[i]['message_content']+'</li>');
                            }
                            else
                            {
                                $this.after('<ul class="list-made-messages"><li class="item-made-messages" >'+ets_made_messages[i]['message_content']+'</li></ul>');
                            }
                        }
                    }
                }
            });
        }
        $(document).on('click','.conversation-item',function(e){
            if($(this).hasClass('notclick'))
                $(this).removeClass('notclick');
        });
        $(document).on('click','.conversation-item:not(.notclick),.recen-conversation-item',function(e){
            if (document.documentElement.clientWidth < 767)
            {
                if($('.conversation-item.notclick').length)
                {
                    $('.conversation-item').removeClass('notclick');
                    return false;
                }    
                $('.lc_list_customer_chat').addClass('lc_clicked');    
            }
            $(this).addClass('notclick');
            var id_conversation= $(this).attr('data-id');
            livechat.openNewFromChat(id_conversation);
        });
        $(document).on('click','.module_form_mail_submit_btn',function(e){
            e.preventDefault();
            var $this=$(this);
            $(this).parents('form').find('.panel-footer >.bootstrap').remove();
            var formData = new FormData($(this).parents('form').get(0));
            $('body').addClass('lc_loading');
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    if(json)
                    {
                        if(json.error)
                        {
                            $('body').removeClass('lc_loading');
                            $this.parent().find('.module_form_mail_cancel_btn').before(json.error);
                        } 
                        else
                        {
                            $('.lc_send_mail_form_wapper').removeClass('show');
                            $('#content_mail').val('');
                            $('#title_mail').val('');
                            livechat.displayMessage(json.success);
                        }    
                    }
                },
                error: function(xhr, status, error)
                {
                    var err = eval("(" + xhr.responseText + ")");     
                    $.growl.error({ message: err.Message });             
                }
            });
        });
        $(document).on('click','.lc_send',function(e){
            e.preventDefault();
            var $this=$(this);
            var id_conversation = $(this).closest('.lc_text_area').find('input[name="id_conversation"]').val();
            $('.chatbox_employe_'+id_conversation+' .lc_error').addClass('lc_hide');
            $(this).closest('.lc_text_area').find('input[name="message_writing"]').val('0');
            var message = $(this).closest('.lc_text_area').find('textarea[name="message"]').val();
            var message_file = $(this).closest('.lc_text_area').find('input[name="message_file"]').val(); 
            $('.list-made-messages').remove();      
            if((message!=''|| message_file) && !($('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()&& message ==$(this).closest('.lc_text_area').find('textarea[name="lc_message_old"]').val()))
            {
                var formData = new FormData($(this).parents('form').get(0));
                $('.chatbox_employe_'+id_conversation+' textarea[name="message"]').val('');
                $('.chatbox_employe_'+id_conversation+' .message_status').hide();
                $('.chatbox_employe_'+id_conversation+' .lc_message_end_chat').hide();
                $('.chatbox_employe_'+id_conversation+' .cl_end_chat').show();
                if($('.chatbox_employe_'+id_conversation+' input[name="send_message_to_mail"]:checked').length>0)
                {
                    $('.chatbox_employe_'+id_conversation+' input[name="send_message_to_mail"]').click();
                }
                $this.closest('.lc_chatbox_employe').find('textarea[name="message"]').focus();
                $this.closest('.lc_chatbox_employe').find('input[name="message_file"]').val('');
                $this.closest('.lc_chatbox_employe').find('.form_upfile_val').remove();
                $.ajax({
                    url: $(this).parents('form').eq(0).attr('action'),
                    data: formData,
                    type: 'post',
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(json){
                        if(json)
                        {
                            if(json.checkDepartment)
                            {
                                alert(json.checkDepartment);
                                return '';
                            }
                            if(!json.error)
                            {
                                if($('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()!='' && json.employee_message_edited!='')
                                {
                                    $('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"] .lc_msg_content').html(json.employee_message_edited.message);
                                    if($('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"] .lc_msg_edited').length)
                                    $('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"] .lc_msg_edited').remove();
                                    $('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"]').append('<div class="lc_msg_edited">'+(!ETS_LC_DISPLAY_TIME?edited_text:edited_at_text+' '+json.employee_message_edited.datetime_edited)+'</div>');
                                    if($('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"] .lc_msg_editing').length)
                                        $('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"] .lc_msg_editing').remove();
                                    $('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val('');
                                }
                                if($('.chatbox_employe_'+id_conversation+' .lc_send').val()!=ETS_LC_TEXT_SEND)
                                    $('.chatbox_employe_'+id_conversation+' .lc_send').val(ETS_LC_TEXT_SEND);
                                livechat.processMessages(json);
                                $this.closest('.lc_chatbox_employe').find('textarea[name="message"]').focus();
                                $this.closest('.lc_chatbox_employe').find('input[name="message_file"]').val('');
                                $this.closest('.lc_chatbox_employe').find('.form_upfile_val').remove();
                            } 
                            else
                            {
                                $('.chatbox_employe_'+id_conversation+' .lc_error').html(json.error);
                                $('.chatbox_employe_'+id_conversation+' .lc_error').removeClass('lc_hide');
                                $('.chatbox_employe_'+id_conversation+' textarea[name="message"]').val(message);
                            }    
                        }
                    },
                    error: function(xhr, status, error)
                    {
                        var err = eval("(" + xhr.responseText + ")");     
                        $.growl.error({ message: err.Message });   
                        $('.chatbox_employe_'+id_conversation+' textarea[name="message"]').val(message);            
                    }
                });
            }
            else
            {
                if($('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val())
                {
                    if($('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"] .lc_msg_editing').length)
                        $('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"] .lc_msg_editing').remove();
                    if($('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"] .lc_msg_edited').length)
                        $('li.lc_msg[data-id-message="'+$('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val()+'"] .lc_msg_edited').show();
                    $('.chatbox_employe_'+id_conversation+' input[name="id_message"]').val('');
                    $('.chatbox_employe_'+id_conversation+' textarea[name="message"]').val('');
                    if($('.chatbox_employe_'+id_conversation+' .lc_send').val()!=ETS_LC_TEXT_SEND)
                        $('.chatbox_employe_'+id_conversation+' .lc_send').val(ETS_LC_TEXT_SEND);
                }
            }
        });
        $(document).on('click','.lc_heading',function(){
            if (document.documentElement.clientWidth < 767)
                return false;
            if($(this).closest('.lc_chatbox_employe').hasClass('active'))
            {
                var id_conversation = $(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
                if($(this).closest('.lc_chatbox_employe').hasClass('lc_chatbox_closed'))
                {
                    
                    if($('.conversation_item_'+id_conversation+' .count_message_not_seen').length>0)
                    {
                        $('.conversation_item_'+id_conversation+' .count_message_not_seen').remove();
                    }
                    $(this).closest('.lc_chatbox_employe').find('input[name="message_seen"]').val('1');
                    $(this).closest('.lc_chatbox_employe').find('.lc_heading_count_message_not_seen').removeClass('show');
                    $(this).closest('.lc_chatbox_employe').removeClass('lc_chatbox_closed').addClass('lc_chatbox_open');
                    $(this).closest('.lc_chatbox_employe').find('.lc_minimize').attr('title',hide_chat_text);
                    $(this).closest('.lc_chatbox_employe').find('.lc_minimize').html(hide_chat_text);
                    livechat.scrollLiveChat(id_conversation);
                    if($(this).closest('.lc_chatbox_employe').hasClass('lc_chatbox_maximize'))
                        $('body').addClass('lc_no_scroll');
                    $.ajax({
                        url: ETS_LC_MODULE_URL_AJAX,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            hide_conversation_chatbox: 1,
                            status:'open',
                            id_conversation:id_conversation
                        },
                        success: function(json)
                        {     
                            
                        }
                    });
                }   
                else
                {
                    $(this).closest('.lc_chatbox_employe').find('input[name="message_seen"]').val('0');
                    $(this).closest('.lc_chatbox_employe').removeClass('lc_chatbox_open').addClass('lc_chatbox_closed');
                    $(this).closest('.lc_chatbox_employe').find('.lc_minimize').attr('title',show_chat_text);
                    $(this).closest('.lc_chatbox_employe').find('.lc_minimize').html(show_chat_text);
                    if($(this).closest('.lc_chatbox_employe').hasClass('lc_chatbox_maximize'))
                        $('body').removeClass('lc_no_scroll');
                    $.ajax({
                        url: ETS_LC_MODULE_URL_AJAX,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            hide_conversation_chatbox: 1,
                            status:'close',
                            id_conversation:id_conversation
                        },
                        success: function(json)
                        {     
                            
                        }
                    });
                }
                
            }
        });  
        $(document).on('click','.lc_close',function(e){
            e.stopPropagation(); 
            var $this=$(this);
            if($(this).hasClass('close_ticket'))
            {
                if($('.lc_form_change_to_ticket_popup').length)
                    $('.lc_form_change_to_ticket_popup').hide();
                return false;
            }
            if($(this).closest('.lc_chatbox_employe').hasClass('lc_chatbox_maximize'))
            {
                if(document.documentElement.clientWidth < 767)
                {
                    if($('body').hasClass('hide_chatbox_employe_not_active'))
                        $('body').removeClass('lc_no_scroll');
                }
                else
                {
                    $('body').removeClass('lc_no_scroll');
                }
                
            }   
            var id_conversation =$this.closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();  
            $('.conversation_item_'+id_conversation).removeClass('notclick');
            $this.closest('.lc_chatbox_employe').remove();
            if($('body .lc_chatbox_employe.active').length<number_form_chat)
            {
                $('body .lc_chatbox_employe:not(.active)').last().addClass('active');
            }
            if($('.lc_chatbox_employe').length>0 && $('.lc_chatbox_employe.active_expand').length==0)
            {
                $('.lc_chatbox_employe:first').addClass('active_expand');
                $('.conversation_item_'+$('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val()).addClass('active_expand');
            }
            livechat.updateDisplayFormChatBox();
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    close_conversation_chatbox: 1,
                    id_conversation:id_conversation,
                },
                success: function(json)
                {     
                    
                }
            }); 
        });
        $(document).on('click','.changed_satatusblock',function(){
            var status_block = $(this).attr('data-status');
            var $this= $(this);
            var id_conversation =$(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    changed_satatusblock : 1,
                    status: status_block=='1' ? 0 : 1,
                    id_conversation: id_conversation,
                },
                success: function(json)
                {
                    if(json.checkDepartment)
                        alert(json.checkDepartment);
                    else
                    {
                        $this.attr('data-status',json.status);
                        if(json.status=='1')
                        $this.removeClass('disabled').addClass('enabled');
                        else
                        $this.removeClass('enabled').addClass('disabled');
                        $this.attr('title',json.text_status);
                        $this.html(json.text_status);
                    }        
                }
            }); 
        });
        $(document).on('click','.delete_conversation',function(){
            if(confirm(confirm_delete_chatbox))
            {
                var $this= $(this);
                    var id_conversation =$(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
                    $.ajax({
                        url: ETS_LC_MODULE_URL_AJAX,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            delete_conversation: 1,
                            id_conversation:id_conversation,
                        },
                        success: function(json)
                        {
                            if(json.checkDepartment)
                                alert(json.checkDepartment);
                            else
                            {
                                $this.closest('.lc_chatbox_employe').remove();
                                $('.conversation_item_'+id_conversation).parent().remove();
                                if($('body .lc_chatbox_employe.active').length<number_form_chat)
                                {
                                    $('body .lc_chatbox_employe:not(.active)').last().addClass('active');
                                }
                                livechat.updateDisplayFormChatBox();
                                if($('body').hasClass('lc-expand-admin'))
                                {
                                    if($('.lc_chatbox_employe').length==0)
                                    {
                                        if($('.list_customer li').length>0)
                                        {
                                            $('.list_customer li:first').find('.conversation-item').click();
                                            $('body').append('<div class="lc_loading_expand"></div>');
                                        }
                                        else
                                            $('body').append('<div class="lc_loading_expand_empty"><div class="title_box_empty"></div><div class="box_infor_empty"><h4 class="title_expand"><i class="fa fa-info-circle"></i>'+user_information_text+'</h4><div class="expand-admin" title="Close"></div></div></div>');
                                        
                                    }
                                    else if($('.lc_chatbox_employe.active_expand').length==0)
                                    {
                                        $('.lc_chatbox_employe.active:first').addClass('active_expand');
                                        $('.conversation_item_'+$('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val()).addClass('active_expand');
                                    }
                                }
                            }
                                     
                        }
                });
            }
        });
        $(document).on('click','.cl_end_chat',function(e){
            e.stopPropagation();
            if(confirm(confirm_end_chat))
            {
                var $this= $(this);
                var id_conversation =$(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
                $.ajax({
                    url: ETS_LC_MODULE_URL_AJAX,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        end_chat_conversation: 1,
                        id_conversation:id_conversation,
                    },
                    success: function(json)
                    {   
                        if(json.checkDepartment)
                            alert(json.checkDepartment);
                        else
                        {
                            $this.hide();
                             $this.closest('.lc_chatbox_employe').find('.message_status').hide();
                             $this.closest('.lc_chatbox_employe').find('.lc_message_end_chat').show(); 
                             $this.closest('.lc_chatbox_employe').find('.cl_end_chat').hide(); 
                             $('.chatbox_employe_'+id_conversation+' .lc_messages').stop().animate({scrollTop: $('.chatbox_employe_'+id_conversation+' .lc_messages').scrollTop() + $('.chatbox_employe_'+id_conversation+' .lc_message_end_chat').position().top});  
                             livechat.actionEndchat(id_conversation);
                        }
                              
                    }
                });
            }
        });
        $(document).on('change','.id_departments',function(){
            livechat.displayFromDepartment($(this).data('id'));
        });
        $(document).on('change','.ticket_id_departments',function(){
            var id_conversation = $(this).data('id');
            if($('.chatbox_employe_'+id_conversation+' .ticket_id_employee').length)
            {
                var id_departments= $(this).val();
                if(id_departments=='' || $('.chatbox_employe_'+id_conversation+' .ticket_id_departments option[selected="selected"]').hasClass('all_employees'))
                    $('.chatbox_employe_'+id_conversation+' .ticket_id_employee option').show();
                else
                {
                    $('.chatbox_employe_'+id_conversation+' .ticket_id_employee .chonse_department').hide();
                    $('.chatbox_employe_'+id_conversation+' .ticket_id_employee .chonse_department.department_'+id_departments).show();
                }
            }
            
        });
        $(document).on('click','.change_department',function(e){
            e.stopPropagation();
            if(confirm(confirm_change_department))
            {
                var $this= $(this);
                var id_conversation =$(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
                $.ajax({
                    url: ETS_LC_MODULE_URL_AJAX,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        change_department: 1,
                        id_conversation:id_conversation,
                        id_departments : $('#id_departments_'+id_conversation).length ? $('#id_departments_'+id_conversation).val() :0,
                        id_employee : $('#id_employee_'+id_conversation).length ? $('#id_employee_'+id_conversation).val() :0,
                    },
                    success: function(json)
                    {   
                        if(json.error)
                            alert(json.error);
                        else
                        {
                            if(json.waiting_acceptance)
                            {
                                $('.chatbox_employe_'+id_conversation).addClass('waiting_acceptance');
                                $('.conversation_item_'+id_conversation).parent().addClass('waiting_acceptance'); 
                                $('.block_waiting_acceptance .staff_name').html(json.waiting_acceptance);
                                setTimeout(function(){ livechat.resizeBodyFormChat(id_conversation); }, 1000);
                                
                            }
                            
                        }   
                    }
                });
            }
            return false;
        });
        $(document).on('click','.cancel_acceptance',function(){
            var id_conversation =$(this).data('id');
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    cancel_acceptance: 1,
                    id_conversation:id_conversation,
                },
                success: function(json)
                {   
                    if(json.error)
                        alert(json.error);
                    $('.chatbox_employe_'+id_conversation).removeClass('waiting_acceptance');
                    $('.conversation_item_'+id_conversation).parent().removeClass('waiting_acceptance');   
                }
            });
            return false;
        });
        $(document).on('click','.changed_satatuscaptcha',function(){
            var status_block = $(this).attr('data-status');
            var $this= $(this);
            var id_conversation =$(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    changed_satatuscaptcha: 1,
                    status: status_block=='1'?0:1,
                    id_conversation:id_conversation,
                },
                success: function(json)
                {
                    if(json.checkDepartment)
                    {
                        alert(json.checkDepartment);
                    }
                    else
                    {
                        $this.attr('data-status',json.status);
                         $this.attr('data-status',json.status);
                         if(json.status=='1')
                            $this.removeClass('disabled').addClass('enabled');
                         else
                            $this.removeClass('enabled').addClass('disabled');
                         $this.attr('title',json.text_status);
                         $this.html(json.text_status); 
                    }
                              
                }
            }); 
        });
        $(document).on('click','.lc_close_map',function(e){
            $('.lc_googelmap_box').removeClass('show');
        });
        $(document).on('click','.lc_close_pre_made',function(){
            $('.pre_made_messages_box').removeClass('show');
        });
        $(document).on('click','.tab_customer >div',function(e){
            $('.lc_list_customer_chat').removeClass('list_customer_all').removeClass('list_customer_active').removeClass('list_customer_archive').addClass($(this).attr('data-content'));
            $('#input_search_customer_chat').val('');
            $('.lc_list_customer_chat').removeClass('list_customer_search');
            livechat.displayListCustomerChat(0);
        });
        $(document).on('keyup','#input_search_customer_chat',function(){
            if($(this).val().length>=1)
            {
                $('.lc_list_customer_chat').addClass('list_customer_search');
                livechat.displayListCustomerChat(0);  
            }
            else
            {
                $('.lc_list_customer_chat').removeClass('list_customer_search');
                livechat.displayListCustomerChat(0);
            }
        });
        $(document).on('click','.list_customer .archive_customer',function(e){
            var id_conversation = parseInt($(this).attr('data-id'));
            var $this= $(this);
            var customer_all=0;
            var customer_archive=0;
            if($('.lc_list_customer_chat').hasClass('list_customer_all') || $('.lc_list_customer_chat').hasClass('list_customer_search'))
            {
               customer_all=1;
               customer_archive=0;
            }
            else
            {
                if($('.lc_list_customer_chat').hasClass('list_customer_archive'))
                {
                    customer_all=0;
                    customer_archive=1;
                }
            }
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    add_archive_customer_chat: 1,
                    id_conversation:id_conversation,
                    customer_all:customer_all,
                    customer_archive:customer_archive,
                    count_conversation : $('.lc_list_customer_chat .list_customer li').length > count_conversation ? $('.lc_list_customer_chat .list_customer li').length :count_conversation,
                    refresh:1,
                },
                success: function(json)
                {
                    if(json.html)
                    {
                        if($('.lc_list_customer_chat').length>0)
                        {
                            $('.lc_list_customer_chat .tab_content_customer .list_customer').html(json.html);
                        }
                        else
                        {
                            $('body').append(json.html);
                        }
                    }                      
                }
            });
        });
        $(document).on('click','.list_customer .active_customer',function(e){
            var id_conversation = parseInt($(this).attr('data-id'));
            var $this= $(this);
            var customer_all=0;
            var customer_archive=0;
            if($('.lc_list_customer_chat').hasClass('list_customer_all') || $('.lc_list_customer_chat').hasClass('list_customer_search'))
            {
               customer_all=1;
               customer_archive=0;
            }
            else
            {
                if($('.lc_list_customer_chat').hasClass('list_customer_archive'))
                {
                    customer_all=0;
                    customer_archive=1;
                }
            }
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    add_active_customer_chat: 1,
                    id_conversation:id_conversation,
                    customer_all:customer_all,
                    customer_archive:customer_archive,
                    count_conversation : $('.lc_list_customer_chat .list_customer li').length > count_conversation ? $('.lc_list_customer_chat .list_customer li').length :count_conversation,
                    refresh:1,
                },
                success: function(json)
                {
                    if(json.html)
                    {
                        if($('.lc_list_customer_chat').length>0)
                        {
                            $('.lc_list_customer_chat .tab_content_customer .list_customer').html(json.html);
                        }
                        else
                        {
                            $('body').append(json.html);
                        }
                    }                     
                }
            });
        });
        $(document).on('click','.list_chatbox_customer_extra',function(e){
            $('.lc_chatbox_employe:not(.active)').toggleClass('show'); 
            $('.number_extra_customer_chatbox').toggleClass('lc_hide');
        });
        $(document).on('change','#ets_lc_status_employee',function(){
            var $this= $(this);
            if($this.val()=='online' || $this.val()=='foce_online')
            {
                $('.title_class').attr('title',online_text);
            }
            if($this.val()=='do_not_disturb')
            {
                $('.title_class').attr('title',busy_text);
            }
            if($this.val()=='invisible')
            {
                $('.title_class').attr('title',invisible_text);
            }
            if($this.val()=='offline')
            {
                $('.title_class').attr('title',offline_text);
                $('body').addClass('lc_chatbox_backend_offline');
            }
            else
                $('body').removeClass('lc_chatbox_backend_offline');
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'html',
                data: {
                    change_status_employee: $(this).val(),
                },
                success: function(json)
                { 
                    $('.lc_list_customer_chat').removeClass('online').removeClass('do_not_disturb').removeClass('invisible').removeClass('offline').addClass(($this.val()=='foce_online' || $this.val()==null)? 'online':$this.val());
                    livechat.displayListCustomerChat(1);
                    livechat.loadMasegeInFormChat();
                }
            });
        });
        if (document.documentElement.clientWidth < 767)
        {
            $(document).on('click','.lc_emotion',function(e){
                if ($(this).hasClass('show')){
                    $(this).removeClass('show'); 
                } else {
                    $('.lc_emotion').removeClass('show');
                    $(this).addClass('show');
                }
            });
        }
        $(document).on('click','.lc_emotion li',function(e){
            e.stopPropagation();
            var message= $(this).closest('.lc_chatbox_employe').find('textarea[name="message"]').val()+$(this).attr('data-emotion');
            $(this).closest('.lc_chatbox_employe').find('textarea[name="message"]').val(message);
            $(this).closest('.lc_chatbox_employe').find('textarea[name="message"]').focus();
            if (document.documentElement.clientWidth < 767)
            {
                $('.lc_emotion').removeClass('show');
            }
        });
        $(document).on('click','.lc_list_customer_chat .lc_heading',function(e){
            $('.lc_list_customer_chat').removeClass('lc_left_hide');
            $('.toogle-hide-left').attr('title',close_text);
            $('.lc_heading').attr('title','');
            $('body').removeClass('hide_chatbox_employe_not_active'); 
            if (document.documentElement.clientWidth < 767)
            {
                $('body').addClass('lc_no_scroll');
            }
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'html',
                data: {
                    change_status_display_admin: 1,
                    status : $('.lc_list_customer_chat').hasClass('lc_left_hide')?0:1,
                },
                success: function(json)
                {  
                } 
            });
        });
        $(document).on('click','.toogle-hide-left',function(e){
            e.stopPropagation();
            $('.lc_list_customer_chat').toggleClass('lc_left_hide');
            if($('.lc_list_customer_chat').hasClass('lc_left_hide'))
            {
                $('.toogle-hide-left').attr('title',open_text);
                $('.lc_heading').attr('title',open_text);
            }
            else
            {
                $('.toogle-hide-left').attr('title',close_text);
                $('.lc_heading').attr('title','');
            }
            $('body').toggleClass('hide_chatbox_employe_not_active'); 
            livechat.lc_markdrap();
            if (document.documentElement.clientWidth < 767)
            {
                if($('body').hasClass('hide_chatbox_employe_not_active') && $('.lc_chatbox_employe.lc_chatbox_maximize').length==0)
                    $('body').removeClass('lc_no_scroll');
                else
                    $('body').addClass('lc_no_scroll');
            }
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'html',
                data: {
                    change_status_display_admin: 1,
                    status : $('.lc_list_customer_chat').hasClass('lc_left_hide')?0:1,
                },
                success: function(json)
                {  
                    if($('.lv_admin_statistic').length>0)
                        createChart();
                        $('.lc_ticket_recently_list').slick('refresh'); 
                } 
            });
            
        });
        $(document).on('click','#ETS_LC_CUSTOMER_AVATA-images-thumbnails a',function(){
            return confirm(confirm_delete_avata);
        });
        $(document).on('click','#ETS_LC_COMPANY_LOGO-images-thumbnails a',function(){
            return confirm(confirm_delete_logo);
        });
    }
}
$(document).ready(function(){
    $(document).ajaxStart(function() {
      $("#ajax_running").remove();
      setTimeout(function(){ $("#ajax_running").hide(); }, 500); 
    });
    if(is_lc_RTL)
        $('body').addClass('lc_body_rtl');
    if($('input[name="ETS_LC_STAFF_ACCEPT"]:checked').val()==1)
        $('input[name="ETS_ENABLE_AUTO_REPLY"]').attr('disabled','disabled');
    $(document).on('click','input[name="ETS_LC_STAFF_ACCEPT"]',function(){
       if($(this).val()==1) 
            $('input[name="ETS_ENABLE_AUTO_REPLY"]').attr('disabled','disabled');
       else
            $('input[name="ETS_ENABLE_AUTO_REPLY"]').removeAttr('disabled');
    });
    var window_width= $(window).width()-250-50;
    number_form_chat = parseInt(window_width/360);
    if(number_form_chat==0)
        number_form_chat=1;
    livechat.run();
    if(converation_opened&&document.documentElement.clientWidth>=767)
    {
        $.each(converation_opened,function(i,id_conversation){
            livechat.openNewFromChat(id_conversation);
        });
    }
    if (document.documentElement.clientWidth < 767)
    { 
       document.querySelector("meta[name=viewport]").setAttribute('content','width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
    }
    $(document).on('click','.lc_sound.disable',function(){
        if (document.documentElement.clientWidth < 767)
        { 
            document.getElementById("lg_ets_sound").play();
        }
        $('.lc_list_customer_chat .lc_sound').removeClass('disable').addClass('enable');
    }); 
    $(document).on('click','.expand-admin,.view-all-conversations',function(){
        $('body').toggleClass('lc-expand-admin');
        if($('.lc_loading_expand_empty').length)
            $('.lc_loading_expand_empty').remove();
        if($('.lc_form_change_to_ticket_popup').length)
            $('.lc_form_change_to_ticket_popup').hide();
        if($('body').hasClass('lc-expand-admin'))
        {
            if($('.lc_chatbox_employe').length==0)
            {
                if($('.list_customer li').length>0)
                {
                    $('.list_customer li:first').find('.conversation-item').click();
                    $('body').append('<div class="lc_loading_expand"></div>');
                }
                else
                    $('body').append('<div class="lc_loading_expand_empty"><div class="title_box_empty"></div><div class="box_infor_empty"><h4 class="title_expand"><i class="fa fa-info-circle"></i>'+user_information_text+'</h4><div class="expand-admin" title="Close"></div></div></div>');
                
            }
            else if($('.lc_chatbox_employe.active_expand').length==0)
            {
                $('.lc_chatbox_employe.active:first').addClass('active_expand');
                $('.conversation_item_'+$('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val()).addClass('active_expand');
            }
        }
        livechat.resizeBodyFormChat($('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val());
        return false;
    });
    $(document).on('click','.lc_maximize',function(e){
       e.stopPropagation(); 
       var id_conversation =$(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
       if($(this).closest('.lc_chatbox_employe').hasClass('lc_chatbox_maximize'))
       {
            $(this).closest('.lc_chatbox_employe').removeClass('lc_chatbox_maximize');
            $(this).attr('title',maximize_text);
            $('body').removeClass('lc_no_scroll');
            livechat.resizeFormChat(id_conversation);
       } 
       else
       {
          $('body').toggleClass('lc-expand-admin');
           if($('body').hasClass('lc-expand-admin'))
           {
                $('.lc_chatbox_employe').removeClass('active_expand');
                $('.conversation-item').removeClass('active_expand');
                $('.lc_chatbox_employe.chatbox_employe_'+id_conversation).addClass('active_expand');
                $('.conversation_item_'+id_conversation).addClass('active_expand');
           } 
           livechat.resizeBodyFormChat(id_conversation);
       }
        
       //if(!$(this).closest('.lc_chatbox_employe').hasClass('lc_chatbox_maximize'))
//       {
//            $(this).closest('.lc_chatbox_employe').addClass('lc_chatbox_maximize');
//            $(this).attr('title',minimize_text);
//            $('body').addClass('lc_no_scroll');
//       } 
//       else
//       {
//            $(this).closest('.lc_chatbox_employe').removeClass('lc_chatbox_maximize');
//            $(this).attr('title',maximize_text);
//            $('body').removeClass('lc_no_scroll');
//       }
//       livechat.resizeFormChat($(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val()); 
    });
    $(document).on('click','.lc_sound.enable',function(){
        $('.lc_list_customer_chat .lc_sound').removeClass('enable').addClass('disable');
    });
    $( window ).resize(function() {
        if($('.list_chatbook_employee .lc_chatbox_employe').length)
        {
            if (document.documentElement.clientWidth < 767)
            {
                if($('.lc_chatbox_employe.lc_chatbox_maximize').length==0)
                $('body .lc_chatbox_employe.active').last().addClass('lc_chatbox_maximize');
                $('body').removeClass('lc-expand-admin');
            }
        }
        $('.list_chatbook_employee .lc_chatbox_employe').each(function(){
            id_conversation =$(this).find('input[name="id_conversation"]').val();
            livechat.scrollLiveChat(id_conversation);
            livechat.resizeFormChat(id_conversation);
            livechat.resizeBodyFormChat(id_conversation);
        });
    });
    $(document).on('click','.accept_submit',function(e){
        var $this = $(this); 
        var id_conversation=$this.data('id');
        $('.chatbox_employe_'+id_conversation+' .lc_error').addClass('lc_hide');
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                accept_submit: 1,
                id_conversation : id_conversation,
            },
            success: function(json)
            { 
                if(json.error)
                {
                    alert(json.error);
                    $('.lc_chatbox_employe.chatbox_employe_'+id_conversation).remove();
                    $('.conversation-item.conversation_item_'+id_conversation).parent().remove();
                }
                else
                {
                    $('.chatbox_employe_'+id_conversation).removeClass('wait_accept').removeClass('has_changed');
                    $('.conversation_item_'+id_conversation).parent().removeClass('wait_accept').removeClass('has_changed');
                    $('body .chatbox_employe_'+id_conversation+' textarea[name="message"]').focus();
                    livechat.resizeBodyFormChat(id_conversation);
                }
            }
        });
        return false;
    });
    $(document).on('click','.decline_submit',function(e){
        $this = $(this);
        var id_conversation= $this.data('id');
        $('.chatbox_employe_'+id_conversation+' .lc_error').addClass('lc_hide');
        if(!confirm(confirm_decline_submit))
            return false;
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                decline_submit: 1,
                id_conversation : id_conversation,
            },
            success: function(json)
            { 
                if(json.error)
                    alert(json.error);
                else
                {
                    if(json.id_profile==1)
                    {
                        $('.lc_chatbox_employe.chatbox_employe_'+id_conversation+' .lc_message_end_chat').after('<div class="conversation-decline">'+decline_text+'</div>');
                        $('.lc_chatbox_employe.chatbox_employe_'+id_conversation).removeClass('wait_accept');
                    }
                    else
                    {
                        $('.lc_chatbox_employe.chatbox_employe_'+id_conversation).remove();
                        $('.conversation-item.conversation_item_'+id_conversation).parent().remove();
                        if($('body').hasClass('lc-expand-admin'))
                        {
                            if($('.lc_chatbox_employe').length==0 && $('.list_customer li').length>0)
                            {
                                $('.list_customer li:first').find('.conversation-item').click();
                            }
                            else if($('.lc_chatbox_employe.active_expand').length==0)
                            {
                                $('.lc_chatbox_employe.active:first').addClass('active_expand');
                                $('.conversation_item_'+$('.lc_chatbox_employe.active_expand').find('input[name="id_conversation"]').val()).addClass('active_expand');
                            }
                        }
                        
                    }
                    
                }
            }
        });
        return false;
    });
    $(document).on('click','.view-conversation',function(){
        var $this = $(this);
        var id_conversation= $this.data('id');
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                view_conversation: 1,
                id_conversation : id_conversation,
            },
            success: function(json)
            { 
                if(json.error)
                    alert(json.error);
                else
                {
                    if($this.parents('.conversation-history').eq(0).next('.conversation-message').length)
                    {
                        $this.parents('.conversation-history').eq(0).next('.conversation-message').html(json.converation_messages);
                    }
                    else
                    {
                        $this.parents('.conversation-history').eq(0).after('<div class="conversation-message">'+json.converation_messages+'</div>');
                    }
                    $this.parents('.info_show_expand').eq(0).addClass('show_message');
                }
            }
        });
        return false;
    });
    $(document).on('click','.back-conversation-list',function(){
        $(this).parents('.info_show_expand').eq(0).removeClass('show_message');
        return false;
    });
    $(document).on('click','.conversation-history .links a',function(){
        $this = $(this);
        var id_conversation= $(this).closest('.lc_chatbox_employe').find('input[name="id_conversation"]').val();
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: $this.attr('href')+'&id_conversation='+id_conversation,
            success: function(json)
            { 
                if(json.error)
                    alert(json.error);
                else
                {
                    $this.parents('.conversation-history').eq(0).html(json.conversation_history);
                }
            }
        });
        return false;
    });
    $(document).on('change','.conversation_note',function(e){
        $this = $(this);
        var id_conversation= $this.data('id');
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                conversation_note: $this.val(),
                id_conversation : id_conversation,
            },
            success: function(json)
            { 
                if(json.error)
                    alert(json.error);
                else
                {
                    $this.parent().append('<p class="alert alert_sussec_note">'+succesfull_noted+'</p>');
                    setTimeout(function(){ $('.alert_sussec_note').remove(); }, 3000);
                }
            }
        });
    });
    $(document).on('change','input[name="message_file"]',function(){
        var filename = $(this).val().split('\\').pop();
        filesize= this.files[0].size/1048576;
        if(!ETS_LC_MAX_FILE_MS || filesize<=ETS_LC_MAX_FILE_MS)
        {
            if($(this).parents('.form_upfile').eq(0).next('.form_upfile_val').length)
            {
                $(this).parents('.form_upfile').eq(0).next('.form_upfile_val').addClass('show').find('.file_name').html(filename);
            }
            else
            {
                $(this).parents('.form_upfile').eq(0).after('<div class="form_upfile_val show"><div class="file_name">'+filename+'</div><button class="delete_file_message" title="Delete">'+delete_text+'</button></div>');
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
        $(this).parents('.form_upfile').eq(0).next('.form_upfile_val').next().focus();
    });
    $(document).on('click','.delete_file_message',function(){
        $(this).parents('.form_upfile_val').eq(0).removeClass('show').prev('.form_upfile').find('input').val('');
        $(this).parents('.form_upfile_val').eq(0).removeClass('show').remove();
    });
    $(document).on('click','.lc_send_mail_button',function(){
        $(this).closest('.lc_chatbox_employe').find('.lc_send_mail_form_wapper').addClass('show');
        $('body').addClass('show_popup_send_mail');
    });
    $(document).on('click','.module_form_mail_cancel_btn,.lc_close_form_mail',function(){
        $('.lc_send_mail_form_wapper').removeClass('show');
        $('body').removeClass('show_popup_send_mail');
    });
    $(document).on('click','.btn_change_to_ticket',function(){
        $('.lc_form_change_to_ticket_popup').show();
        return false;
    });
    $(document).on('click','.cancel_change_to_ticket',function(){
        $('.lc_form_change_to_ticket_popup').hide();
        return false;
    });
    $(document).on('click','.submit_change_to_ticket',function(){
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('change_conversation_to_ticket',1);
        $this = $(this);
        $('body').addClass('lc_loading');
        $('.lc_form_change_to_ticket .bootstrap').remove();
        $.ajax({
            url: $(this).parents('form').eq(0).attr('action'),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                if(json)
                {
                    if(json.errors)
                    {
                        $this.closest('.lc_form_change_to_ticket').append(json.errors);
                        $('body').removeClass('lc_loading');
                    }
                    else
                    {
                        livechat.displayMessage(json.success);
                        $this.closest('.lc_chatbox_form').find('.change_to_ticket').html(ticket_text+' <a href="'+json.link_ticket+'" class="ticket-conversation">#'+json.subject_ticket+'</a> '+create_ticket)
                        $('.lc_form_change_to_ticket_popup').remove();
                    }    
                }
            },
            error: function(xhr, status, error)
            {
                var err = eval("(" + xhr.responseText + ")");     
                $.growl.error({ message: err.Message });   
                $('.chatbox_employe_'+id_conversation+' textarea[name="message"]').val(message);            
            }
        });
        return false;
    });
    if ( $('.lc_ticket_recently_list').length > 0 ){
        $('.lc_ticket_recently_list').slick({
          infinite: true,
          slidesToShow: 1,
          dots: true,
          arrows: false,
          autoplay: true,
          slidesToScroll: 1
        });
    }
});