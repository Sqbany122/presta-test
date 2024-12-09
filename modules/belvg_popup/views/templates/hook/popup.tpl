<div id="showScroll"></div>
{literal}
    <script>
        document.addEventListener('DOMContentLoaded', function ()
        {
            var popUpContent = {/literal}{$BELVG_POPUP_CONTENT|unescape: "html" nofilter}{literal};
            $('<div/>', {
                id: 'popup-hide-content',
                html: popUpContent
            }).appendTo('body').hide();

            var cookieCounterName = '{/literal}{$BELVG_POPUP_COOKIE_NAME_PREFIX}{literal}' + '_popUpCounter';
            var cookieSubscribeName = '{/literal}{$BELVG_POPUP_COOKIE_NAME_PREFIX}{literal}' + '_popUpSubscribe';

            if(
                (getCookie(cookieCounterName) == undefined
                || getCookie(cookieCounterName) < {/literal}{$BELVG_POPUP_NUMBER_OF_APPEARANCE}{literal})
                && getCookie(cookieSubscribeName) != 1
            ){
                {/literal}
                    {if $BELVG_POPUP_TRIGGER_MECHANISM == 'delay'}
                        {literal}
                            setTimeout(function ()
                            {
                                showPopUP();
                            }, {/literal}{$BELVG_POPUP_DELAY*1000}{literal})
                        {/literal}
                    {elseif $BELVG_POPUP_TRIGGER_MECHANISM == 'scroll'}
                        {literal}
                            var closeFlag = true;
                            window.addEventListener('scroll', function() {
                                var scrolled = window.pageYOffset || document.documentElement.scrollTop;
                                if(scrolled > 1000 && closeFlag){
                                    showPopUP();
                                    closeFlag = false;
                                }
                            });
                        {/literal}
                    {/if}
                {literal}
            }

            function showPopUP()
            {
                $.fancybox.open({
                        href: '#popup-hide-content',
                        type: 'inline'
                    },
                    {
                        padding: 0,
                        helpers:{
                            overlay: {
                                locked: false
                            }
                        },
                        afterLoad: function ()
                        {
                            if (getCookie(cookieCounterName) === undefined) {
                                setCookie(cookieCounterName, 1,  {expires: 20*24*60*60})
                            } else {
                                var currentCounter = getCookie(cookieCounterName)
                                setCookie(cookieCounterName, parseInt(currentCounter) + 1)
                            }
                        }
                    })
            }

            $("#cpp-form").submit(function (event)
            {
                event.preventDefault();

                var frontendController = '{/literal}{$BELVG_POPUP_FRONT_CONTROLLER_LINK}{literal}';
                var email = $('#cpp-form > div.belvg-popup-halloween__input > input[type="email"]').val();

                $.ajax({
                    url: frontendController,
                    cache: false,
                    type: 'POST',
                    data: {
                        ajax: true,
                        email: email
                    },
                    success : function(result){
                        var resultObj = JSON.parse(result);
                        if(resultObj.valid){
                            $('#belvg-popup-error-message').hide();
                            $('<div/>', {
                                id: 'belvg-popup-success-message',
                                html: resultObj.valid,
                                class: 'message',
                                style: 'color:green'
                            }).appendTo('.belvg-popup-halloween__input');
                            setCookie(cookieSubscribeName, 1, {expires: 7*24*60*60})
                            setTimeout($.fancybox.close, 3000);
                        } else if(resultObj.error){
                            $('<div/>', {
                                id: 'belvg-popup-error-message',
                                html: resultObj.error,
                                class: 'message',
                                style: 'color:red'
                            }).appendTo('.belvg-popup-halloween__input');
                        }
                    },
                    error: function (jqXHR)
                    {
                        console.log(jqXHR);
                    }
                });
            });

            function setCookie(name, value, options)
            {
                options = options || {}

                var expires = options.expires

                if (typeof expires == 'number' && expires) {
                    var d = new Date()
                    d.setTime(d.getTime() + expires * 1000)
                    expires = options.expires = d
                }
                if (expires && expires.toUTCString) {
                    options.expires = expires.toUTCString()
                }

                value = encodeURIComponent(value)

                var updatedCookie = name + '=' + value

                for (var propName in options) {
                    updatedCookie += '; ' + propName
                    var propValue = options[propName]
                    if (propValue !== true) {
                        updatedCookie += '=' + propValue
                    }
                }

                document.cookie = updatedCookie
            }

            function getCookie(name)
            {
                var matches = document.cookie.match(new RegExp(
                    '(?:^|; )' + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'
                ))
                return matches ? decodeURIComponent(matches[1]) : undefined
            }
        })
    </script>
{/literal}