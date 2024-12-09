/**
 * 2009-2017 Blue Train Lukasz Kowalczyk / PrestaCafe
 *
 * NOTICE OF LICENSE
 *
 * Lukasz Kowalczyk is the owner of the copyright of this module.
 * All rights of any kind, which are not expressly granted in this
 * License, are entirely and exclusively reserved to and by Lukasz
 * Kowalczyk.
 *
 * You may not rent, lease, transfer, modify or create derivative
 * works based on this module.
 *
 * @author    Lukasz Kowalczyk <lkowalczyk@prestacafe.pl>
 * @copyright Lukasz Kowalczyk
 * @license   LICENSE.txt
 */
var PrestaCafePayuAdmin = {};

(function() {
    var self = this;

    self.onPageLoadPs15 = function(prestacafeCurrentTab) {
        // Create tab panels inside module's form.
        $('<div class="ps15 tab-content" id="prestacafe_tab_content">'
            +'<div class="tab-pane active" id="prestacafe_tab_general"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_pln"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_eur"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_usd"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_gbp"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_czk"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_troubleshooting"></div>'
        +'</div>')
        .appendTo('#module_form');

        $('<input type="hidden" name="prestacafe_current_tab" value="' + prestacafeCurrentTab + '">')
        .prependTo('#module_form');

        // Remove existing form panels from their places in DOM and append them
        // inside tab-panels created above.
        var div_no = 0;
        $('#module_form > fieldset').each(function() {
            ++div_no;
            $(this)
            .detach()
            .css('display', 'inline-block')
            .css('width', '100%')
            .appendTo('#prestacafe_tab_content > div:nth-child(' + div_no + ')');
            $(this).find('legend').remove();
        });

        $('#prestacafe_tabs a').click(function(e) {
            e.preventDefault();
            $('#prestacafe_tabs > li.active').removeClass('active');
            $(this).parent().addClass('active');
            $('#prestacafe_tab_content > div.active').removeClass('active');
            $($(this).attr('href')).addClass('active');
            $('input[name="prestacafe_current_tab"]').val($(this).attr('href').substring(1));
        });

        if (prestacafeCurrentTab) {
            $('#prestacafe_tabs > li.active').removeClass('active');
            $('#prestacafe_tabs a[href="#' + prestacafeCurrentTab + '"]').parent('li').addClass('active');
            $('#prestacafe_tab_content > div.tab-pane').removeClass('active');
            $('#' + prestacafeCurrentTab).addClass('active');
        }
    };

    self.onPageLoadPs16 = function(prestacafeCurrentTab) {
        self.onPageLoadPs17(prestacafeCurrentTab);
    };

    self.onPageLoadPs17 = function(prestacafeCurrentTab) {
        // Create tab panels inside module's form.
        $('<div class="tab-content" id="prestacafe_tab_content">'
            +'<div class="tab-pane active" id="prestacafe_tab_general"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_pln"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_eur"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_usd"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_gbp"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_pos_czk"></div>'
            +'<div class="tab-pane" id="prestacafe_tab_troubleshooting"></div>'
        +'</div>')
        .appendTo('#module_form');

        $('<input type="hidden" name="prestacafe_current_tab" value="' + prestacafeCurrentTab + '">')
        .prependTo('#module_form');

        // Remove existing form panels from their places in DOM and append them
        // inside tab-panels created above.
        var div_no = 0;
        $('#module_form > div.panel').each(function() {
            ++div_no;
            $(this)
            .detach()
            .css('display', 'inline-block')
            .css('width', '100%')
            .appendTo('#prestacafe_tab_content > div:nth-child(' + div_no + ')');
            $(this).find('div.panel-heading').remove();
            $(this).find('div.form-wrapper').removeClass('form-wrapper');
            $(this).find('div.panel-footer').removeClass('panel-footer');
        });

        // $('#prestacafe_tab_content').addClass('panel');

        $('#prestacafe_tabs a').click(function(e) {
            e.preventDefault();
            $(this).tab('show');
            $('input[name="prestacafe_current_tab"]').val($(this).attr('href').substring(1));
        });

        if (prestacafeCurrentTab) {
            $('#prestacafe_tabs a[href="#' + prestacafeCurrentTab + '"]').tab('show');
        }
    };
}).apply(PrestaCafePayuAdmin);
