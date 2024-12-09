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
    displayField();
    lc_displayForm();
    $(document).on('change','.form-group.field_type select',function(){
       lc_displayFormField(); 
    });
    $(document).on('change','input[name="ETS_LC_DISPLAY_SEND_US_AN_EMAIL"],select[name="ETS_LC_LINK_SUPPORT_TYPE"]',function(){
        lc_displayFormExtra();
    });
    $(document).on('change','.lc_field_label,.lc_field_type',function(){
       $(this).closest('.ticket-form-field').find('.field_lable').html($(this).closest('.ticket-form-field').find('.lc_field_label').val()+'-'+$(this).closest('.ticket-form-field').find('.lc_field_type option[value="'+$(this).closest('.ticket-form-field').find('.lc_field_type').val()+'"]').html()); 
    });
    $(document).on('click', '.ets_livechat_callback_url', function () {
        var range, selection;
        if (window.getSelection && document.createRange)
        {
        selection = window.getSelection();
        range = document.createRange();
        range.selectNodeContents($(this)[0]);
        selection.removeAllRanges();
        selection.addRange(range);
        }
        else if (document.selection && document.body.createTextRange)
        {
        range = document.body.createTextRange();
        range.moveToElementText($(this)[0]);
        range.select();
        }
        $('.copied').remove();
        document.execCommand('copy');
        if ($(this).data('msg'))
        {
            $(this).before('<span class="copied">'+$(this).data('msg')+'</span>');
            setTimeout(function(){$('.copied').remove(); }, 2000);
        }
    });
    $(document).on('change','input[type="file"]',function(){
        if($(this).attr('name')=='message_file')
            return true;
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];  
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $(this).val('');
            if($(this).next('.dummyfile').length > 0)
            {
                $(this).next('.dummyfile').eq(0).find('input[type="text"]').val('');
            }
            if($(this).parents('.col-lg-9').eq(0).find('.preview_img').length > 0)
                $(this).parents('.col-lg-9').eq(0).find('.preview_img').eq(0).remove(); 
            if($(this).parents('.col-lg-9').eq(0).next('.uploaded_image_label').length > 0)
            {
                $(this).parents('.col-lg-9').eq(0).next('.uploaded_image_label').removeClass('hidden');
                $(this).parents('.col-lg-9').eq(0).next('.uploaded_image_label').next('.uploaded_img_wrapper').removeClass('hidden');
            }            
            alert(lc_invalid_file);
        }
        else
        {
            lc_readURL(this);            
        }       
    });
    $(document).on('click','.del_preview',function(){
        if($(this).parents('.col-lg-9').eq(0).next('.uploaded_image_label').length > 0)
        {
            $(this).parents('.col-lg-9').eq(0).next('.uploaded_image_label').removeClass('hidden');
            $(this).parents('.col-lg-9').eq(0).next('.uploaded_image_label').next('.uploaded_img_wrapper').removeClass('hidden');
        }
        $(this).parents('.col-lg-9').eq(0).find('.dummyfile input[type="text"]').val('');
        if($(this).parents('.col-lg-9').eq(0).find('input[type="file"]').length > 0)
        {
            $(this).parents('.col-lg-9').eq(0).find('input[type="file"]').eq(0).val('');
        }
        $(this).parents('.preview_img').remove();
    });
    $(document).on('change','#title_'+lc_default_lang,function(){
        if(!$('#id_form').val())
        {
            $('#friendly_url_'+lc_default_lang).val(str2url($(this).val(), 'UTF-8')); 
        }        
        else
        if($('#friendly_url_'+lc_default_lang).val() == '')
            $('#friendly_url_'+lc_default_lang).val(str2url($(this).val(), 'UTF-8')); 
    });
