/**

 * Prestashop Modules & Themen End User License Agreement
 *
 * This End User License Agreement ("EULA") is a legal agreement between you and Presta-Apps ltd.
 * ( here in referred to as "we" or "us" ) with regard to Prestashop Modules & Themes
 * (herein referred to as "Software Product" or "Software").
 * By installing or using the Software Product you agree to be bound by the terms of this EULA.
 *
 * 1. Eligible Licensees. This Software is available for license solely to Software Owners,
 * with no right of duplication or further distribution, licensing, or sub-licensing.
 * A Software Owner is someone who legally obtained a copy of the Software Product via Prestashop Store.
 *
 * 2. License Grant. We grant you a personal/one commercial, non-transferable and non-exclusive right to use the copy
 * of the Software obtained via Prestashop Store. Modifying, translating, renting, copying, transferring or assigning
 * all or part of the Software, or any rights granted hereunder, to any other persons and removing any proprietary
 * notices, labels or marks from the Software is strictly prohibited. Furthermore, you hereby agree not to create
 * derivative works based on the Software. You may not transfer this Software.
 *
 * 3. Copyright. The Software is licensed, not sold. You acknowledge that no title to the intellectual property in the
 * Software is transferred to you. You further acknowledge that title and full ownership rights to the Software will
 * remain the exclusive property of Presta-Apps Mobile, and you will not acquire any rights to the Software,
 * except as expressly set forth above.
 *
 * 4. Reverse Engineering. You agree that you will not attempt, and if you are a corporation,
 * you will use your best efforts to prevent your employees and contractors from attempting to reverse compile, modify,
 * translate or disassemble the Software in whole or in part. Any failure to comply with the above or any other terms
 * and conditions contained herein will result in the automatic termination of this license.
 *
 * 5. Disclaimer of Warranty. The Software is provided "AS IS" without warranty of any kind. We disclaim and make no
 * express or implied warranties and specifically disclaim the warranties of merchantability, fitness for a particular
 * purpose and non-infringement of third-party rights. The entire risk as to the quality and performance of the Software
 * is with you. We do not warrant that the functions contained in the Software will meet your requirements or that the
 * operation of the Software will be error-free.
 *
 * 6. Limitation of Liability. Our entire liability and your exclusive remedy under this EULA shall not exceed the price
 * paid for the Software, if any. In no event shall we be liable to you for any consequential, special, incidental or
 * indirect damages of any kind arising out of the use or inability to use the software.
 *
 * 7. Rental. You may not loan, rent, or lease the Software.
 *
 * 8. Updates and Upgrades. All updates and upgrades of the Software from a previously released version are governed by
 * the terms and conditions of this EULA.
 *
 * 9. Support. Support for the Software Product is provided by Presta-Apps ltd. For product support, please send an
 * email to support at info@iniweb.de
 *
 * 10. No Liability for Consequential Damages. In no event shall we be liable for any damages whatsoever
 * (including, without limitation, incidental, direct, indirect special and consequential damages, damages for loss
 * of business profits, business interruption, loss of business information, or other pecuniary loss) arising out of
 * the use or inability to use the Software Product. Because some states/countries do not allow the exclusion or
 * limitation of liability for consequential or incidental damages, the above limitation may not apply to you.
 *
 * 11. Indemnification by You. You agree to indemnify, hold harmless and defend us from and against any claims or
 * lawsuits, including attorney's fees that arise or result from the use or distribution of the Software in violation
 * of this Agreement.
 *
 * @author    Presta-Apps Limited
 * @website   www.presta-apps.com
 * @contact   info@presta-apps.com
 * @copyright 2009-2016 Presta-Apps Ltd.
 * @license   Proprietary

*/

