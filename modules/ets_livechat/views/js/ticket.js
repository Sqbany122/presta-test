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
$(document).ready(function(){
    $('input[name="search_customer_ticket"]').autocomplete(link_customer_search,{
		minChars: 1,
		autoFill: true,
		max:20,
		matchContains: true,
		mustMatch:true,
		scroll:false,
		cacheLength:0,
		formatItem: function(item) {
			return item[0]+' - '+item[1];
		}
	}).result(addCustomerToTicket);
    $(document).on('click','.delete_customer_search',function(){
        $(this).closest('.customer_name_search').remove();
        $('#search_customer_ticket').val('');
        $('#id_customer_ticket').val('');
        if($('.is_contact_mail').length && $('.is_contact_mail:first').attr('readonly'))
        {
            $('.is_contact_mail:first').val('');
            if(!$('#search_customer_ticket').hasClass('readonly'))
                $('.is_contact_mail:first').removeAttr('readonly');
        } 
        if($('.is_contact_name').length && $('.is_contact_name:first').attr('readonly'))
        {
            $('.is_contact_name:first').val('');
            if(!$('#search_customer_ticket').hasClass('readonly'))
                $('.is_contact_name:first').removeAttr('readonly');
        }
        if($('.is_customer_phone_number').length && $('.is_customer_phone_number:first').attr('readonly'))
        {
            $('.is_customer_phone_number:first').val('');
            if(!$('#search_customer_ticket').hasClass('readonly'))
                $('.is_customer_phone_number:first').removeAttr('readonly');
        } 
    });
    $(document).on('click','#id_departments_ticket',function(){
        lc_displayFromDepartmentTicket();
    });
    $(document).on('click','.lc_form_submit_close',function(){
       $('.lc_popup').hide(); 
    });
    lc_displayFromDepartmentTicket();
//    $('#ticket_note').focus();
    $(document).on('click','.lc_send_message_ticket',function(){
        if($('#ticket_file').val()=='' && $('#ticket_note').val()=='' && !$('body').hasClass('lc_loading_ticket'))
            return false;
        $('.lc_ticket_message').prev('.bootstrap').remove();
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('lc_send_message_ticket',1);
        $('body').addClass('lc_loading_ticket');
        $('.module_error').parent().remove();
        $.ajax({
            url: $(this).parents('form').eq(0).attr('action'),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                
                if(json.messages)
                {
                    $('.lc_note_message').removeClass('nocomment');
                    $.each(json.messages,function(i,msg){
                        var msgHtml = '<li class="lc_msg '+(msg.id_employee != 0 ? 'is_employee':'is_customer')+'" data-id-message="'+msg.id_note+'">'
                        +'<div class="lc_sender">'+(msg.id_employee != 0 ? (msg.employee_avata?'<div class="avata'+(ETS_LC_AVATAR_IMAGE_TYPE=='square'?' lc_avatar_square':'')+'"><img src="'+msg.employee_avata+'" title="'+msg.employee_name+'">'+(msg.employee_name?'<span title="'+msg.employee_name+'">'+msg.employee_name_hide+'</span>':'')+'</div>':''): (msg.customer_avata?'<div class="avata'+(ETS_LC_AVATAR_IMAGE_TYPE=='square'?' lc_avatar_square':'')+'"><img src="'+msg.customer_avata+'" title="'+msg.customer_name+'">'+(msg.customer_name?'<span title="'+msg.customer_name+'">'+msg.customer_name_hide+'</span>':'')+'</div>':''))+'</div>'
                        +'<div class="lc_msg_content">'+msg.note+'</div>'
                        +'<div class="lc_msg_time">'+msg.date_add+'</div>'
                        +'</li>';               
                        if($('.ticket-list-messages > .lc_msg[data-id-message="'+msg.id_note+'"]').length <= 0)
                        {
                            var msgAdded = false;
                            $($('.ticket-list-messages > .lc_msg').get().reverse()).each(function(){                        
                                if(parseInt($(this).attr('data-id-message')) < parseInt(msg.id_note))
                                {
                                    $(this).after(msgHtml);
                                    msgAdded = true;
                                    return false;   
                                }
                            });
                            if(!msgAdded)
                            {
                                $('.ticket-list-messages').prepend(msgHtml); 
                            }
                                   
                        }                    
                    });
                }
                if(json.success)
                {
                    $('#ticket_file').val('');
                    $('#ticket_note').val('');
                    $('.lc_custom_loading .squaresWaveG').hide();
                    $('.lc_custom_loading .lc_custom_text_loading').hide();
                    $('body').append('<div class="alert lc-alert-success"><span class="success_table"><span><i class="fa fa-check"></i>'+json.success+'</span></span></div>');
                    $('body').removeClass('lc_loading');
                    setTimeout(function(){ $('.lc-alert-success').remove(); }, 1000);
                    setTimeout(function(){$('.lc_custom_loading .squaresWaveG').show(); $('.lc_custom_loading .lc_custom_text_loading').show(); }, 1500);
                }
                if(json.error)
                {
                    $('.lc_note_message').after(json.error);
                }
                $('body').removeClass('lc_loading_ticket');
            },
            error: function(xhr, status, error)
            {
                var err = eval("(" + xhr.responseText + ")");     
                $.growl.error({ message: err.Message });             
            }
        });
        return false;
    });
//    $(document).on('keypress','#ticket_note',function(e){
//        if(e.which == 13 && $('#ticket_note').val()!='') { 
//           $('.lc_send_message_ticket').click();
//           return false;
//        }
//    });
    $(document).on('click','.change_priority',function(){
        $('.lc_form_priority').show();
        return false;
    });
    $(document).on('click','.transfer_ticket',function(){
        $('.lc_form_transfer_ticket').show();
        return false;
    });
    $(document).on('click','.cancel_transfer_ticket',function(){
        $('.lc_form_transfer_ticket').hide();
        return false; 
    });
    $(document).on('click','.cancel_change_priority',function(){
        $('.lc_form_priority').hide(); 
        return false;
    });
    $(document).on('click','.lc_close',function(){
        $('.lc_form_priority').hide();
        $('.lc_form_transfer_ticket').hide();
        if($('.lc_form_submit_new_ticket').length)
            $('.lc_form_submit_new_ticket').hide();
    });
    $(document).on('click','#new_ticket_bt',function(){
        if($('#form_ticket').val()!= '--')
        {
            window.location.href=$('#form_ticket').val();
        }
        return false;
    });
    $(document).on('click','.submit_new_ticket_bt',function(){
        $('.lc_form_submit_new_ticket').show();
    });
    $('.ticket_readed_all').click(function(){
        if (this.checked) {
           $('.ticket_readed').prop('checked', true);
        } else {
            $('.ticket_readed').prop('checked', false);
        } 
        lc_displayBulkAction();
    });
    $(document).on('click','.ticket_readed',function(){
        lc_displayBulkAction();
    });
    $(document).on('change','#bulk_action_ticket',function(){
        $('.alert.alert-success').hide();
        if($(this).val()=='')
            return false;
        if($('#bulk_action_ticket').val()=='delete_selected')
        {
            var result = confirm(confirm_delete_ticket);
            if(!result)
            {
                $(this).val('');
                return false;
            } 
        }
        $('body').addClass('lc_loading');
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('submitBulkActionTicket', 1);
        $.ajax({
            url: '',
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                //$('body').removeClass('lc_loading');
                if(json.url_reload)
                   window.location.href=json.url_reload; 
                else
                    location.reload();
                //if($('#bulk_action_ticket').val()=='delete_selected')
//                {
//                    if(json.url_reload)
//                       window.location.href=json.url_reload; 
//                    else
//                        location.reload();
//                }
//                else
//                {
//                    for(var k in json.messages)
//                    {
//                        $('#tr-message-'+k).html(json.messages[k]);
//                        $('#tr-message-'+k+' .message_readed').prop('checked', true);
//                        if($('#bulk_action_message').val()=='mark_as_read')
//                            {
//                                $('#tr-message-'+k).removeClass('no-reaed');
//                            }
//                        else
//                           $('#tr-message-'+k).addClass('no-reaed'); 
//                    }
//                    $('.count_messages').html(json.count_messages);
//                    if(json.count_messages>0)
//                        $('.count_messages').removeClass('hide');
//                    else
//                        $('.count_messages').addClass('hide');
//                    displayBulkAction();
//                    $('#bulk_action_message').val('');
//                }
            },
            error: function(xhr, status, error)
            {
                $('body').removeClass('lc_loading');
                var err = eval("(" + xhr.responseText + ")");     
                alert(err.Message);               
            }
        });
    });
    $(document).on('change','input[name="ticket_file"],.lc_form_add_ticket input[type="file"]',function(){
        var filename = $(this).val().split('\\').pop();
        filesize= this.files[0].size/1048576;
        if(!ETS_LC_MAX_FILE_MS || filesize<=ETS_LC_MAX_FILE_MS)
        {
            if($(this).parent().hasClass('lc_upload_file'))
            {
                if($(this).parents('.lc_upload_file').eq(0).next('.form_upfile_val').length)
                {
                    $(this).parents('.lc_upload_file').eq(0).next('.form_upfile_val').addClass('show').find('.file_name').html($(this).val().split('\\').pop());
                }
                else
                {
                    $(this).parents('.lc_upload_file').eq(0).after('<div class="form_upfile_val show"><div class="file_name">'+$(this).val().split('\\').pop()+'</div><button class="delete_file_note" title="Delete">'+delete_text+'</button></div>');
                }
            }
            else
            {
                if($(this).parents('.form_upfile').eq(0).next('.form_upfile_val').length)
                {
                    $(this).parents('.form_upfile').eq(0).next('.form_upfile_val').addClass('show').find('.file_name').html($(this).val().split('\\').pop());
                }
                else
                {
                    $(this).parents('.form_upfile').eq(0).after('<div class="form_upfile_val show"><div class="file_name">'+$(this).val().split('\\').pop()+'</div><button class="delete_file_note" title="Delete">'+delete_text+'</button></div>');
                }
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
        $('#ticket_note').focus();
    });
    $(document).on('click','.delete_file_note',function(){
        $(this).parents('.form_upfile_val').eq(0).removeClass('show').prev().find('input').val('');
        $(this).parents('.form_upfile_val').eq(0).removeClass('show').remove();
    });
    $(document).keyup(function(e) { 
        if(e.keyCode == 27) {
            $('.lc_form_submit_new_ticket').hide();
        }
    });
    $(document).mouseup(function (e){
        var container = $('.lc_form_priority form');
        if (!container.is(e.target)&& container.has(e.target).length === 0)
        {
            $('.lc_form_priority').hide();
        }
        var container_transfer = $('.lc_form_transfer_ticket form');
        if (!container_transfer.is(e.target)&& container_transfer.has(e.target).length === 0)
        {
            $('.lc_form_transfer_ticket').hide();
        }
        var conainer_new_ticket= $('.lc_form_submit_new_ticket_content');
        if (!conainer_new_ticket.is(e.target)&& conainer_new_ticket.has(e.target).length === 0)
        {
            $('.lc_form_submit_new_ticket').hide();
        }
    });
});
function lc_displayBulkAction()
{
    if($('.ticket_readed:checked').length )
    {
        $('#bulk_action_ticket').show();
    }
    else
    {
        $('#bulk_action_ticket').hide();
    }
    if($('.ticket_readed:checked').length==$('.ticket_readed[data="1"]:checked').length)
        $('#bulk_action_ticket option[value="mark_as_read"]').hide();
    else
        $('#bulk_action_ticket option[value="mark_as_read"]').show();
    if($('.ticket_readed:checked').length==$('.ticket_readed[data="0"]:checked').length)
        $('#bulk_action_ticket option[value="mark_as_unread"]').hide();
    else
        $('#bulk_action_ticket option[value="mark_as_unread"]').show();
}
function lc_displayFromDepartmentTicket()
{
    var id_departments= $('#id_departments_ticket').val();
    if($('#id_departments_ticket').length)
    {
        if(id_departments==-1 || $('#id_departments_ticket option[selected="selected"]').hasClass('all_employees'))
            $('.id_employee_ticket option').show();
        else
        {
            $('.id_employee_ticket .chonse_department').hide();
            $('.id_employee_ticket .chonse_department.department_'+id_departments).show();
        }
    }
    else
        $('.id_employee_ticket option').show();
     
}
var addCustomerToTicket=function(event,data,formatted)
{
    if (data == null)
    {
       return false;
    }
	var customerId = data[3];
	var customerName = data[0];
    var customerEmail = data[1];
    var customerPhone = data[2];
    $('#search_customer_ticket').val(customerName);
    $('#search_customer_ticket').before('<div class="customer_name_search">'+customerName+'<span class="delete_customer_search"><i class="icon-trash"></i></span></div>')
    $('#id_customer_ticket').val(customerId);
    if($('.is_contact_mail').length && customerEmail)
    {
        $('.is_contact_mail:first').val(customerEmail);
        $('.is_contact_mail:first').attr('readonly',true);
    } 
    if($('.is_contact_name').length && customerName)
    {
        $('.is_contact_name:first').val(customerName);
        $('.is_contact_name:first').attr('readonly',true);
    }
    if($('.is_customer_phone_number').length && customerPhone)
    {
        $('.is_customer_phone_number:first').val(customerPhone);
        $('.is_customer_phone_number:first').attr('readonly',true);
    }   
}