//    $(document).on('click','input[name="ETS_LC_DISPLAY_AVATA"],input[name="ETS_LC_DISPLAY_COMPANY_INFO"]',function(e){
//        if($('input[name="ETS_LC_DISPLAY_AVATA"]:checked').val()==1||$('input[name="ETS_LC_DISPLAY_COMPANY_INFO"]:checked').val()==1)
//        {
//            $('.form-group.image_type').show();
//        }
//        else
//        {
//            $('.form-group.image_type').hide();
//        }
//    });
    $(document).on('click','.field-toggle',function(){
        if(!$(this).hasClass('show_filed'))
        {
            $('.field-toggle').removeClass('show_filed');
            $('.filed-body').hide();
        }
       $(this).toggleClass('show_filed');
       $(this).next('.filed-body').toggle();
    });
    $(document).on('click','.delete-form-field',function(e){
        var id=$(this).data('id');
        e.preventDefault();
        if(!confirm(confirm_delete_field))
            return false;
        if(id)
        {
            var $this=$(this);
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    delete_form_field: 1,
                    id_field : id,
                },
                success: function(json)
                { 
                    $this.closest('.ticket-form-field').remove();
                    var i = 1;
                    $('.ticket-form-field').each(function(){
                         $(this).find('.position_field .position').html(i);
                         $(this).find('.ets_fields_position').val(i);
                         i++;
                    });
                    $('#form_ticket_form .form-wrapper').append('<div class="lc_succesfully">'+json.success+'</div>');
                    setTimeout(function(){ $('#form_ticket_form .form-wrapper .lc_succesfully').remove(); }, 3000);
                }
            });
        }
        else
        {
            
            $(this).closest('.ticket-form-field').remove();
            var i = 1;
            $('.ticket-form-field').each(function(){
                 $(this).find('.position_field .position').html(i);
                 $(this).find('.ets_fields_position').val(i);
                 i++;
            });
            $('#form_ticket_form .form-wrapper').append('<div class="lc_succesfully">'+json.success+'</div>');
            setTimeout(function(){ $('#form_ticket_form .form-wrapper .lc_succesfully').remove(); }, 3000);
        }
    });
    $(document).on('click','.delete_form',function(e){
        var id=$(this).data('id');
        e.preventDefault();
        if(!confirm(confirm_delete_form))
            return false;
        var $this=$(this);
        $('body').addClass('lc_loading');
        $('.lc-list-ticket-form .module_error').remove();
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                delete_form_obj: 1,
                id_form : id,
            },
            success: function(json)
            { 
                if(json.error)
                {
                    $('.lc-list-ticket-form').append(json.error);
                    $('body').removeClass('lc_loading');
                }
                else
                {
                    $('#ticket_form_'+id).remove();
                    lc_displayMessage(json.success);
                    if($('.lc-list-ticket-form .ticket_form').length==1)
                    {
                        $('#ticket_form_0').hide();
                        $('.lc-no-form').show();
                    }
                }
                
            }
        });
    });
    $(document).on('change','#ETS_CLOSE_CHAT_BOX_TYPE,#ETS_LC_MAIL_TO_custom,#ETS_LC_SEND_MAIL_affter_a_centaint_time,input[name="ETS_LC_DISPLAY_COMPANY_INFO"],input[name="ETS_LC_DISPLAY_AVATA"],input[name="ETS_LC_DISPLAY_REQUIRED_FIELDS"],input[name="ETS_LC_SEND_MAIL_WHEN_SEND_MG"],input[name="ETS_LC_AUTO_OPEN"]',function(){
        displayField();
    });
    $(document).on('click','.statust_tab',function(){
        $(this).parent().find('.statust_tab').removeClass('active');
        $(this).addClass('active');
        lc_displayForm();
    });
    $(document).on('click','input[name="allow_captcha"]',function(){
        lc_displayForm();
    });
    if(is_lc_RTL)
        $('#module_form').addClass('form_ltr');
    $(document).on('click','#form_ticket_form_cancel_btn',function(){
        $('.lc_system_ticket').show();
        $('.lc_block_form_new_ticket').hide(); 
    });
    $(document).on('click','.add_new_ticket_form',function(e){
        $('body').addClass('lc_loading');
        e.preventDefault();
        lc_getFormTicketForm(0);
    });
    $(document).on('click','.edit_form',function(e){
        $('body').addClass('lc_loading');
        e.preventDefault();
        lc_getFormTicketForm($(this).data('id'));
        
    });
    $(document).on('click','.add_new_field_in_form',function(e){
        $('body').addClass('lc_loading');
        e.preventDefault();
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                add_new_field_in_form: 1,
                max_position : $('#list-fields .ticket-form-field').length,
            },
            success: function(json)
            { 
                $('body').removeClass('lc_loading');
                $('.field-toggle').removeClass('show_filed');
                $('.filed-body').hide();
                $('#list-fields').append(json.html_form_filed);
                lc_updatePositionField();
                lc_displayFormField();
            }
        });
    });
    $(document).on('click','button[name="saveFormTicket"] ,input[name="saveFormTicket"]',function(e){
        $('body').addClass('lc_loading');
        e.preventDefault();
        $('#form_ticket_form .form-wrapper .bootstrap').remove();
        $('.bootstrap> .alert').remove();
        if ($('#form_ticket_form').find('input.tagify').length > 0)
        {
            $('#form_ticket_form').find('input.tagify').each(function () {
                $(this).val($(this).tagify('serialize'));
            });
        }
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('run_ajax',1);
        formData.append('number_field',$('#list-fields .ticket-form-field').length );
        $.ajax({
            url: $(this).parents('form').eq(0).attr('action'),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                if(json.error)
                {
                    $('#form_ticket_form .panel > .form-wrapper').append(json.error);
                    $('body').removeClass('lc_loading');
                }                
                else
                {
                    if(json.fields_list)
                    {
                        $('#list-fields').html(json.fields_list);
                    }
                    //$('#form_ticket_form .form-wrapper').append('<div class="lc_succesfully">'+json.success+'</div>');
                    //(function(){ $('#form_ticket_form .form-wrapper .lc_succesfully').remove(); }, 3000);
                    if(json.id_form)
                        $('input[name="id_form"]').val(json.id_form);
                    var html ='<div class="col-lg-1 col-xs-1">'+json.id_form+'</div>';
                    html +='<div class="col-lg-2 col-xs-2">'+json.form_value.title+'</div>';
                    html +='<div class="col-lg-3 col-xs-3"><a target="_blank" href="'+json.form_value.link+'">'+json.form_value.link+'</a></div>';
                    html +='<div class="col-lg-3 col-xs-3">'+json.form_value.description+'</div>';
                    html +='<div class="col-lg-1 col-xs-1 sort_order dragHandle"><div class="dragGroup"><span class="position">'+json.form_value.sort_order+'</span></div></div>'
                    html +='<div class="col-lg-1 col-xs-1">';
                        if(json.form_value.active==1)
                        {
                            html +='<a class="lc_form_list_action field-is_featured list-action-enable action-enabled list-item-'+json.id_form+'"  href="#" data-value="1" data-id="'+json.id_form+'" title="'+json.active_title+'">';
                            html +='<i class="icon-check"></i>';
                        }    
                        else
                        {
                            html +='<a class="lc_form_list_action field-enabled list-action-enable action-disabled list-item-'+json.id_form+'"  href="#" data-value="0" data-id="'+json.id_form+'" title="'+json.active_title+'">';
                            html +='<i class="icon-remove"></i>';
                        }  
                        html +='</a>';  
                    html +='</div>';    
                    html +='<div class="col-lg-1 col-xs-1"><span class="lg_edit edit_form" data-id="'+json.id_form+'" title="'+edit_text+'" >'+edit_text+'</span>'+(json.id_form!=1 ? '<span class="lg_delete delete_form" data-id="'+json.id_form+'" title="'+delete_text+'" >'+delete_text+'</span>':'')+'</div>';
                    if($('.lc-list-ticket-form #ticket_form_'+json.id_form).length)
                    {
                        $('.lc-list-ticket-form #ticket_form_'+json.id_form).html(html);
                    }
                    else
                    {
                        $('#lc-list-ticket-form').append('<div id="ticket_form_'+json.id_form+'" class="ticket_form form-group">'+html+'</div>');
                    }
                    $('#ticket_form_0').show();
                    $('.lc-no-form').hide();
                    $('.help-block .link_form_support').html(json.link_form).attr('href',json.link_form);
                    lc_displayMessage(json.success);
                    lc_displayFormField();
                    lc_displayUpdatePositionForm();
                }
            },
            error: function(xhr, status, error)
            {
                var err = eval("(" + xhr.responseText + ")");     
                $.growl.error({ message: err.Message }); 
                $('body').removeClass('lc_loading');              
            }
            
        });
        return false;
    });
    $(document).on('click','button[name="saveConfig"],input[name="saveConfig"]',function(e){
        $('body').addClass('lc_loading');
        e.preventDefault();
        $('#module_form .form-wrapper .bootstrap').remove();
        $('.bootstrap> .alert').remove();
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('run_ajax',1);
        $.ajax({
            url: $(this).parents('form').eq(0).attr('action'),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                if(!json)
                    alert('You may have been logged out. Please login then try again');
                if(json.error)
                {
                    $('#module_form .form-wrapper').append(json.errors);
                    $('body').removeClass('lc_loading');
                }                
                else
                {
                    lc_displayMessage(successful_update);
                }
                
            },
            error: function(xhr, status, error)
            {
                var err = eval("(" + xhr.responseText + ")");     
                $.growl.error({ message: err.Message }); 
                $('body').removeClass('lc_loading');              
            }
        }); 
    });
    $(document).on('keyup','.form-group.change_form input,.form-group.change_form textarea',function(){
        lc_changeFormChatDisplay();
    });
    $(document).on('change','.color,#ETS_LC_AVATAR_IMAGE_TYPE',function(){
       lc_changeFormChatDisplay(); 
    });
    $(document).on('click','input[name="ETS_LC_DISPLAY_COMPANY_INFO"],input[name="ETS_LC_DISPLAY_REQUIRED_FIELDS"],.form-group.captcha input[type="checkbox"],input[name="ETS_LC_ALLOW_CLOSE"],input[name="ETS_LC_ALLOW_MAXIMIZE"],input[name="ETS_LC_ALLOW_MINIMIZE"],input[name="ETS_LC_USE_SOUND_FONTEND"]',function(){
        lc_changeFormChatDisplay();
    });
    $(document).on('change','input[name="ETS_LC_DISPLAY_COMPANY_INFO"]',function(){
        lc_displayCompanyInfo();
    });
    $.ajax({
        url: ETS_LC_MODULE_URL_AJAX,
        type: 'post',
        dataType: 'json',
        data: {
            get_extra_form: 1,
        },
        success: function(json)
        { 
            if($('.ybc-form-group').length)
                $('input[name="ETS_STOP_AUTO_REPLY"]').closest('.ybc-form-group').after(json.formhtml);
            else
                $('input[name="ETS_STOP_AUTO_REPLY"]').closest('.margin-form').after(json.formhtml);
            $('.ybc-blog-tab-'+current_tab_active).addClass('active'); 
            $('.ybc-blog-tab-'+current_tab_active).addClass('active'); 
            $('.config_tab_'+current_tab_active).addClass('active');
            $('body').addClass('ets_lv_tab-'+current_tab_active);
            $('.confi_tab_right .config_tab_livechat').attr('data-tab-id',current_tab_active);
            lc_changeFormChatDisplay();
            lc_displayUpdatePositionForm();
            lc_displayUpdatePositionDepartments();
        }
    });
    $(document).on('click','#submit_clear_message',function(e){
        e.preventDefault();
        var $this= $(this);
        $('.block_sessucfull').hide();
        $('#module_form .form-wrapper .module_error').remove();
        if(confirm(confirm_clear))
        {
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    submit_clear_message: 1,
                    ETS_CLEAR_MESSAGE:$('#ETS_CLEAR_MESSAGE').val(),
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        $('#module_form .form-wrapper').append(json.error);
                    }
                    else{
                        if(json.ids_conversation)
                        {
                            var ids_conversation_deleted=json.ids_conversation.split(',');
                            $.each(ids_conversation_deleted,function(i,id){
                                if($('.chatbox_employe_'+id).length)
                                    $('.chatbox_employe_'+id).remove();
                            });
                        }
                        $('.block_sessucfull').show();
                        $('.block_sessucfull .alert-success').html('<button data-dismiss="alert" class="close" type="button">×</button>'+clear_conversation);
                    }
                    
                }
            });
        }
    });
    $(document).on('click','#submit_clear_attachments',function(e){
        e.preventDefault();
        var $this= $(this);
        $('.block_sessucfull').hide();
        $('#module_form .form-wrapper .module_error').remove();
        if(confirm(confirm_delete_attachment))
        {
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    submit_clear_attachments: 1,
                    ETS_CLEAR_ATTACHMENT : $('#ETS_CLEAR_ATTACHMENT').val(),
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        $('#module_form .form-wrapper').append(json.error);
                    }
                    else
                    {
                        if(!json.attachments_everything)
                        $('#submit_clear_attachments').closest('.form-group').remove();
                        $('.block_sessucfull').show();
                        $('.block_sessucfull .alert-success').html('<button data-dismiss="alert" class="close" type="button">×</button>'+delete_attachments);
                        $('#ETS_CLEAR_ATTACHMENT option[value="everything"]').html(everything_text+' ('+json.attachments_everything+' '+(json.attachments_everything >1? files_text:file_text)+ (json.attachments_everything_size <1024 ? json.attachments_everything_size+' MB' : json.attachments_everything_size/1024+' GB' )+')');
                        $('#ETS_CLEAR_ATTACHMENT option[value="1_week"]').html(week_1_text+' ('+json.attachments_1_week+' '+(json.attachments_1_week >1? files_text:file_text)+(json.attachments_1_week_size <1024 ? json.attachments_1_week_size+' MB' : json.attachments_1_week_size/1024+' GB' )+ ')');
                        $('#ETS_CLEAR_ATTACHMENT option[value="1_month_ago"]').html(month_1_text+' ('+json.attachments_1_month_ago+' '+(json.attachments_1_month_ago >1? files_text:file_text)+(json.attachments_1_month_ago_size <1024 ? json.attachments_1_month_ago_size+' MB' : json.attachments_1_month_ago_size/1024+' GB' )+')');
                        $('#ETS_CLEAR_ATTACHMENT option[value="6_month_ago"]').html(month_6_text+' ('+json.attachments_6_month_ago+' '+(json.attachments_6_month_ago >1? files_text:file_text)+(json.attachments_6_month_ago_size <1024 ? json.attachments_6_month_ago_size+' MB' : json.attachments_6_month_ago_size/1024+' GB' )+')');
                        $('#ETS_CLEAR_ATTACHMENT option[value="1_year_ago"]').html(year_1_text+' ('+json.attachments_year_ago+' '+(json.attachments_year_ago >1? files_text:file_text)+(json.attachments_year_ago_size <1024 ? json.attachments_year_ago_size+' MB' : json.attachments_year_ago_size/1024+' GB' )+')');
                    }
                    
                }
            });
        }
    });
    $(document).on('click','#submit_auto_reply',function(e){
        e.preventDefault();
        $('#module_form .form-wrapper .module_error').remove();
        $('body').addClass('lc_loading');
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                submit_auto_reply: 1,
                message_order : $('#message_order').val(),
                auto_content :$('#auto_content').val(),
                id_auto_msg : $('#id_auto_msg').val()?$('#id_auto_msg').val():0,
            },
            success: function(json)
            { 
                if(json.error)
                {
                    $('#module_form .form-wrapper').append(json.error);
                    $('body').removeClass('lc_loading');
                }
                else
                {
                    var html ='<div class="col-lg-2">'+json.message_order+'</div>';
                        html +='<div class="col-lg-8">'+json.auto_content+'</div>';
                        html +='<div class="col-lg-2">';
                            html +='<span class="lg_edit edit_auto_reply" data-id="'+json.id_auto_msg+'" title="'+edit_text+'" >'+edit_text+'</span>';
                            html +='<span class="lg_delete delete_auto_reply" data-id="'+json.id_auto_msg+'" title="'+delete_text+'" >'+delete_text+'</span>';
                        html +='</div>';
                    if($('#auto_reply_'+json.id_auto_msg).length)
                    {
                        
                        $('#auto_reply_'+json.id_auto_msg).html(html);
                    }
                    else
                    {
                        $('#auto_reply_list').append('<div id="auto_reply_'+json.id_auto_msg+'" class="auto_reply">'+html+'</div>');
                    }
                    $('#auto_reply_0').show();
                    $('.lc_no_auto_message').hide();
                    $('#block_form_auto_reply').hide();
                    $('#auto_reply_list').show();
                    lc_displayMessage(json.success);
                    lc_displayFormExtra();
                }
            }
        });
    });
    $(document).on('click','.edit_staff',function(){
        var id_employee= $(this).attr('data-id');
        var $this=$(this); 
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                get_staff: 1,
                id_employee:id_employee,
            },
            success: function(json)
            { 
                $('#form_staff').show();
                $('.block_form_staffs_list_header .tab_title_c').html(edit_staff_text);
                $('#form_staff').html(json.html);
                $('#lc_staff_list').hide();
                lc_displayFormExtra();
            }
        });
    });
    $(document).on('click','.edit_auto_reply',function(e){
        var id_auto_msg = $(this).attr('data-id');
        var $this= $(this);
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                get_auto_reply_info: 1,
                id_auto_msg:id_auto_msg,
            },
            success: function(json)
            { 
                $('#block_form_auto_reply').show(); 
                $('#block_form_auto_reply .tab_title_c').html(edit_auto_message_text);
                $('#auto_reply_list').hide();
                $('#block_errors_auto_reply').hide();
                $('#auto_content').val(json.auto_content);
                $('#id_auto_msg').val(json.id_auto_msg);
                $('#message_order').val(json.message_order);
                lc_displayFormExtra();
            }
        });
    });
    $(document).on('click','.delete_auto_reply',function(e){
        if(confirm(confirm_delete_message))
        {
           var id_auto_msg= $(this).attr('data-id');
           var $this= $(this);
           $('#module_form .form-wrapper .module_error').remove();
           $('body').addClass('lc_loading');
           $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    delete_auto_reply: 1,
                    id_auto_msg:id_auto_msg,
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        $('#module_form .form-wrapper').append(json.error);
                        $('body').removeClass('lc_loading');
                    }
                    else
                    {
                        $this.closest('.auto_reply').remove();
                        lc_displayMessage(json.success);
                        if($('#auto_reply_list .auto_reply').length==1)
                        {
                            $('#auto_reply_0').hide();
                            $('.lc_no_auto_message').show();
                        }
                    }
                    
                }
            }); 
        }   
    });
    $(document).on('click','.add_auto_message',function(){
       $('#block_form_auto_reply').show(); 
       $('#block_form_auto_reply .tab_title_c').html(add_auto_message_text);
       $('#auto_reply_list').hide();
       $('.form-message-order').show();
       $('#block_errors_auto_reply').hide();
       $('#auto_content').val('');
       $('#id_auto_msg').val('');
       $('#message_order').val('');
       lc_displayFormExtra();
    });
    $(document).on('click','#cancel_auto_reply', function(e){
       e.preventDefault();
       $('#block_form_auto_reply').hide(); 
       $('#auto_reply_list').show(); 
       lc_displayFormExtra();
    });
    $(document).on('click','.add_pre_made_message',function(){
        $('#pre_made_message_list').hide();
        $('#block_form_pre_made_message').show();
        $('#block_form_pre_made_message .tab_title_c').html(add_pre_made_message_text);
        $('.block_errors_pre_made_message').hide();
        $('#title_message').val('');
        $('#message_content').val('');
        $('#id_pre_made_message').val('');
        $('#short_code_message').val('');
        lc_displayFormExtra();
    });
    $(document).on('click','.add_departments',function(){
        $('#departments_list').hide();
        $('#block_form_departments').show();
        $('#block_form_departments .tab_title_c').html(add_department_text);
        $('.block_errors_departments').hide();
        $('#departments_status_on').click();
        $('#departments_name').val('');
        $('input[name="departments_agents[]"],input[name="departments_name_all"]').each(function(){
           $(this).removeAttr('checked');
        });
        $('#departments_description').val('');
        $('#id_departments').val('');
        lc_displayFormExtra();
    });
    $(document).on('click','#departments_name_all',function(e){
        if($('input[name="departments_name_all"]:checked').length)
        {
            $('input[name="departments_agents[]"]').each(function(){
               $(this).attr('checked','checked');
               $(this).attr('disabled','disabled');
            });
        }
        else
        {
            $('input[name="departments_agents[]"]').each(function(){
               $(this).removeAttr('disabled');
            });
        }
        
    });
    $(document).on('click','#departments_all',function(e){
        if($('#departments_all:checked').length)
        {
            $('input[name="departments[]"]').each(function(){
               $(this).attr('checked','checked');
               if($(this).attr('id')!='departments_all')
                    $(this).attr('disabled','disabled');
            });
        }
        else
        {
            $('input[name="departments[]"]').each(function(){
               $(this).removeAttr('disabled');
            });
        }
    });
    $(document).on('click','#cancel_staff',function(e){
        $('#form_staff').hide();
        $('#lc_staff_list').show();
        $('.block_form_staffs_list_header .tab_title_c').html(list_staff_text);
        lc_displayFormExtra();
        return false;
    });
    $(document).on('click','#submit_save_staff',function(e){
        e.preventDefault();
        $('body').addClass('lc_loading');
        $('.block_form_staffs_list_header .tab_title_c').html(list_staff_text);
        $('#module_form .form-wrapper .module_error').remove();
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('submit_save_staff',1);
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function(json)
            { 
                if(json.error)
                {
                    $('#module_form .form-wrapper').append(json.error);
                    $('body').removeClass('lc_loading');
                }                
                else
                {
                    lc_displayMessage(json.success);
                    if(json.image)
                    {
                        $('#lc_staff_list #staff_'+json.id_employee+' .avata_staff').html('<img src="'+json.image+'" >');
                    } 
                    $('#lc_staff_list #staff_'+json.id_employee+' .nick_name').html(json.nick_name);
                    $('#lc_staff_list #staff_'+json.id_employee+' .signature').html(json.signature);
                    if(json.status==1)
                        $('.lc_staff_list_action.list-item-'+json.id_employee).removeClass('action-disabled').addClass('action-enabled').html('<i class="icon-check"></i>');
                    else
                        $('.lc_staff_list_action.list-item-'+json.id_employee).removeClass('action-enabled').addClass('action-disabled').html('<i class="icon-remove"></i>');
                    $('#form_staff').hide();
                    $('#lc_staff_list').show(); 
                    lc_displayFormExtra();    
                }
            },
            error: function(xhr, status, error)
            {

                $('body').removeClass('lc_loading');              
            }
        });
    });
    $(document).on('click','#submit_departments',function(e){
        e.preventDefault();
        $('#module_form .form-wrapper .module_error').remove();
        var departments_agents='';
        if($('input[name="departments_agents[]"]:checked').length)
        {
            $('input[name="departments_agents[]"]:checked').each(function(){
               departments_agents +=$(this).val()+','; 
            });
        }
        $('body').addClass('lc_loading');
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                submit_departments: 1,
                departments_status : $('input[name="departments_status"]:checked').val(),
                departments_name :$('#departments_name').val(),
                departments_description : $('#departments_description').val(),
                departments_agents : departments_agents,
                id_departments: $('#id_departments').val() ? $('#id_departments').val() : 0,
                departments_name_all : $('input[name="departments_name_all"]:checked').length ? 1 :0,
            },
            success: function(json)
            { 
                if(json.error)
                {
                    $('#module_form .form-wrapper').append(json.error);
                    $('body').removeClass('lc_loading');
                }
                else
                {
                    if($('#departments_'+json.id_departments).length)
                      $('#departments_'+json.id_departments).html(json.department);
                    else
                    {
                        $('#department-list-sort').append('<div id="departments_'+json.id_departments+'" class="form-group">'+json.department+'</div>');
                    } 
                    $('#departments_0').show();
                    $('.lc_no_department').hide(); 
                    $('#block_form_departments').hide();
                    $('#departments_list').show();
                    if(json.success)
                        lc_displayMessage(json.success);
                    else 
                        $('body').removeClass('lc_loading');
                    lc_displayFormExtra();
                    lc_displayUpdatePositionDepartments();
                }
                
            }
        });
    });
    $(document).on('click','#submit_pre_made_message',function(e){
        e.preventDefault();
        $('#module_form .form-wrapper .module_error').remove();
        $('body').addClass('lc_loading');
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                submit_pre_made_message: 1,
                title_message : $('#title_message').val(),
                message_content :$('#message_content').val(),
                short_code_message : $('#short_code_message').val(),
                id_pre_made_message : $('#id_pre_made_message').val()?$('#id_pre_made_message').val():0,
            },
            success: function(json)
            { 
                if(json.error)
                {
                    $('#module_form .form-wrapper').append(json.error);
                    $('body').removeClass('lc_loading');
                }
                else
                {
                    var html ='<div class="col-lg-3">'+json.pre_made_message.short_code+'</div>';
                        html +='<div class="col-lg-7">'+json.pre_made_message.message_content+'</div>';
                        html +='<div class="col-lg-2">';
                            html +='<span class="lg_edit edit_pre_made_message" data-id="'+json.pre_made_message.id_pre_made_message+'" title="'+edit_text+'" >'+edit_text+'</span>';
                            html +='<span class="lg_delete delete_pre_made_message" data-id="'+json.pre_made_message.id_pre_made_message+'" title="'+delete_text+'" >'+delete_text+'</span>';
                        html +='</div>';
                    if($('#pre_made_message_'+json.pre_made_message.id_pre_made_message).length)
                    {
                        
                        $('#pre_made_message_'+json.pre_made_message.id_pre_made_message).html(html);
                    }
                    else
                    {
                        $('#pre_made_message_list').append('<div id="pre_made_message_'+json.pre_made_message.id_pre_made_message+'" class="pre_made_message">'+html+'</div>');
                    }
                    $('#pre_made_message_0').show();
                    $('.lc_no_pre_made').hide();
                    $('#block_form_pre_made_message').hide();
                    $('#pre_made_message_list').show();
                    if(json.success)
                        lc_displayMessage(json.success);
                    else
                        $('body').removeClass('lc_loading');
                    lc_displayFormExtra();
                }
            }
        });
    });
    $(document).on('click','#cancel_pre_made_message',function(e){
        e.preventDefault();
        $('#block_form_pre_made_message').hide(); 
        $('#pre_made_message_list').show();       
        lc_displayFormExtra();
    });
    $(document).on('click','#cancel_departments',function(e){
        e.preventDefault();
        $('#block_form_departments').hide(); 
        $('#departments_list').show(); 
        lc_displayFormExtra();      
    });
    $(document).on('click','.edit_pre_made_message',function(e){
        var id_pre_made_message =$(this).attr('data-id');
        var $this= $(this);
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                get_pre_made_message: 1,
                id_pre_made_message:id_pre_made_message,
            },
            success: function(json)
            { 
                $('#block_form_pre_made_message').show(); 
                $('#block_form_pre_made_message .tab_title_c').html(edit_pre_made_message_text);
                $('#pre_made_message_list').hide();
                $('.block_errors_pre_made_message').hide();
                $('#title_message').val(json.title_message);
                $('#message_content').val(json.message_content);
                $('#id_pre_made_message').val(json.id_pre_made_message);
                $('#short_code_message').val(json.short_code);
                lc_displayFormExtra();
            }
        }); 
    });
    $(document).on('click','.edit_deparments',function(e){
        var id_departments =$(this).attr('data-id');
        var $this= $(this);
        $.ajax({
            url: ETS_LC_MODULE_URL_AJAX,
            type: 'post',
            dataType: 'json',
            data: {
                get_departments: 1,
                id_departments:id_departments,
            },
            success: function(json)
            { 
                $('#block_form_departments').show(); 
                $('#departments_list').hide();
                $('.block_errors_departments').hide();
                $('#block_form_departments').html(json.departments_from);
                lc_displayFormExtra();
            }
        }); 
    });
    $(document).on('click','.delete_pre_made_message',function(){
        if(confirm(confirm_delete_message))
        {
            var id_pre_made_message =$(this).attr('data-id');
            var $this= $(this);
            $('body').addClass('lc_loading');
            $('#module_form .form-wrapper .module_error').remove();
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    delete_pre_made_message: 1,
                    id_pre_made_message:id_pre_made_message,
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        $('#module_form .form-wrapper').append(json.error);
                        $('body').removeClass('lc_loading');
                    }
                    else
                    {
                        $this.closest('.pre_made_message').remove();
                        lc_displayMessage(json.success);
                        if($('#pre_made_message_list .pre_made_message').length==1)
                        {
                            $('#pre_made_message_0').hide();
                            $('.lc_no_pre_made').show();
                        }
                    }
                    
                }
            });
        }
        
    });
    $(document).on('click','.delete_avata_staff',function(){
        if(confirm(confirm_delete_avata_staff))
        {
            $('body').addClass('lc_loading');
            var id_staff =$(this).attr('data-id');
            var $this= $(this);
            $('#module_form .form-wrapper .module_error').remove();
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    delete_avata_staff: 1,
                    id_staff:id_staff,
                },
                success: function(json)
                { 
                    
                    if(json.error)
                    {
                        $('#module_form .form-wrapper').append(json.error);
                        $('body').removeClass('lc_loading');
                    }
                    else
                    {
                        $('.uploaded_image_label').remove();
                        $('.uploaded_img_wrapper').remove();
                        if(json.image)
                            $('#lc_staff_list #staff_'+id_staff+' .avata_staff img').attr('src',json.image);
                        else
                            $('#lc_staff_list #staff_'+id_staff+' .avata_staff').html('');
                        lc_displayMessage(json.success);
                    }
                    
                }
            });
        }
    });
    $(document).on('click','.delete_departments',function(){
        if(confirm(confirm_delete_deparment))
        {
            $('body').addClass('lc_loading');
            var id_departments =$(this).attr('data-id');
            var $this= $(this);
            $('#module_form .form-wrapper .module_error').remove();
            $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    delete_departments: 1,
                    id_departments:id_departments,
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        $('#module_form .form-wrapper').append(json.error);
                        $('body').removeClass('lc_loading');
                    }
                    else
                    {
                        $('#departments_'+id_departments).remove();
                            var i = 1;
                        if($('#department-list-sort .sort_order').length)
                        {
                            $('#department-list-sort .sort_order').each(function(){
            			         $(this).find('span').html(i);
                                 i++;
        			         });
                        }
    			         
                        lc_displayMessage(json.success);
                        if($('#departments_list .form-group').length==1)
                        {
                            $('#departments_0').hide();
                            $('.lc_no_department').show();
                        }
                    }
                    
                }
            });
        }
    });
    $(document).on('click','.lc_form_list_action',function(){
        var $this=$(this);
        $('body').addClass('lc_loading');
        $('.lc-list-ticket-form .module_error').remove();
        $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    change_status_form: 1,
                    id_form:$this.data('id'),
                    active:$this.attr('data-value')==1 ? 0 :1,
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        $('.lc-list-ticket-form').append(json.error);
                        $('body').removeClass('lc_loading');
                    }
                    else
                    {
                        if(json.active==1)
                            $('.lc_form_list_action.list-item-'+json.id_form).removeClass('action-disabled').addClass('action-enabled').html('<i class="icon-check"></i>');
                        else
                            $('.lc_form_list_action.list-item-'+json.id_form).removeClass('action-enabled').addClass('action-disabled').html('<i class="icon-remove"></i>');
                        $('.lc_form_list_action.list-item-'+json.id_form).attr('title',json.title);
                        $('.lc_form_list_action.list-item-'+json.id_form).attr('data-value',json.active);
                        lc_displayMessage(json.success);
                    }
                    
                },
                error: function(xhr, status, error)
                {
                    $('body').removeClass('lc_loading');              
                }
            });
        return false;
    });
    $(document).on('click','.lc_department_list_action',function(){
        var $this=$(this);
        $('body').addClass('lc_loading');
        $('#module_form .form-wrapper .module_error').remove();
        $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    change_status_departments: 1,
                    id_departments:$this.data('id'),
                    status:$this.attr('data-value')==1 ? 0 :1,
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        $('#module_form .form-wrapper').append(json.error);
                        $('body').removeClass('lc_loading');
                    }
                    else
                    {
                        if(json.status==1)
                            $('.lc_department_list_action.list-item-'+json.id_departments).removeClass('action-disabled').addClass('action-enabled').html('<i class="icon-check"></i>');
                        else
                            $('.lc_department_list_action.list-item-'+json.id_departments).removeClass('action-enabled').addClass('action-disabled').html('<i class="icon-remove"></i>');
                        $('.lc_department_list_action.list-item-'+json.id_departments).attr('title',json.title);
                        $('.lc_department_list_action.list-item-'+json.id_departments).attr('data-value',json.status);
                        lc_displayMessage(json.success);
                    }
                },
                error: function(xhr, status, error)
                {
                    $('body').removeClass('lc_loading');              
                }
            });
        return false;
    });
    $(document).on('click','.lc_staff_list_action',function(){
        var $this=$(this);
        $('body').addClass('lc_loading');
        $('#module_form .form-wrapper .module_error').remove();
        $.ajax({
                url: ETS_LC_MODULE_URL_AJAX,
                type: 'post',
                dataType: 'json',
                data: {
                    change_status_staff: 1,
                    id_employee:$this.data('id'),
                    status:$this.attr('data-value')==1 ? 0 :1,
                },
                success: function(json)
                { 
                    if(json.error)
                    {
                        $('#module_form .form-wrapper').append(json.error);
                        $('body').removeClass('lc_loading');
                    }
                    else
                    {
                       if(json.status==1)
                            $('.lc_staff_list_action.list-item-'+json.id_employee).removeClass('action-disabled').addClass('action-enabled').html('<i class="icon-check"></i>');
                        else
                            $('.lc_staff_list_action.list-item-'+json.id_employee).removeClass('action-enabled').addClass('action-disabled').html('<i class="icon-remove"></i>');
                        $('.lc_staff_list_action.list-item-'+json.id_employee).attr('title',json.title);
                        $('.lc_staff_list_action.list-item-'+json.id_employee).attr('data-value',json.status);
                        lc_displayMessage(json.success); 
                    }
                    
                },
                error: function(xhr, status, error)
                {
                    $('body').removeClass('lc_loading');              
                }
            });
        return false;
    });
    if(current_tab_active=='statistics')
    {
        $('button[name="saveConfig"]').hide();
    }
    else
        $('button[name="saveConfig"]').show();
    $('.confi_tab').click(function(){
        $('.confi_tab').each(function(){
            var current_tabactive = $(this).attr("data-tab-id");
            $('body').removeClass('ets_lv_tab-'+current_tabactive);
        });
        var current_tabactive = $('.confi_tab').attr("data-tab-id");
        $('body').removeClass('ets_lv_tab-'+current_tabactive);
        $('.ybc-form-group').removeClass('active');
        $('.ybc-blog-tab-'+$(this).attr('data-tab-id')).addClass('active');  
        $('body').addClass('ets_lv_tab-'+$(this).attr("data-tab-id") );
        $('#ETS_TAB_CURENT_ACTIVE').val($(this).attr('data-tab-id'));
        $(this).parent().find('.confi_tab').removeClass('active');
        $(this).addClass('active');    
        if($(this).attr('data-tab-id')=='statistics')
        {
            $('button[name="saveConfig"]').hide();
        }
        else
            $('button[name="saveConfig"]').show();
        lc_displayFormExtra();    
    });
    $(document).on('click','.confi_tab_right .confi_tab',function(){
        $('.confi_tab_right .confi_tab').removeClass('active');
        $('.confi_tab_right .confi_tab[data-tab-id="'+$(this).attr('data-tab-id')+'"]').addClass('active');
        if($(this).hasClass('config_tab_ticket_system'))
        {
            $('form#module_form').hide();
            $('.lc_ticket_system_form').show();
        }
        else
        {
            $('form#module_form').show();
            $('.lc_ticket_system_form').hide();
            if($(this).hasClass('config_tab_livechat'))
            {
                $('.confi_tab_left').removeClass('lc_hide');
                $('#module_form .panel > .form-wrapper').removeClass('full');
            }
            else
            {
                $('.confi_tab_left').addClass('lc_hide');
                $('#module_form .panel > .form-wrapper').addClass('full');
            }
        }
        if($(this).attr('data-tab-id')=='departments')
        {
            $('#module_form .panel-heading').html('<i class="icon-AdminAdmin"></i>'+department_config_text);
        }else if($(this).attr('data-tab-id')=='staffs')
           $('#module_form .panel-heading').html('<i class="icon-AdminAdmin"></i>'+staff_config_text); 
        else
            $('#module_form .panel-heading').html('<i class="icon-AdminAdmin"></i>'+livechat_config_text);
        lc_displayFormExtra();    
    });
    $(document).on('click','.confi_tab_left .confi_tab',function(){
        $('.confi_tab_right .config_tab_livechat').attr('data-tab-id',$(this).attr('data-tab-id')); 
        lc_displayFormExtra();      
    });
    if($('select[name="ETS_LC_MISC[]"] option[value="all"]').is(':selected'))
        $('select[name="ETS_LC_MISC[]"] option').prop('selected',true);
    $('select[name="ETS_LC_MISC[]"] option').click(function(){
        if($(this).val()=='all' && !$('select[name="ETS_LC_MISC[]"][value="all"]').is(':selected'))
            $('select[name="ETS_LC_MISC[]"] option').prop('selected',true);
    });  
    if($('select[name="ETS_LC_CUSTOMER_GROUP[]"] option[value="all"]').is(':selected'))
        $('select[name="ETS_LC_CUSTOMER_GROUP[]"] option').prop('selected',true);
    $('select[name="ETS_LC_CUSTOMER_GROUP[]"] option').click(function(){
        if($(this).val()=='all' && !$('select[name="ETS_LC_CUSTOMER_GROUP[]"][value="all"]').is(':selected'))
            $('select[name="ETS_LC_CUSTOMER_GROUP[]"] option').prop('selected',true);
    }); 
    $(document).on('click','.st_form_tab > li',function(){
        if(!$(this).hasClass('active'))
        {
            $('.st_form > div, .st_form_tab > li').removeClass('active');
            $(this).addClass('active');
            $('.st_form > div.st_form_'+$(this).attr('data-tab')).addClass('active');
        }        
    });
    $('#ETS_SOUND_WHEN_NEW_MESSAGE').change(function(){
        document.getElementById("lg_ets_"+$(this).val()).play();
    });
    $(document).on('click','#mail_new_ticket_custom_emails,input[name="require_select_department"]',function(){
        lc_displayFromFromTicket();
    });
});
function lc_displayForm()
{
    $('.form-group.status').hide();
    $('.form-group.ticket').hide();
    $('.form-group.status.'+$('.chatbox_tab.active').attr('data-tab')).show();
    $('.form-group.ticket.'+$('.ticket_tab.active').attr('data-tab')).show();
    if($('.ticket_tab.active').attr('data-tab')=='general')
    {
        if($('input[name="allow_captcha"]:checked').val()==1)
            $('.customer_no_captcha.form-group').show();
        else
            $('.customer_no_captcha.form-group').hide();
    }
    lc_displayFromFromTicket();
}
function displayField()
{
    //if($('input[name="ETS_LC_DISPLAY_COMPANY_INFO"]:checked').val()=='general')
//        $('.company_info.form-group').show();
//    else
//        $('.company_info.form-group').hide();
    if($('input[name="ETS_LC_DISPLAY_AVATA"]:checked').val()==1)
        $('.customer_avata.form-group').show();
    else
        $('.customer_avata.form-group').hide();
    
    if($('input[name="ETS_LC_DISPLAY_REQUIRED_FIELDS"]:checked').val()==1)
        $('.display_required_fields.form-group').show();
    else
        $('.display_required_fields.form-group').hide();
    
    if($('input[name="ETS_LC_SEND_MAIL_WHEN_SEND_MG"]:checked').val()==1)
    {
        $('.lc_send_mail.form-group').show();
        if($('#ETS_LC_MAIL_TO_custom').is(':checked'))
            $('.customer_emails.form-group').show();
        else
            $('.customer_emails.form-group').hide();
        if($('#ETS_LC_SEND_MAIL_affter_a_centaint_time').is(':checked'))
            $('.time_send_email.form-group').show();
        else
            $('.time_send_email.form-group').hide();
    }    
    else
        $('.lc_send_mail.form-group').hide();
    if($('#ETS_CLOSE_CHAT_BOX_TYPE').val()=='image')
    {
        $('.form-group.lc_bubble_image').show();
    }
    else
        $('.form-group.lc_bubble_image').hide();    
    if($('input[name="ETS_LC_AUTO_OPEN"]:checked').val()==1)
        $('.lc_auto_open.form-group').show();
    else
        $('.lc_auto_open.form-group').hide();
    
}
function updatePositionPreMadeMessage($mySort)
{
    if($mySort.length <=0)
        return false;
	$mySort.sortable({
		opacity: 0.6,
		cursor: "move",
		update: function() {
			var order = $(this).sortable("serialize") + "&action=updatePreMadeMessageOrdering";						
            $.ajax({
                url: '',
                type: 'post',
                dataType: 'json',
                data: order,
                success: function(json)
                { 
                    $('#module_form .form-wrapper').append('<div class="lc_succesfully">update succesfully</div>');
                    setTimeout(function(){ $('#module_form .form-wrapper .lc_succesfully').remove(); }, 3000);
                }
            });
		}
	});
	$mySort.hover(function() {
		$(this).css("cursor","move");
		},
		function() {
		$(this).css("cursor","auto");
	});
}
function lc_updatePositionField()
{
    if($('#list-fields').length<=0)
        return false;
    var $mySort= $('#list-fields');
	$mySort.sortable({
    		opacity: 0.6,
            handle: ".position_field",
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updatePositionField";						
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: ETS_LC_MODULE_URL_AJAX,
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:order,
        			success: function(jsonData)
        			{
        			     var i = 1;
        			     $('.ticket-form-field').each(function(){
        			         $(this).find('.position_field .position').html(i);
                             $(this).find('.ets_fields_position').val(i);
                             i++;
        			     });
                         $('#form_ticket_form .form-wrapper').append('<div class="lc_succesfully">'+jsonData.success+'</div>');
                         setTimeout(function(){ $('#form_ticket_form .form-wrapper .lc_succesfully').remove(); }, 3000);
                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
	});
}
function  lc_displayUpdatePositionForm()
{
    if($('#lc-list-ticket-form').length<=0)
        return false;
    var $mySort= $('#lc-list-ticket-form');
    
	$mySort.sortable({
    		opacity: 0.6,
            handle: ".sort_order",
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updatePositionForm";	
                $('.lc-list-ticket-form .module_error').remove();					
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: ETS_LC_MODULE_URL_AJAX,
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:order,
        			success: function(jsonData)
        			{
            			 if(jsonData.error)
                         {
                            $('.lc-list-ticket-form').append(jsonData.error);
                            $mySort.sortable("cancel");
                         }
                         else
                         {
                             var i = 0;
            			     $('.ticket_form').each(function(){
            			         $(this).find('.sort_order span').html(i);
                                 i++;
            			     });
                             $('body').addClass('lc_loading');
                             lc_displayMessage(jsonData.success);
                         }
        			    
                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
	});
}
function hideOtherLanguageETS(id)
{
	$('.translatable-field').hide();
	$('.lang-' + id).show();

	var id_old_language = id_language;
	id_language = id;

	if (id_old_language != id)
		changeEmployeeLanguage();
    $('#id_language_change').val(id);
	updateCurrentText();
}
function lc_changeFormChatDisplay()
{
    var id_lang_current = $('#id_language_change').val();
    if($('.statust_tab.active').length<=0)
        return false;
    var form_tab = $('.statust_tab.active').attr('data-from');
    if(form_tab=='do_not_disturb')
      var  form_tab2 ='busy';
    else
      var  form_tab2=form_tab;
    if($('.lang-'+id_lang_current+' .lc_admin_'+form_tab+' .lc_'+form_tab+'_heading').html()!=$('#ETS_LC_TEXT_HEADING_'+form_tab2.toUpperCase()+'_'+id_lang_current).val())
    {
        $('.lang-'+id_lang_current+' .lc_admin_'+form_tab+' .lc_'+form_tab+'_heading').html($('#ETS_LC_TEXT_HEADING_'+form_tab2.toUpperCase()+'_'+id_lang_current).val());
    }
    $('.lc_admin_'+form_tab+' .lc_heading_'+form_tab).css('background-color',$('input[name="ETS_LC_HEADING_COLOR_'+form_tab2.toUpperCase()+'"]').val());
    $('.lc_list_customer_chat .lc_heading_'+form_tab).css('background-color',$('input[name="ETS_LC_HEADING_COLOR_'+form_tab2.toUpperCase()+'"]').val());
    $('.lc_list_customer_chat .lc_heading_'+form_tab+' .toogle-hide-left').css('background-color',$('input[name="ETS_LC_HEADING_COLOR_'+form_tab2.toUpperCase()+'"]').val());
    if($('.lang-'+id_lang_current+' .lc_admin_'+form_tab+' .lc_introduction .lc_'+form_tab+'_text').html()!=$('#ETS_LC_TEXT_'+form_tab.toUpperCase()+'_'+id_lang_current).val())
    {
        $('.lang-'+id_lang_current+' .lc_admin_'+form_tab+' .lc_introduction .lc_'+form_tab+'_text').html($('#ETS_LC_TEXT_'+form_tab.toUpperCase()+'_'+id_lang_current).val());
    }
//    if($('input[name="ETS_LC_DISPLAY_COMPANY_INFO"]:checked').val()==1)
//    {
//        if(!$('.chatbox_demo_backend .lc_company_info').hasClass('display_company_info'))
//            $('.chatbox_demo_backend .lc_company_info').addClass('display_company_info');
//    }
//    else
//    {
//        if($('.chatbox_demo_backend .lc_company_info').hasClass('display_company_info'))
//            $('.chatbox_demo_backend .lc_company_info').removeClass('display_company_info');
//    }
    if($('.chatbox_demo_backend .lc_admin_'+form_tab+' .company.company-name').html()!=$('#ETS_LC_COMPANY_NAME').val())
    {
        $('.chatbox_demo_backend .company.company-name').each(function(){
            $(this).html($('#ETS_LC_COMPANY_NAME').val());
        });
        $('.company-name .name').html($('#ETS_LC_COMPANY_NAME').val());
    } 
    if($('.lang-'+id_lang_current+' .lc_admin_'+form_tab+' .sub_title').html()!=$('#ETS_LC_SUB_TITLE_'+id_lang_current).val())
    {
        $('.lang-'+id_lang_current+' .sub_title').each(function(){
            $(this).html($('#ETS_LC_SUB_TITLE_'+id_lang_current).val());
        });
    }
    if($('input[name="ETS_LC_DISPLAY_REQUIRED_FIELDS"]:checked').val()!=1)
    {
        $('.chatbox_demo_backend .lc_customer_info').removeClass('closed');
    }
    else
    {
        $('.chatbox_demo_backend .lc_customer_info').addClass('closed');
    }
    if($('#ETS_LC_CAPTCHA_first').is(':checked')||$('#ETS_LC_CAPTCHA_notlog').is(':checked')||$('#ETS_LC_CAPTCHA_always').is(':checked'))
    {
        $('.chatbox_demo_backend .lc_captcha').addClass('active');
        $('.chatbox_demo_backend .lc_captcha').parent().addClass('show_capcha');
    }
    else
    {
        $('.chatbox_demo_backend .lc_captcha').removeClass('active');
        $('.chatbox_demo_backend .lc_captcha').parent().removeClass('show_capcha');
    }
    if($('.lang-'+id_lang_current+' .lc_admin_online .lc_send_start').val()!=$('#ETS_LC_TEXT_SEND_START_CHAT_'+id_lang_current).val())
    {
        $('.lang-'+id_lang_current+' .lc_send_start').val($('#ETS_LC_TEXT_SEND_START_CHAT_'+id_lang_current).val());
    }
    if($('.lang-'+id_lang_current+' .lc_admin_offline .lc_send_offline').val()!=$('#ETS_LC_TEXT_SEND_OffLINE_'+id_lang_current).val())
    {
        $('.lang-'+id_lang_current+' .lc_admin_offline .lc_send_offline').val($('#ETS_LC_TEXT_SEND_OffLINE_'+id_lang_current).val());
    }
    if($('input[name="ETS_LC_ALLOW_CLOSE"]:checked').val()==1)
    {
        $('.chatbox_demo_backend .lc_close').removeClass('lc_hide');
    }
    else
    {
        $('.chatbox_demo_backend .lc_close').addClass('lc_hide');
    }
    if($('input[name="ETS_LC_ALLOW_MAXIMIZE"]:checked').val()==1)
    {
        $('.chatbox_demo_backend .lc_maximize').removeClass('lc_hide');
    }
    else
    {
        $('.chatbox_demo_backend .lc_maximize').addClass('lc_hide');
    }
    if($('input[name="ETS_LC_ALLOW_MINIMIZE"]:checked').val()==1)
    {
        $('.chatbox_demo_backend .lc_minimize').removeClass('lc_hide');
    }
    else
    {
        $('.chatbox_demo_backend .lc_minimize').addClass('lc_hide');
    }
    if($('input[name="ETS_LC_USE_SOUND_FONTEND"]:checked').val()==1)
    {
        $('.chatbox_demo_backend .lc_sound').removeClass('lc_hide');
    }
    else
    {
        $('.chatbox_demo_backend .lc_sound').addClass('lc_hide');
    }
//    if($('input[name="ETS_LC_DISPLAY_COMPANY_INFO"]:checked').val()==1)
//    {
//        $('.chatbox_demo_backend .lc_company_info').removeClass('lc_hide');
//        $('.chatbox_demo_backend').removeClass('no_display_compay_info');
//    }
//    else
//    {
//        $('.chatbox_demo_backend .lc_company_info').addClass('lc_hide');
//        $('.chatbox_demo_backend').addClass('no_display_compay_info');
//    }
    if($('#ETS_LC_DISPLAY_COMPANY_INFO_general').is(':checked'))
    {
        $('.chatbox_demo_backend .lc_company_info').addClass('display_company_info'); 
    }
    else
        $('.chatbox_demo_backend .lc_company_info').removeClass('display_company_info'); 
    if($('#ETS_LC_AVATAR_IMAGE_TYPE').val()=='rounded')
    {
        if($('.lc_company_logo').hasClass('lc_avatar_square'))
        {
            $('.lc_company_logo').removeClass('lc_avatar_square');
            $('.lc_company_logo').addClass('lc_avatar_rounded');
        }
    }
    else
    {
        if($('.lc_company_logo').hasClass('lc_avatar_rounded'))
        {
            $('.lc_company_logo').removeClass('lc_avatar_rounded');
            $('.lc_company_logo').addClass('lc_avatar_square');
        }
    }
    $('.lc_send_box input').css('background',$('input[name="LC_BACKGROUD_COLOR_BUTTON"]').val());
}
function lc_getFormTicketForm(id_form)
{
    $.ajax({
        url: ETS_LC_MODULE_URL_ADMIM,
        data: {
            get_form_ticket_form : 1,
            id_form: id_form,
        },
        type: 'post',
        dataType: 'json',
        success: function(json){
            $('body').removeClass('lc_loading');
            $('.lc_block_form_new_ticket').html(json.form_html);
            if(json.fields_list)
            {
                $('#list-fields').html(json.fields_list);
                if(id_form==1)
                    $('#form_ticket_form').addClass('form-private');
            }
            $('.lc_system_ticket').hide();
            $('.lc_block_form_new_ticket').show();
            lc_updatePositionField();
            lc_displayForm();
            lc_displayFormField();
        },
        error: function(xhr, status, error)
        {
            var err = eval("(" + xhr.responseText + ")");     
            $.growl.error({ message: err.Message }); 
            $('body').removeClass('lc_loading');              
        }
    }); 
}
function  lc_displayUpdatePositionDepartments()
{
    if($('#department-list-sort').length<=0)
        return false;
    var $mySort= $('#department-list-sort');
	$mySort.sortable({
    		opacity: 0.6,
            handle: ".sort_order",
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updatePositionDepartments";
                $('#module_form .form-wrapper .module_error').remove();					
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: ETS_LC_MODULE_URL_AJAX,
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:order,
        			success: function(jsonData)
        			{
        			     if(jsonData.error)
                         {
                            $('body').removeClass('lc_loading');
                            $('#module_form .form-wrapper').append(jsonData.error);
                            $('body').removeClass('lc_loading');
                            $mySort.sortable("cancel");
                         }
                         else
                         {
                            if(jsonData.success)
                             {
                                var i = 1;
                			     $('#department-list-sort .sort_order').each(function(){
                			         $(this).find('span').html(i);
                                     i++;
                			     });
                                $('body').addClass('lc_loading');
                                lc_displayMessage(jsonData.success);
                             }
                         }  
                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
	});
}
function lc_displayFormField()
{
    
    if($('.form-group.ticket-form-field').length)
    {
        $('.form-group.ticket-form-field').each(function(){
            if($(this).find('.form-group.field_type select').val()=='text')
            {
                $(this).find('.form-group.field_contact_email').hide();
                $(this).find('.form-group.field_contact_name').show();
                $(this).find('.form-group.field_contact_subject').show();
                $(this).find('.form-group.field_contact_placeholder').show();
                $(this).find('.form-group.field_contact_option').hide();
                $(this).find('.form-group.field_contact_phone').hide();
            }
            else if($(this).find('.form-group.field_type select').val()=='email')
            {
                $(this).find('.form-group.field_contact_email').show();
                $(this).find('.form-group.field_contact_name').hide();
                $(this).find('.form-group.field_contact_subject').hide();
                $(this).find('.form-group.field_contact_placeholder').show();
                $(this).find('.form-group.field_contact_option').hide();
                $(this).find('.form-group.field_contact_phone').hide();
            }
            else if($(this).find('.form-group.field_type select').val()=='text_editor' || $(this).find('.form-group.field_type select').val()=='phone_number')
            {
                $(this).find('.form-group.field_contact_email').hide();
                $(this).find('.form-group.field_contact_name').hide();
                $(this).find('.form-group.field_contact_subject').hide();
                $(this).find('.form-group.field_contact_placeholder').show();
                $(this).find('.form-group.field_contact_option').hide();
                $(this).find('.form-group.field_contact_phone').hide();
            }
            if($(this).find('.form-group.field_type select').val()=='phone_number')
            {
                $(this).find('.form-group.field_contact_email').hide();
                $(this).find('.form-group.field_contact_name').hide();
                $(this).find('.form-group.field_contact_subject').hide();
                $(this).find('.form-group.field_contact_placeholder').hide();
                $(this).find('.form-group.field_contact_option').hide();
                $(this).find('.form-group.field_contact_phone').show();
            }
            else if($(this).find('.form-group.field_type select').val()=='select' || $(this).find('.form-group.field_type select').val()=='radio' || $(this).find('.form-group.field_type select').val()=='file')
            {
                $(this).find('.form-group.field_contact_email').hide();
                $(this).find('.form-group.field_contact_name').hide();
                $(this).find('.form-group.field_contact_subject').hide();
                $(this).find('.form-group.field_contact_placeholder').hide();
                if($(this).find('.form-group.field_type select').val()=='file')
                    $(this).find('.form-group.field_contact_option').hide();
                else
                    $(this).find('.form-group.field_contact_option').show();
                $(this).find('.form-group.field_contact_phone').hide();
            } 
        });
    }
}
function lc_displayFromFromTicket()
{
    if($('.ticket_tab.active').length==0)
        return false;
    if($('.ticket_tab.active').data('tab')=='email')
    {
        if($('#mail_new_ticket_custom_emails').is(':checked'))
        {
            $('.form-group.ticket.custom_email').show();
        }
        else
            $('.form-group.ticket.custom_email').hide();
    }
    if($('.ticket_tab.active').data('tab')=='general')
    {
        if($('input[name="require_select_department"]:checked').val()==1)
        {
            $('.form-group.ticket.departments').show();
        }
        else
            $('.form-group.ticket.departments').hide();
        if($('#departments_all:checked').length)
        {
            $('input[name="departments[]"]').each(function(){
               $(this).attr('checked','checked');
               if($(this).attr('id')!='departments_all')
                    $(this).attr('disabled','disabled');
            });
        }
        else
        {
            $('input[name="departments[]"]').each(function(){
               $(this).removeAttr('disabled');
            });
        }
    }
    
}
function lc_displayMessage(message)
{
    $('.lc_custom_loading .squaresWaveG').hide();
    $('.lc_custom_loading .lc_custom_text_loading').hide();
    $('body').append('<div class="alert lc-alert-success"><span class="success_table"><span><i class="fa fa-check"></i>'+message+'</span></span></div>');
    $('body').removeClass('lc_loading');
    setTimeout(function(){ $('.lc-alert-success').remove(); }, 1000);
    setTimeout(function(){$('.lc_custom_loading .squaresWaveG').show(); $('.lc_custom_loading .lc_custom_text_loading').show(); }, 1500);
}
function lc_displayCompanyInfo(){
    $.ajax({
        url: ETS_LC_MODULE_URL_AJAX,
        type: 'post',
        dataType: 'json',
        data: {
            change_company_info: 1,
            value:$('input[name="ETS_LC_DISPLAY_COMPANY_INFO"]:checked').val(),
            shop_name: $('#ETS_LC_COMPANY_NAME').val(),
        },
        success: function(json)
        { 
            $('.lc_company_info .lc_company_logo img').attr('src',json.logo);
            $('.lc_company_info .company-name').html(json.name);
        }
    });
}
function lc_readURL(input){
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            if($(input).parents('.col-lg-9').eq(0).find('.preview_img').length <= 0)
            {
                $(input).parents('.col-lg-9').eq(0).append('<div class="preview_img"><img src="'+e.target.result+'"/> <i style="font-size: 20px;" class="process-icon-delete del_preview"></i></div>');
            }
            else
            {
                $(input).parents('.col-lg-9').eq(0).find('.preview_img img').eq(0).attr('src',e.target.result);
            }
            if($(input).parents('.col-lg-9').eq(0).next('.uploaded_image_label').length > 0)
            {
                $(input).parents('.col-lg-9').eq(0).next('.uploaded_image_label').addClass('hidden'); 
                $(input).parents('.col-lg-9').eq(0).next('.uploaded_image_label').next('.uploaded_img_wrapper').addClass('hidden');
            }
                                      
        }
        reader.readAsDataURL(input.files[0]);
    }
}
function lc_displayFormExtra()
{
    var footer=true;
    if($('body.ets_lv_tab-auto_reply').length)
    {
        if($('#block_form_auto_reply:not(:hidden)').length)
        {
            $('.ybc-blog-tab-auto_reply > .form-group').hide();
            footer=false;
        }
        else
        {
            $('.ybc-blog-tab-auto_reply > .form-group').show();
        }
    }
    if($('body.ets_lv_tab-pre_made_message').length)
    {
        if($('#block_form_pre_made_message:not(:hidden)').length)
        {
            $('.ybc-blog-tab-pre_made_message > .form-group').hide();
            footer=false;
            
        }
        else
        {
            $('.ybc-blog-tab-pre_made_message > .form-group').show();
        }
        
    }
    if($('body.ets_lv_tab-departments').length)
    {
        if($('#block_form_departments:not(:hidden)').length)
        {
            $('.ybc-blog-tab-departments > .form-group').hide();
            footer=false;
            
        }
        else
        {
            $('.ybc-blog-tab-departments > .form-group').show();
        }
        
    }
    if($('body.ets_lv_tab-staffs').length)
    {
        footer=false;
    }
    if(footer==false)
        $('#module_form_submit_btn').hide();
    else
        $('#module_form_submit_btn').show();
    if($('input[name="ETS_LC_DISPLAY_SEND_US_AN_EMAIL"]:checked').val()==1)
    {
        $('.form-group.link_support').show();
        if($('#ETS_LC_LINK_SUPPORT_TYPE').val()=='contact-form')
        {
            $('.form-group.link_support.ticket,.form-group.link_support.custom').hide();
        }
        else
        {
            if($('#ETS_LC_LINK_SUPPORT_TYPE').val()=='ticket-form')
            {
                $('.form-group.link_support.ticket').show();
                $('.form-group.link_support.custom').hide();
            }
            else
            {
                $('.form-group.link_support.ticket').hide();
                $('.form-group.link_support.custom').show();
            }
        }
    }
    else
        $('.form-group.link_support').hide();
    
    
}