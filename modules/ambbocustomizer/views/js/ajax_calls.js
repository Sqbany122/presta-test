 /*
  *    ambBOcustomizer Module : customize the prestashop back-office
  *
  *    @module    BO Customizer (AmbBoCustomizer)
  *    @file      views/js/ajax_calls.js
  *    @subject   Manages ajax calls for callbacks
  *    @author    Ambris Informatique
  *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
  *    @license   Commercial license
  *
  *    Support by mail: support@ambris.com
  **/

 (function($) {
     var compat_jQ = (function() {
         try {
             var jQ = amb_jQ;
             if (jQ.fn.popover === undefined)
                return $;
             else
                return amb_jQ;
         } catch (err) {
             return $;
         }
     })();

     (function($) {

         $(document).ready(function() {
            if ($.fn.popover !== undefined) {
                 $('[data-toggle="popover"]').popover({
                     html: true,
                     container: 'body',
                     placement: 'right',
                     trigger: 'hover',
                     template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                 });
             }
         });


         var amb_bo_customizer_tooltips = function() {
            if ($.fn.popover !== undefined) {
                 $('.amb_bo_customizer_tooltip').popover({
                     content: hoverGetData,
                     html: true,
                     container: 'body',
                     placement: 'auto top',
                     trigger: 'hover',
                     template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content nowrap"></div></div>'
                 });
            }
             var cachedData = Array();

             function hoverGetData() {
                 var element = $(this);

                 var id = element.data('method') + element.data('id');

                 if (id in cachedData) {
                     return cachedData[id];
                 }

                 var localData = "error";

                 $.ajax({
                     async: false,
                     url: 'ajax-tab.php',
                     dataType: "json",
                     data: {
                         id: element.data('id'),
                         ajax: '1',
                         controller: 'AdminAmbBoCustomizerAjax',
                         token: element.data('token'),
                         action: element.data('method'),
                     },
                     success: function(data) {
                         localData = (data.html ? data.html : data);
                     },
                     error: function(xhr, textStatus, errorThrown) {
                         console.log(xhr);
                     }
                 });

                 cachedData[id] = localData;

                 return localData;
             }

             $('.hoverToolTip').on('click', function(e) {
                 //e.preventDefault();
                 e.stopImmediatePropagation();
             });

         };

         if (window.addEventListener) {
             window.addEventListener('load', amb_bo_customizer_tooltips, false);
         } else if (window.attachEvent) {
             window.attachEvent('onload', amb_bo_customizer_tooltips);
         }


 })(compat_jQ);
})(jQuery);
