 /*
 *    ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      views/js/params.js
 *    @subject   Manages the configuration ajax saving system
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *
 *    Support by mail: support@ambris.com
 **/


(function($) {
  var compat_jQ = (function() {
    try {
      return amb_jQ;
    } catch (err) {
      return $;
    }
  })();
  (function($) {

    $(document).ready(function() {


      $('table.table td > a').click(function(e){
        e.stopPropagation();
      });
      $('table.table td > a').parent().prop('onclick',null).off('click')


      $('span.process-icon-configure').html('<i class="process-icon-configure"></i>');
      $('#view_selector.compat').insertAfter($('[name=list_table] tr').first().find('td').first().find('span').first())

      $("#amb_customizer_view").change(function(){
       window.location.href = $(this).data('url')+'&amb_customizer_view_id='+$(this).val();
      });

    //tgt = $('.panel-heading').first()
    //$("#view_selector").detach().insertAfter(tgt)

    $('[data-edit-view]').click(function(){
      view = $(this).data('edit-view');
      $('#edit-view-'+view).show();
      $('#show-view-'+view).hide();
    });

    $('[data-save-edit-view]').click(function(){
       element = $(this);
       view = element.data('save-edit-view');
       $.ajax({
          async: false,
        url: 'ajax-tab.php',
        data: {
          value: $('#new-value-'+view).val(),
          viewname: view,
          ajax: '1',
          name: $('#controller_name').val(),
          controller: 'AdminAmbBoCustomizerParams',
          action: 'UpdateListDisplayName',
          token: $('#token').val()
        },
        success: function(data) {
          localData = data;
          showSuccessMessage(update_success_msg);
          $('#edit-view-'+view).hide();
          $('#show-view-'+view).show();
          $('.display-name-'+view).html($('#new-value-'+view).val());
        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(xhr);
        }
        });
    });


     $(".activate_field").change(function(){

      element = $(this);
       $.ajax({
          async: true,
        url: 'ajax-tab.php',
        data: {
          field: element.attr('name'),
          value: element.val(),
          ajax: '1',
          name: $('#controller_name').val(),
          controller: 'AdminAmbBoCustomizerParams',
          action: 'UpdateActive',
          token: $('#token').val()
        },
        success: function(data) {
          localData = data;
          showSuccessMessage(update_success_msg);
        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(xhr);
        }
        });
     });

     $(".activate_amblist").change(function(){

      element = $(this);
       $.ajax({
          async: true,
        url: 'ajax-tab.php',
        data: {
          value: element.val(),
          ajax: '1',
          name: element.data('controller_name'),
          controller: 'AdminAmbBoCustomizerParams',
          action: 'UpdateListActive',
          token: $('#token').val()
        },
        success: function(data) {
          localData = data;
          showSuccessMessage(update_success_msg);
        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(xhr);
        }
        });



     });
    });
  })(compat_jQ);
})(jQuery);

