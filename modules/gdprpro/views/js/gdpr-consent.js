/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */
;(function ($, window, document, undefined) {

    "use strict";

    var pluginName = "prestaGdprConsent";
    var defaults = {
        content: "",
        showOnPageLoad: false,
        animateTabChange: false,
        cookieStoreUrl: "/",
        modules: {},
        checkAllByDefault: true,
        reloadAfterSave: false,
        reloadAfterAcceptAll: false,
        under18AlertText: "You need to be 18+ to visit this site",
        closeModalOnlyWithButtons: false,
        acceptByScroll: false
    };

    function Plugin(element, options) {
        this.element = element;
        this.settings = $.extend({}, defaults, options);
        this.settings.acceptByScroll = options.acceptByScroll;
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    $.extend(Plugin.prototype, {
        init: function () {
            var that = this;
            $(document).on("gdprModuleCheckBoxesChanged", function () {
                that.updateSelectedModules();
            });
            var instance = gdprModal.create(
                this.settings.content,
                {
                    closable: !that.settings.closeModalOnlyWithButtons
                }
            );
            instance.init();

            if (that.settings.checkAllByDefault && typeof(this.settings.showOnPageLoad) !== "undefined" && this.settings.showOnPageLoad) {
                for (var key in that.settings.modules) {
                    that.settings.modules[key] = "true";
                }
            }
            this.mdlCheckboxes = $('.module-cookies-chkbox');
            this.initTabs();
            this.initCheckboxes();

            $('#close-gdpr-consent').on('click', function () {
                if (that.settings.reloadAfterSave) {
                    $(document).ajaxStop(function () {
                        location.reload();
                    });
                }
                instance.close();
                that.saveSettingsToCookie();
            });
            $(document).on('click', '#under-18', function (event) {
                if (confirm(that.settings.under18AlertText) !== true) {
                    window.location.href = "about:blank";
                }
                $(this).removeClass('all_checked');
                $(this).addClass('not_checked');
                event.preventDefault();
                event.stopPropagation();
            });
            if (this.settings.showOnPageLoad) {
                instance.show();
            }

            $('.show-gdpr-modal').on('click', function () {
                instance.show();
                return false;
            });
            this.postInit();
            $(document).on('gdprModuleCheckBoxesChanged', function (event, name, isEnabled) {
                name = name.replace("_","-");
                if (isEnabled) {
                    $('body').removeClass(name + "-off").addClass(name + "-on");
                } else {
                    $('body').removeClass(name + "-on").addClass(name + "-off");
                }
            });

            var acceptByScroll = gdprSettings.acceptByScroll;
            console.log(acceptByScroll);
            $(window).scroll(function () {
                if (acceptByScroll && $('.gdprModal').is(":visible")) {
                    instance.close();
                    that.saveSettingsToCookie();
                }
            });
        },
        updateSelectedModules: function () {
            $('#gdpr-selected-count').text($('.module-cookies-chkbox:checked').length);
        },
        /**
         * Initiate the tabs
         */
        initTabs: function () {
            /**
             * Init the modal tabs
             */
            var tabs = $('.gdpr-consent-tabs');

            tabs.each(function () {
                var tab = $(this),
                    tabItems = tab.find('ul.gdpr-consent-tabs-navigation'),
                    tabContentWrapper = tab.find('ul.gdpr-consent-tabs-content'),
                    tabNavigation = tab.find('nav');

                tabItems.on('click', 'a', function (event) {
                    var selectedItem = $(this);
                    if ($(selectedItem).attr("href")) {
                        return true;
                    }
                    event.preventDefault();
                    if (!selectedItem.hasClass('selected')) {
                        var selectedTab = selectedItem.data('content'),
                            selectedContent = tabContentWrapper.find('li[data-content="' + selectedTab + '"]'),
                            slectedContentHeight = window.innerHeight * 0.8;

                        tabItems.find('a.selected').removeClass('selected');
                        selectedItem.addClass('selected');
                        selectedContent.addClass('selected').siblings('li').removeClass('selected');
                    }
                });

                //hide the .gdpr-consent-tabs::after element when tabbed navigation has scrolled to the end (mobile version)
                checkScrolling(tabNavigation);
                tabNavigation.on('scroll', function () {
                    checkScrolling($(this));
                });
            });
            tabs.each(function () {
                var tab = $(this);
                checkScrolling(tab.find('nav'));
                //tab.find('.gdpr-consent-tabs-content').css('height', window.innerHeight * 0.8);
            });
            $(window).on('resize', function () {
                tabs.each(function () {
                    var tab = $(this);
                    checkScrolling(tab.find('nav'));
                    //tab.find('.gdpr-consent-tabs-content').css('height', window.innerHeight * 0.8);
                });
            });

            function checkScrolling(tabs) {
                var totalTabWidth = parseInt(tabs.children('.gdpr-consent-tabs-navigation').width()),
                    tabsViewport = parseInt(tabs.width());
                if (tabs.scrollLeft() >= totalTabWidth - tabsViewport) {
                    tabs.parent('.gdpr-consent-tabs').addClass('is-ended');
                } else {
                    tabs.parent('.gdpr-consent-tabs').removeClass('is-ended');
                }
            }
        },
        initCheckboxes: function () {
            var that = this;
            this.mdlCheckboxes.each(function () {
                this.addEventListener('change', function (event) {
                    console.log(this);
                    that.settings.modules[$(this).data('mdl')] = this.checked;
                    
                    $(document).trigger("gdprModuleCheckBoxesChanged", [$(this).data('mdl'), this.checked]);
                    if (this.checked) {
                        $(this).closest(".td_checkbox").addClass('allowed');

                        $('.div_cookie_category #span_under_16').removeClass('all_checked').addClass('not_checked');
                    }
                    else {
                        $(this).closest(".td_checkbox").removeClass('allowed');
                    }

                    var category_stat = 'not_checked';
                    var not_checked_checkboxes = $(this).closest(".cookie-category-tab").find('.module-cookies-chkbox:not(:checked)').length;
                    var checked_checkboxes = $(this).closest(".cookie-category-tab").find('.module-cookies-chkbox:checked').length;
                    var checkboxes = $(this).closest(".cookie-category-tab").find('.module-cookies-chkbox').length;
                    if (checked_checkboxes == checkboxes) {
                        category_stat = 'all_checked';
                    }
                    else {
                        if (not_checked_checkboxes == checkboxes) {
                            category_stat = 'not_checked';
                        }
                        else {
                            category_stat = 'partial_checked';
                        }
                    }

                    var checkboxgroup = $(this).closest(".cookie-category-tab");

                    $('.div_summary_checkboxes .div_cookie_category').each(function () {
                        var cookiecategory = $(this).attr('id');
                        if (checkboxgroup.hasClass(cookiecategory)) {
                            $(this).children('span').removeClass().addClass('span-preferences').addClass(category_stat);
                        }
                    });
                });

                try {
                    var moduleName = $(this).data('mdl');
                    if (that.settings.modules[moduleName] === "true") {
                        this.checked = true;
                        $(this).closest(".td_checkbox").addClass('allowed');
                    }
                    else {
                        $(this).closest(".td_checkbox").removeClass('allowed');
                    }
                } catch (error) {
                    console.error(error);
                }
            });
            $(document).trigger("gdprModuleCheckBoxesChanged");
            $(document).on('click', '#gdpr-check-all-modules', function () {
                $(this).closest('.table').find('.module-cookies-chkbox:not(:checked)').trigger('click');
                $('.div_cookie_category #span_under_16').removeClass('all_checked').addClass('not_checked');
            });
            $(document).on('click', '#accept-all-gdpr', function () {
                if (that.settings.reloadAfterAcceptAll) {
                    $(document).ajaxStop(function () {
                        location.reload();
                    });
                }
                $('.module-cookies-chkbox:not(:checked)').trigger('click');
                $('#close-gdpr-consent').trigger('click');

                $('.div_cookie_category #span_under_16').removeClass('all_checked').addClass('not_checked');
            });

            $(document).on('click', '#reject-all-gdpr', function() {
                if (that.settings.reloadAfterAcceptAll) {
                    $(document).ajaxStop(function() {
                        location.reload()
                    })
                }
                $('.module-cookies-chkbox:checked').trigger('click');
                $('#close-gdpr-consent').trigger('click');
                $('.div_cookie_category #span_under_16').removeClass('all_checked').addClass('not_checked')
            })
        },
        saveSettingsToCookie: function () {
            var event = new CustomEvent("gdprSaveEvent", {gdprSettings: this.settings.modules});
            document.dispatchEvent(event);
            $.post(this.settings.cookieStoreUrl, {gdprSettings: this.settings.modules});
        },
        postInit: function () {

            $('.div_summary_checkboxes .div_cookie_category').each(function () {
                var cookiecategory = $(this).attr('id');
                var category_stat = 'not_checked';
                $('.gdpr-consent-tabs-content li').each(function () {
                    if ($(this).hasClass(cookiecategory)) {
                        var not_checked_checkboxes = $(this).find('.module-cookies-chkbox:not(:checked)').length;
                        var checked_checkboxes = $(this).find('.module-cookies-chkbox:checked').length;
                        var checkboxes = $(this).find('.module-cookies-chkbox').length;
                        if (checked_checkboxes == checkboxes) {
                            category_stat = 'all_checked';
                        }
                        else {
                            if (not_checked_checkboxes == checkboxes) {
                                category_stat = 'not_checked';
                            }
                            else {
                                category_stat = 'partial_checked';
                            }
                        }
                    }
                });
                $(this).children('span').addClass(category_stat);
            });

            $('.div_summary_checkboxes .div_cookie_category').on('click', function () {
                if ($(this).hasClass('div_under_16')) {
                    $('.module-cookies-chkbox:checked').trigger('click');
                    //$('.module-cookies-chkbox:checked').prop("disabled", true);

                    if ($('.div_summary_checkboxes .div_cookie_category:not(#div_necessary):not(.div_under_16) > span').hasClass('all_checked')) {
                        $('.div_summary_checkboxes .div_cookie_category:not(#div_necessary):not(.div_under_16) > span').removeClass('all_checked').addClass('not_checked');
                    }
                    else {
                        if ($('.div_summary_checkboxes .div_cookie_category:not(#div_necessary):not(.div_under_16) > span').hasClass('partial_checked')) {
                            $('.div_summary_checkboxes .div_cookie_category:not(#div_necessary):not(.div_under_16) > span').removeClass('partial_checked').addClass('not_checked');
                        }
                    }

                    if ($(this).children('span').hasClass('not_checked')) {
                        $(this).children('span').removeClass('not_checked').addClass('all_checked');
                    }
                    else {
                        if ($(this).children('span').hasClass('all_checked')) {
                            $(this).children('span').removeClass('all_checked').addClass('not_checked');
                        }
                    }
                }
                else {
                    var cookiecategory = $(this).attr('id');
                    if (cookiecategory != 'div_necessary') {
                        if ($(this).children('span').hasClass('not_checked') || $(this).children('span').hasClass('partial_checked')) {
                            $('.gdpr-consent-tabs-content li').each(function () {
                                if ($(this).hasClass(cookiecategory)) {
                                    $(this).find('.module-cookies-chkbox:not(:checked)').trigger('click');
                                }
                            });
                            $(this).children('span').removeClass('not_checked').removeClass('partial_checked').addClass('all_checked');
                        }
                        else {
                            if ($(this).children('span').hasClass('all_checked')) {
                                $('.gdpr-consent-tabs-content li').each(function () {
                                    if ($(this).hasClass(cookiecategory)) {
                                        $(this).find('.module-cookies-chkbox:checked').trigger('click');
                                    }
                                });
                                $(this).children('span').removeClass('all_checked').addClass('not_checked');
                            }
                        }
                    }
                }
            });


            $(".div_summary_checkboxes + .div_hide_show .show_details").on("click", function () {
                $(this).parent().toggleClass('open');
                $(".div_center_area").slideDown("slow");
            });
            $(".div_summary_checkboxes + .div_hide_show .hide_details").on("click", function () {
                $(this).parent().toggleClass('open');
                $(".div_center_area").slideUp("slow");
            });


            $(".gdpr-consent-tabs .div_accept_moreinfo .span_moreinfo").on("click", function () {
                $(this).toggleClass('open');
                $(".div_show_moreinfo").slideToggle("slow");
            });

            $(".gdpr-consent-tabs-content .table-responsive .td_description").hover(function () {
                $('.gdpr-consent-tabs-content .table-responsive td.td_description .tooltiptext').css({top: $(this).position().top - $(this).height() / 2 - $(this).children('.tooltiptext').outerHeight() - 15});
            });

            $(".gdpr-consent-tabs-content .table-responsive .td_description").on("click", function () {
                $(".gdpr-consent-tabs-content .table-responsive tr").not($(this).parent()).removeClass('active');
                $(this).parent().toggleClass("active");
            });

            $(window).resize(function () {
                if ($(window).width() >= 650) {
                    $('.gdprModal').height($('body').height());
                }
                else {
                    $('.gdprModal').height($(document).height());
                }
            });
        }
    });

    $.fn[pluginName] = function (options) {

        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" +
                    pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery, window, document);
$(document).ready(function () {
    var content = $("#gdpr-modal-container").html();
    $("#gdpr-modal-container").remove();
    $("#gdpr-consent").prestaGdprConsent({
        content: content,
        showOnPageLoad: (gdprSettings.doNotTrackCompliance && navigator.doNotTrack == 1) ? false : gdprSettings.showWindow,
        cookieStoreUrl: gdprSettings.gdprCookieStoreUrl,
        modules: gdprSettings.gdprEnabledModules,
        checkAllByDefault: gdprSettings.checkAllByDefault,
        reloadAfterSave: gdprSettings.reloadAfterSave,
        reloadAfterAcceptAll: gdprSettings.reloadAfterAcceptAll,
        under18AlertText: gdprSettings.under18AlertText,
        closeModalOnlyWithButtons: gdprSettings.closeModalOnlyWithButtons
    });
});