$(document).ready(function () {


    var discarded;


    var infobar_ajax_link;





    var infobar_position;


    var lastHeight = 0;


    var lastWidth = 0;


    var bar_top = $(".infobar_wrap.top");


    var bar_bottom = $(".infobar_wrap.bottom");


    var bar_left = $(".infobar_wrap.left");


    var bar_right = $(".infobar_wrap.right");





    var button_top = $('.infobar_btn.top');


    var button_bottom = $('.infobar_btn.bottom');


    var button_left = $('.infobar_btn.left');


    var button_right = $('.infobar_btn.right');





    if(bar_top.length){


        button_left.css('top', '3px');


        bar_left.css('top', '3px');


    }


    if(bar_bottom.length){


        button_right.css('bottom', '3px');


        bar_right.css('bottom', '3px');


    }


    if(bar_left.length){


        button_bottom.css('left', '3px');


    }


    console.log(bar_right.length);


    if(bar_right.length){


        button_top.css('right', '3px');


    }





    $(".infobar_btn.left , .infobar_btn.bottom, .infobar_btn.top, .infobar_btn.right").on('click', function () {


        var button_position = $(this).attr('id');


        var wrap = $(".infobar_wrap." + button_position);


        var text = $(".infobar_wrap." + button_position + ' div').first().is(':visible');





        discarded = wrap.data('position');


        infobar_ajax_link = wrap.data('link');


        infobar_position = wrap.data('position');





        var toggle_button = $(this);





        //$('.infobar_text.' + button_position).animate({width: 'toggle'});





        if(infobar_position == 'top' || infobar_position == 'bottom') {


                $('.infobar_text.' + infobar_position).slideToggle({


                    duration: 300,


                    step: function (now, tween) {


                        if(!text){


                            if(infobar_position == 'bottom'){


                                toggle_button.css(infobar_position, wrap.height() + 'px');


                                bar_right.css(infobar_position, wrap.height() + 'px');


                                bar_left.css(infobar_position, wrap.height() + 'px');


                                button_right.css(infobar_position, wrap.height() + 'px');


                            }


                            else{


                                toggle_button.css(infobar_position, wrap.height() + 'px');


                                bar_right.css(infobar_position, wrap.height() + 'px');


                                bar_left.css(infobar_position, wrap.height() + 'px');


                                button_left.css(infobar_position, wrap.height() + 'px');


                            }


                        }


                        else{


                            if(infobar_position == 'bottom'){


                                toggle_button.css(infobar_position, wrap.height() + 'px');


                                bar_right.css(infobar_position, wrap.height() + 'px');


                                bar_left.css(infobar_position, wrap.height() + 'px');


                                button_right.css(infobar_position, wrap.height() + 'px');


                            }


                            else{


                                toggle_button.css(infobar_position, wrap.height() + 'px');


                                bar_right.css(infobar_position, wrap.height() + 'px');


                                bar_left.css(infobar_position, wrap.height() + 'px');


                                button_left.css(infobar_position, wrap.height() + 'px');


                            }


                        }


                    },


                    start: function () {


                        lastHeight = wrap.height();


                    },


                    complete: function () {


                        if (lastHeight > wrap.height() && !discarded) {


                            $.get(infobar_ajax_link);


                            discarded = true;


                        }





                        $('.infobar_btn_sign.' + button_position).toggleClass('opened');





                        lastHeight = wrap.height();


                    }


                });


        }


        else{


            $('.infobar_text.' + infobar_position).animate({


                width: 'toggle',


                duration: 400,


            },{


                step: function () {


                    if(!text){


                        if(infobar_position == 'left'){


                            toggle_button.css(infobar_position, wrap.width() + 'px');


                            button_bottom.css(infobar_position, wrap.width() + 'px');


                            $('#gear-right').css('display', 'none');


                        }


                        else{


                            toggle_button.css(infobar_position, wrap.width() + 'px');


                            button_top.css(infobar_position, wrap.width() + 'px');


                        }


                    }


                    else{


                        if(infobar_position == 'left'){


                            toggle_button.css(infobar_position, (wrap.width() - 7) + 'px');


                            button_bottom.css(infobar_position, (wrap.width() - 7) + 'px');


                            $('#gear-right').css('display', 'block');


                        }


                        else{


                            toggle_button.css(infobar_position, (wrap.width() - 7) + 'px');


                            button_top.css(infobar_position, (wrap.width() - 7) + 'px');


                        }


                    }


                },


                start: function () {


                    lastWidth = wrap.width();


                },


                complete: function () {


                    if (lastWidth > wrap.width() && !discarded) {


                        $.get(infobar_ajax_link);


                        discarded = true;


                    }





                    $('.infobar_btn_sign.' + button_position).toggleClass('opened');





                    lastWidth = wrap.width();


                }


            });


        }


    });





    if (bar_top.data('expanded')) {


        $(button_top).click();


    }


    if (bar_bottom.data('expanded')) {


        $(button_bottom).click();


    }


    if (bar_left.data('expanded')) {


        $(button_left).click();


    }


    if (bar_right.data('expanded')) {


        $(button_right).click();


    }





    $('.infobar_btn_sign').css({


		'-moz-user-select': '-moz-none',


		'-moz-user-select': 'none',


		'-o-user-select': 'none',


		'-khtml-user-select': 'none',


		'-webkit-user-select': 'none',


		'-ms-user-select': 'none',


		'user-select': 'none'


	}).bind('selectstart', function () {


		return false;


	});





    $('body').prepend($(".infobar_wrap"));


});