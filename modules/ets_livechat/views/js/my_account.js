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
    $('#ticket_note').focus();
    $('.lc-managament-information input[type="file"]').change(function(){
        var fileExtension = ['png','jpg','jpeg','gif'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert(ets_livechat_invalid_file);
        }
        else
        {
            if($(this).closest('.upload_form_custom').find('.file_name').length)
            {
                $(this).closest('.upload_form_custom').find('.file_name').html($(this).val().split('\\').pop());
            }
            else   
                $(this).closest('.upload_form_custom').append('<span class="file_name">'+$(this).val().split('\\').pop()+'</span>'); 
            lc_readURL(this);            
        }   
    });
    $(document).on('click','.lc_form_submit_close',function(){
        $('.lc_form_submit_new_ticket').hide();
    });
    if ($("table .datepicker").length > 0) {
		$("table .datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});
	}
    $(document).keyup(function(e) { 
            if(e.keyCode == 27) {
                if($('.lc_form_submit_new_ticket').length)
                    $('.lc_form_submit_new_ticket').hide();
            }
    });
    $(document).on('click','.lc_rate_ticket .star',function(){
           $.ajax({
                url: '',
                type: 'post',
                dataType: 'json',
                data: {
                    set_rating_ticket: 1,
                    rating:$('input[name="criterion_ticket"]').val(),
                    id_ticket :$('input[name="id_ticket"]').val(),
                },
                success: function(json)
                {       
                    if($('.lc_chatbox').hasClass('end_chat_rate'))
                        $('.lc_chatbox').removeClass('end_chat_rate').addClass('end_chat');
                    $('body').append('<div class="lc_success">'+json.success+'</div>');
                    setTimeout(function(){ $('.lc_success').remove() }, 2000);
                }
            }); 
    });
    $(document).on('change','input[name="ticket_file"],.ets_livechat_form input[type="file"]',function(){
        var filename = $(this).val().split('\\').pop();
        filesize= this.files[0].size/1048576;
        if(ETS_LC_MAX_FILE_MS  &&  filesize>ETS_LC_MAX_FILE_MS)
        {
            $(this).val('');
            alert(invalid_file_max_size);
        }
        else
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
    $(document).on('click','.lc_send_message_ticket',function(){
        if($('#ticket_file').val()=='' && $('#ticket_note').val()=='' && !$('body').hasClass('lc_loading'))
            return false;
        $('.lc_ticket_message').prev('.bootstrap').remove();
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('lc_send_message_ticket',1);
        $('body').addClass('lc_loading');
        $('.module_error').parent().remove();
        $.ajax({
            url: $(this).parents('form').eq(0).attr('action'),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $('#ticket_file').val('');
                $('#ticket_note').val('');
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
                if(json.error)
                {
                    $('.lc_note_message').after(json.error);
                }
                $('body').removeClass('lc_loading');
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
    $(document).on('click','.lc_ticket_captcha_refesh',function(){
        if($('.lc_ticket_captcha_img').length > 0)
            $('.lc_ticket_captcha_img').attr('src',$(this).attr('data-captcha-img')+'&rand='+Math.random());
    });
    $(document).on('click','.submit_new_ticket_bt',function(){
        $('.lc_form_submit_new_ticket').show();
    });
    $(document).mouseup(function (e){
        var container = $('.lc_form_submit_new_ticket_content');
        if (!container.is(e.target)&& container.has(e.target).length === 0)
        {
            $('.lc_form_submit_new_ticket').hide();
        }
    });
    $(document).on('click','#new_ticket_bt',function(){
        if($('#form_ticket').val()!='--')
            window.location.href=$('#form_ticket').val();
         return false;
    });
});
function lc_readURL(input)
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