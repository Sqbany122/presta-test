/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/



jQuery.migrateMute===void 0&&(jQuery.migrateMute=!0),function(e,t,n){function r(n){var r=t.console;i[n]||(i[n]=!0,e.migrateWarnings.push(n),r&&r.warn&&!e.migrateMute&&(r.warn("JQMIGRATE: "+n),e.migrateTrace&&r.trace&&r.trace()))}function a(t,a,i,o){if(Object.defineProperty)try{return Object.defineProperty(t,a,{configurable:!0,enumerable:!0,get:function(){return r(o),i},set:function(e){r(o),i=e}}),n}catch(s){}e._definePropertyBroken=!0,t[a]=i}var i={};e.migrateWarnings=[],!e.migrateMute&&t.console&&t.console.log&&t.console.log("JQMIGRATE: Logging is active"),e.migrateTrace===n&&(e.migrateTrace=!0),e.migrateReset=function(){i={},e.migrateWarnings.length=0},"BackCompat"===document.compatMode&&r("jQuery is not compatible with Quirks Mode");var o=e("<input/>",{size:1}).attr("size")&&e.attrFn,s=e.attr,u=e.attrHooks.value&&e.attrHooks.value.get||function(){return null},c=e.attrHooks.value&&e.attrHooks.value.set||function(){return n},l=/^(?:input|button)$/i,d=/^[238]$/,p=/^(?:autofocus|autoplay|async|checked|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped|selected)$/i,f=/^(?:checked|selected)$/i;a(e,"attrFn",o||{},"jQuery.attrFn is deprecated"),e.attr=function(t,a,i,u){var c=a.toLowerCase(),g=t&&t.nodeType;return u&&(4>s.length&&r("jQuery.fn.attr( props, pass ) is deprecated"),t&&!d.test(g)&&(o?a in o:e.isFunction(e.fn[a])))?e(t)[a](i):("type"===a&&i!==n&&l.test(t.nodeName)&&t.parentNode&&r("Can't change the 'type' of an input or button in IE 6/7/8"),!e.attrHooks[c]&&p.test(c)&&(e.attrHooks[c]={get:function(t,r){var a,i=e.prop(t,r);return i===!0||"boolean"!=typeof i&&(a=t.getAttributeNode(r))&&a.nodeValue!==!1?r.toLowerCase():n},set:function(t,n,r){var a;return n===!1?e.removeAttr(t,r):(a=e.propFix[r]||r,a in t&&(t[a]=!0),t.setAttribute(r,r.toLowerCase())),r}},f.test(c)&&r("jQuery.fn.attr('"+c+"') may use property instead of attribute")),s.call(e,t,a,i))},e.attrHooks.value={get:function(e,t){var n=(e.nodeName||"").toLowerCase();return"button"===n?u.apply(this,arguments):("input"!==n&&"option"!==n&&r("jQuery.fn.attr('value') no longer gets properties"),t in e?e.value:null)},set:function(e,t){var a=(e.nodeName||"").toLowerCase();return"button"===a?c.apply(this,arguments):("input"!==a&&"option"!==a&&r("jQuery.fn.attr('value', val) no longer sets properties"),e.value=t,n)}};var g,h,v=e.fn.init,m=e.parseJSON,y=/^([^<]*)(<[\w\W]+>)([^>]*)$/;e.fn.init=function(t,n,a){var i;return t&&"string"==typeof t&&!e.isPlainObject(n)&&(i=y.exec(e.trim(t)))&&i[0]&&("<"!==t.charAt(0)&&r("$(html) HTML strings must start with '<' character"),i[3]&&r("$(html) HTML text after last tag is ignored"),"#"===i[0].charAt(0)&&(r("HTML string cannot start with a '#' character"),e.error("JQMIGRATE: Invalid selector string (XSS)")),n&&n.context&&(n=n.context),e.parseHTML)?v.call(this,e.parseHTML(i[2],n,!0),n,a):v.apply(this,arguments)},e.fn.init.prototype=e.fn,e.parseJSON=function(e){return e||null===e?m.apply(this,arguments):(r("jQuery.parseJSON requires a valid JSON string"),null)},e.uaMatch=function(e){e=e.toLowerCase();var t=/(chrome)[ \/]([\w.]+)/.exec(e)||/(webkit)[ \/]([\w.]+)/.exec(e)||/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(e)||/(msie) ([\w.]+)/.exec(e)||0>e.indexOf("compatible")&&/(mozilla)(?:.*? rv:([\w.]+)|)/.exec(e)||[];return{browser:t[1]||"",version:t[2]||"0"}},e.browser||(g=e.uaMatch(navigator.userAgent),h={},g.browser&&(h[g.browser]=!0,h.version=g.version),h.chrome?h.webkit=!0:h.webkit&&(h.safari=!0),e.browser=h),a(e,"browser",e.browser,"jQuery.browser is deprecated"),e.sub=function(){function t(e,n){return new t.fn.init(e,n)}e.extend(!0,t,this),t.superclass=this,t.fn=t.prototype=this(),t.fn.constructor=t,t.sub=this.sub,t.fn.init=function(r,a){return a&&a instanceof e&&!(a instanceof t)&&(a=t(a)),e.fn.init.call(this,r,a,n)},t.fn.init.prototype=t.fn;var n=t(document);return r("jQuery.sub() is deprecated"),t},e.ajaxSetup({converters:{"text json":e.parseJSON}});var b=e.fn.data;e.fn.data=function(t){var a,i,o=this[0];return!o||"events"!==t||1!==arguments.length||(a=e.data(o,t),i=e._data(o,t),a!==n&&a!==i||i===n)?b.apply(this,arguments):(r("Use of jQuery.fn.data('events') is deprecated"),i)};var j=/\/(java|ecma)script/i,w=e.fn.andSelf||e.fn.addBack;e.fn.andSelf=function(){return r("jQuery.fn.andSelf() replaced by jQuery.fn.addBack()"),w.apply(this,arguments)},e.clean||(e.clean=function(t,a,i,o){a=a||document,a=!a.nodeType&&a[0]||a,a=a.ownerDocument||a,r("jQuery.clean() is deprecated");var s,u,c,l,d=[];if(e.merge(d,e.buildFragment(t,a).childNodes),i)for(c=function(e){return!e.type||j.test(e.type)?o?o.push(e.parentNode?e.parentNode.removeChild(e):e):i.appendChild(e):n},s=0;null!=(u=d[s]);s++)e.nodeName(u,"script")&&c(u)||(i.appendChild(u),u.getElementsByTagName!==n&&(l=e.grep(e.merge([],u.getElementsByTagName("script")),c),d.splice.apply(d,[s+1,0].concat(l)),s+=l.length));return d});var Q=e.event.add,x=e.event.remove,k=e.event.trigger,N=e.fn.toggle,T=e.fn.live,M=e.fn.die,S="ajaxStart|ajaxStop|ajaxSend|ajaxComplete|ajaxError|ajaxSuccess",C=RegExp("\\b(?:"+S+")\\b"),H=/(?:^|\s)hover(\.\S+|)\b/,A=function(t){return"string"!=typeof t||e.event.special.hover?t:(H.test(t)&&r("'hover' pseudo-event is deprecated, use 'mouseenter mouseleave'"),t&&t.replace(H,"mouseenter$1 mouseleave$1"))};e.event.props&&"attrChange"!==e.event.props[0]&&e.event.props.unshift("attrChange","attrName","relatedNode","srcElement"),e.event.dispatch&&a(e.event,"handle",e.event.dispatch,"jQuery.event.handle is undocumented and deprecated"),e.event.add=function(e,t,n,a,i){e!==document&&C.test(t)&&r("AJAX events should be attached to document: "+t),Q.call(this,e,A(t||""),n,a,i)},e.event.remove=function(e,t,n,r,a){x.call(this,e,A(t)||"",n,r,a)},e.fn.error=function(){var e=Array.prototype.slice.call(arguments,0);return r("jQuery.fn.error() is deprecated"),e.splice(0,0,"error"),arguments.length?this.bind.apply(this,e):(this.triggerHandler.apply(this,e),this)},e.fn.toggle=function(t,n){if(!e.isFunction(t)||!e.isFunction(n))return N.apply(this,arguments);r("jQuery.fn.toggle(handler, handler...) is deprecated");var a=arguments,i=t.guid||e.guid++,o=0,s=function(n){var r=(e._data(this,"lastToggle"+t.guid)||0)%o;return e._data(this,"lastToggle"+t.guid,r+1),n.preventDefault(),a[r].apply(this,arguments)||!1};for(s.guid=i;a.length>o;)a[o++].guid=i;return this.click(s)},e.fn.live=function(t,n,a){return r("jQuery.fn.live() is deprecated"),T?T.apply(this,arguments):(e(this.context).on(t,this.selector,n,a),this)},e.fn.die=function(t,n){return r("jQuery.fn.die() is deprecated"),M?M.apply(this,arguments):(e(this.context).off(t,this.selector||"**",n),this)},e.event.trigger=function(e,t,n,a){return n||C.test(e)||r("Global events are undocumented and deprecated"),k.call(this,e,t,n||document,a)},e.each(S.split("|"),function(t,n){e.event.special[n]={setup:function(){var t=this;return t!==document&&(e.event.add(document,n+"."+e.guid,function(){e.event.trigger(n,null,t,!0)}),e._data(this,n,e.guid++)),!1},teardown:function(){return this!==document&&e.event.remove(document,n+"."+e._data(this,n)),!1}}})}(jQuery,window);

(function(e,t){function i(t,i){var a,n,r,o=t.nodeName.toLowerCase();return"area"===o?(a=t.parentNode,n=a.name,t.href&&n&&"map"===a.nodeName.toLowerCase()?(r=e("img[usemap=#"+n+"]")[0],!!r&&s(r)):!1):(/input|select|textarea|button|object/.test(o)?!t.disabled:"a"===o?t.href||i:i)&&s(t)}function s(t){return e.expr.filters.visible(t)&&!e(t).parents().addBack().filter(function(){return"hidden"===e.css(this,"visibility")}).length}var a=0,n=/^ui-id-\d+$/;e.ui=e.ui||{},e.extend(e.ui,{version:"1.10.3",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),e.fn.extend({focus:function(t){return function(i,s){return"number"==typeof i?this.each(function(){var t=this;setTimeout(function(){e(t).focus(),s&&s.call(t)},i)}):t.apply(this,arguments)}}(e.fn.focus),scrollParent:function(){var t;return t=e.ui.ie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?this.parents().filter(function(){return/(relative|absolute|fixed)/.test(e.css(this,"position"))&&/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0):this.parents().filter(function(){return/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0),/fixed/.test(this.css("position"))||!t.length?e(document):t},zIndex:function(i){if(i!==t)return this.css("zIndex",i);if(this.length)for(var s,a,n=e(this[0]);n.length&&n[0]!==document;){if(s=n.css("position"),("absolute"===s||"relative"===s||"fixed"===s)&&(a=parseInt(n.css("zIndex"),10),!isNaN(a)&&0!==a))return a;n=n.parent()}return 0},uniqueId:function(){return this.each(function(){this.id||(this.id="ui-id-"+ ++a)})},removeUniqueId:function(){return this.each(function(){n.test(this.id)&&e(this).removeAttr("id")})}}),e.extend(e.expr[":"],{data:e.expr.createPseudo?e.expr.createPseudo(function(t){return function(i){return!!e.data(i,t)}}):function(t,i,s){return!!e.data(t,s[3])},focusable:function(t){return i(t,!isNaN(e.attr(t,"tabindex")))},tabbable:function(t){var s=e.attr(t,"tabindex"),a=isNaN(s);return(a||s>=0)&&i(t,!a)}}),e("<a>").outerWidth(1).jquery||e.each(["Width","Height"],function(i,s){function a(t,i,s,a){return e.each(n,function(){i-=parseFloat(e.css(t,"padding"+this))||0,s&&(i-=parseFloat(e.css(t,"border"+this+"Width"))||0),a&&(i-=parseFloat(e.css(t,"margin"+this))||0)}),i}var n="Width"===s?["Left","Right"]:["Top","Bottom"],r=s.toLowerCase(),o={innerWidth:e.fn.innerWidth,innerHeight:e.fn.innerHeight,outerWidth:e.fn.outerWidth,outerHeight:e.fn.outerHeight};e.fn["inner"+s]=function(i){return i===t?o["inner"+s].call(this):this.each(function(){e(this).css(r,a(this,i)+"px")})},e.fn["outer"+s]=function(t,i){return"number"!=typeof t?o["outer"+s].call(this,t):this.each(function(){e(this).css(r,a(this,t,!0,i)+"px")})}}),e.fn.addBack||(e.fn.addBack=function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}),e("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(e.fn.removeData=function(t){return function(i){return arguments.length?t.call(this,e.camelCase(i)):t.call(this)}}(e.fn.removeData)),e.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()),e.support.selectstart="onselectstart"in document.createElement("div"),e.fn.extend({disableSelection:function(){return this.bind((e.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(e){e.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),e.extend(e.ui,{plugin:{add:function(t,i,s){var a,n=e.ui[t].prototype;for(a in s)n.plugins[a]=n.plugins[a]||[],n.plugins[a].push([i,s[a]])},call:function(e,t,i){var s,a=e.plugins[t];if(a&&e.element[0].parentNode&&11!==e.element[0].parentNode.nodeType)for(s=0;a.length>s;s++)e.options[a[s][0]]&&a[s][1].apply(e.element,i)}},hasScroll:function(t,i){if("hidden"===e(t).css("overflow"))return!1;var s=i&&"left"===i?"scrollLeft":"scrollTop",a=!1;return t[s]>0?!0:(t[s]=1,a=t[s]>0,t[s]=0,a)}})})(jQuery);

(function(){var t,e,s,i,r,o={}.hasOwnProperty,l=function(t,e){function s(){this.constructor=t}for(var i in e)o.call(e,i)&&(t[i]=e[i]);return s.prototype=e.prototype,t.prototype=new s,t.__super__=e.prototype,t};i=function(){function t(){this.options_index=0,this.parsed=[]}return t.prototype.add_node=function(t){return"OPTGROUP"===t.nodeName.toUpperCase()?this.add_group(t):this.add_option(t)},t.prototype.add_group=function(t){var e,s,i,r,o,l;for(e=this.parsed.length,this.parsed.push({array_index:e,group:!0,label:this.escapeExpression(t.label),children:0,disabled:t.disabled}),o=t.childNodes,l=[],i=0,r=o.length;r>i;i++)s=o[i],l.push(this.add_option(s,e,t.disabled));return l},t.prototype.add_option=function(t,e,s){return"OPTION"===t.nodeName.toUpperCase()?(""!==t.text?(null!=e&&(this.parsed[e].children+=1),this.parsed.push({array_index:this.parsed.length,options_index:this.options_index,value:t.value,text:t.text,html:t.innerHTML,selected:t.selected,disabled:s===!0?s:t.disabled,group_array_index:e,classes:t.className,style:t.style.cssText})):this.parsed.push({array_index:this.parsed.length,options_index:this.options_index,empty:!0}),this.options_index+=1):void 0},t.prototype.escapeExpression=function(t){var e,s;return null==t||t===!1?"":/[\&\<\>\"\'\`]/.test(t)?(e={"<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},s=/&(?!\w+;)|[\<\>\"\'\`]/g,t.replace(s,function(t){return e[t]||"&amp;"})):t},t}(),i.select_to_array=function(t){var e,s,r,o,l;for(s=new i,l=t.childNodes,r=0,o=l.length;o>r;r++)e=l[r],s.add_node(e);return s.parsed},e=function(){function t(e,s){this.form_field=e,this.options=null!=s?s:{},t.browser_is_supported()&&(this.is_multiple=this.form_field.multiple,this.set_default_text(),this.set_default_values(),this.setup(),this.set_up_html(),this.register_observers())}return t.prototype.set_default_values=function(){var t=this;return this.click_test_action=function(e){return t.test_active_click(e)},this.activate_action=function(e){return t.activate_field(e)},this.active_field=!1,this.mouse_on_container=!1,this.results_showing=!1,this.result_highlighted=null,this.result_single_selected=null,this.allow_single_deselect=null!=this.options.allow_single_deselect&&null!=this.form_field.options[0]&&""===this.form_field.options[0].text?this.options.allow_single_deselect:!1,this.disable_search_threshold=this.options.disable_search_threshold||0,this.disable_search=this.options.disable_search||!1,this.enable_split_word_search=null!=this.options.enable_split_word_search?this.options.enable_split_word_search:!0,this.group_search=null!=this.options.group_search?this.options.group_search:!0,this.search_contains=this.options.search_contains||!1,this.single_backstroke_delete=null!=this.options.single_backstroke_delete?this.options.single_backstroke_delete:!0,this.max_selected_options=this.options.max_selected_options||1/0,this.inherit_select_classes=this.options.inherit_select_classes||!1,this.display_selected_options=null!=this.options.display_selected_options?this.options.display_selected_options:!0,this.display_disabled_options=null!=this.options.display_disabled_options?this.options.display_disabled_options:!0},t.prototype.set_default_text=function(){return this.default_text=this.form_field.getAttribute("data-placeholder")?this.form_field.getAttribute("data-placeholder"):this.is_multiple?this.options.placeholder_text_multiple||this.options.placeholder_text||t.default_multiple_text:this.options.placeholder_text_single||this.options.placeholder_text||t.default_single_text,this.results_none_found=this.form_field.getAttribute("data-no_results_text")||this.options.no_results_text||t.default_no_result_text},t.prototype.mouse_enter=function(){return this.mouse_on_container=!0},t.prototype.mouse_leave=function(){return this.mouse_on_container=!1},t.prototype.input_focus=function(){var t=this;if(this.is_multiple){if(!this.active_field)return setTimeout(function(){return t.container_mousedown()},50)}else if(!this.active_field)return this.activate_field()},t.prototype.input_blur=function(){var t=this;return this.mouse_on_container?void 0:(this.active_field=!1,setTimeout(function(){return t.blur_test()},100))},t.prototype.results_option_build=function(t){var e,s,i,r,o;for(e="",o=this.results_data,i=0,r=o.length;r>i;i++)s=o[i],e+=s.group?this.result_add_group(s):this.result_add_option(s),(null!=t?t.first:void 0)&&(s.selected&&this.is_multiple?this.choice_build(s):s.selected&&!this.is_multiple&&this.single_set_selected_text(s.text));return e},t.prototype.result_add_option=function(t){var e,s;return t.search_match&&this.include_option_in_results(t)?(e=[],t.disabled||t.selected&&this.is_multiple||e.push("active-result"),!t.disabled||t.selected&&this.is_multiple||e.push("disabled-result"),t.selected&&e.push("result-selected"),null!=t.group_array_index&&e.push("group-option"),""!==t.classes&&e.push(t.classes),s=""!==t.style.cssText?' style="'+t.style+'"':"",'<li class="'+e.join(" ")+'"'+s+' data-option-array-index="'+t.array_index+'">'+t.search_text+"</li>"):""},t.prototype.result_add_group=function(t){return(t.search_match||t.group_match)&&t.active_options>0?'<li class="group-result">'+t.search_text+"</li>":""},t.prototype.results_update_field=function(){return this.set_default_text(),this.is_multiple||this.results_reset_cleanup(),this.result_clear_highlight(),this.result_single_selected=null,this.results_build(),this.results_showing?this.winnow_results():void 0},t.prototype.results_toggle=function(){return this.results_showing?this.results_hide():this.results_show()},t.prototype.results_search=function(){return this.results_showing?this.winnow_results():this.results_show()},t.prototype.winnow_results=function(){var t,e,s,i,r,o,l,n,h,c,a,_,u;for(this.no_results_clear(),r=0,l=this.get_search_text(),t=l.replace(/[-[\]{}()*+?.,\\^$|#\s]/g,"\\$&"),i=this.search_contains?"":"^",s=new RegExp(i+t,"i"),c=new RegExp(t,"i"),u=this.results_data,a=0,_=u.length;_>a;a++)e=u[a],e.search_match=!1,o=null,this.include_option_in_results(e)&&(e.group&&(e.group_match=!1,e.active_options=0),null!=e.group_array_index&&this.results_data[e.group_array_index]&&(o=this.results_data[e.group_array_index],0===o.active_options&&o.search_match&&(r+=1),o.active_options+=1),(!e.group||this.group_search)&&(e.search_text=e.group?e.label:e.html,e.search_match=this.search_string_match(e.search_text,s),e.search_match&&!e.group&&(r+=1),e.search_match?(l.length&&(n=e.search_text.search(c),h=e.search_text.substr(0,n+l.length)+"</em>"+e.search_text.substr(n+l.length),e.search_text=h.substr(0,n)+"<em>"+h.substr(n)),null!=o&&(o.group_match=!0)):null!=e.group_array_index&&this.results_data[e.group_array_index].search_match&&(e.search_match=!0)));return this.result_clear_highlight(),1>r&&l.length?(this.update_results_content(""),this.no_results(l)):(this.update_results_content(this.results_option_build()),this.winnow_results_set_highlight())},t.prototype.search_string_match=function(t,e){var s,i,r,o;if(e.test(t))return!0;if(this.enable_split_word_search&&(t.indexOf(" ")>=0||0===t.indexOf("["))&&(i=t.replace(/\[|\]/g,"").split(" "),i.length))for(r=0,o=i.length;o>r;r++)if(s=i[r],e.test(s))return!0},t.prototype.choices_count=function(){var t,e,s,i;if(null!=this.selected_option_count)return this.selected_option_count;for(this.selected_option_count=0,i=this.form_field.options,e=0,s=i.length;s>e;e++)t=i[e],t.selected&&(this.selected_option_count+=1);return this.selected_option_count},t.prototype.choices_click=function(t){return t.preventDefault(),this.results_showing||this.is_disabled?void 0:this.results_show()},t.prototype.keyup_checker=function(t){var e,s;switch(e=null!=(s=t.which)?s:t.keyCode,this.search_field_scale(),e){case 8:if(this.is_multiple&&this.backstroke_length<1&&this.choices_count()>0)return this.keydown_backstroke();if(!this.pending_backstroke)return this.result_clear_highlight(),this.results_search();break;case 13:if(t.preventDefault(),this.results_showing)return this.result_select(t);break;case 27:return this.results_showing&&this.results_hide(),!0;case 9:case 38:case 40:case 16:case 91:case 17:break;default:return this.results_search()}},t.prototype.container_width=function(){return null!=this.options.width?this.options.width:""+this.form_field.offsetWidth+"px"},t.prototype.include_option_in_results=function(t){return this.is_multiple&&!this.display_selected_options&&t.selected?!1:!this.display_disabled_options&&t.disabled?!1:t.empty?!1:!0},t.browser_is_supported=function(){return"Microsoft Internet Explorer"===window.navigator.appName?document.documentMode>=8:/iP(od|hone)/i.test(window.navigator.userAgent)?!1:/Android/i.test(window.navigator.userAgent)&&/Mobile/i.test(window.navigator.userAgent)?!1:!0},t.default_multiple_text="Select Some Options",t.default_single_text="Select an Option",t.default_no_result_text="No results match",t}(),t=jQuery,t.fn.extend({chosen:function(i){return e.browser_is_supported()?this.each(function(){var e,r;e=t(this),r=e.data("chosen"),"destroy"===i&&r?r.destroy():r||e.data("chosen",new s(this,i))}):this}}),s=function(e){function s(){return r=s.__super__.constructor.apply(this,arguments)}return l(s,e),s.prototype.setup=function(){return this.form_field_jq=t(this.form_field),this.current_selectedIndex=this.form_field.selectedIndex,this.is_rtl=this.form_field_jq.hasClass("chosen-rtl")},s.prototype.set_up_html=function(){var e,s;return e=["chosen-container"],e.push("chosen-container-"+(this.is_multiple?"multi":"single")),this.inherit_select_classes&&this.form_field.className&&e.push(this.form_field.className),this.is_rtl&&e.push("chosen-rtl"),s={"class":e.join(" "),style:"width: "+this.container_width()+";",title:this.form_field.title},this.form_field.id.length&&(s.id=this.form_field.id.replace(/[^\w]/g,"_")+"_chosen"),this.container=t("<div />",s),this.container.html(this.is_multiple?'<ul class="chosen-choices"><li class="search-field"><input type="text" value="'+this.default_text+'" class="default" autocomplete="off" style="width:25px;" /></li></ul><div class="chosen-drop"><ul class="chosen-results"></ul></div>':'<a class="chosen-single chosen-default" tabindex="-1"><span>'+this.default_text+'</span><div><b></b></div></a><div class="chosen-drop"><div class="chosen-search"><input type="text" autocomplete="off" /></div><ul class="chosen-results"></ul></div>'),this.form_field_jq.hide().after(this.container),this.dropdown=this.container.find("div.chosen-drop").first(),this.search_field=this.container.find("input").first(),this.search_results=this.container.find("ul.chosen-results").first(),this.search_field_scale(),this.search_no_results=this.container.find("li.no-results").first(),this.is_multiple?(this.search_choices=this.container.find("ul.chosen-choices").first(),this.search_container=this.container.find("li.search-field").first()):(this.search_container=this.container.find("div.chosen-search").first(),this.selected_item=this.container.find(".chosen-single").first()),this.results_build(),this.set_tab_index(),this.set_label_behavior(),this.form_field_jq.trigger("chosen:ready",{chosen:this})},s.prototype.register_observers=function(){var t=this;return this.container.bind("mousedown.chosen",function(e){t.container_mousedown(e)}),this.container.bind("mouseup.chosen",function(e){t.container_mouseup(e)}),this.container.bind("mouseenter.chosen",function(e){t.mouse_enter(e)}),this.container.bind("mouseleave.chosen",function(e){t.mouse_leave(e)}),this.search_results.bind("mouseup.chosen",function(e){t.search_results_mouseup(e)}),this.search_results.bind("mouseover.chosen",function(e){t.search_results_mouseover(e)}),this.search_results.bind("mouseout.chosen",function(e){t.search_results_mouseout(e)}),this.search_results.bind("mousewheel.chosen DOMMouseScroll.chosen",function(e){t.search_results_mousewheel(e)}),this.form_field_jq.bind("chosen:updated.chosen",function(e){t.results_update_field(e)}),this.form_field_jq.bind("chosen:activate.chosen",function(e){t.activate_field(e)}),this.form_field_jq.bind("chosen:open.chosen",function(e){t.container_mousedown(e)}),this.search_field.bind("blur.chosen",function(e){t.input_blur(e)}),this.search_field.bind("keyup.chosen",function(e){t.keyup_checker(e)}),this.search_field.bind("keydown.chosen",function(e){t.keydown_checker(e)}),this.search_field.bind("focus.chosen",function(e){t.input_focus(e)}),this.is_multiple?this.search_choices.bind("click.chosen",function(e){t.choices_click(e)}):this.container.bind("click.chosen",function(t){t.preventDefault()})},s.prototype.destroy=function(){return t(document).unbind("click.chosen",this.click_test_action),this.search_field[0].tabIndex&&(this.form_field_jq[0].tabIndex=this.search_field[0].tabIndex),this.container.remove(),this.form_field_jq.removeData("chosen"),this.form_field_jq.show()},s.prototype.search_field_disabled=function(){return this.is_disabled=this.form_field_jq[0].disabled,this.is_disabled?(this.container.addClass("chosen-disabled"),this.search_field[0].disabled=!0,this.is_multiple||this.selected_item.unbind("focus.chosen",this.activate_action),this.close_field()):(this.container.removeClass("chosen-disabled"),this.search_field[0].disabled=!1,this.is_multiple?void 0:this.selected_item.bind("focus.chosen",this.activate_action))},s.prototype.container_mousedown=function(e){return this.is_disabled||(e&&"mousedown"===e.type&&!this.results_showing&&e.preventDefault(),null!=e&&t(e.target).hasClass("search-choice-close"))?void 0:(this.active_field?this.is_multiple||!e||t(e.target)[0]!==this.selected_item[0]&&!t(e.target).parents("a.chosen-single").length||(e.preventDefault(),this.results_toggle()):(this.is_multiple&&this.search_field.val(""),t(document).bind("click.chosen",this.click_test_action),this.results_show()),this.activate_field())},s.prototype.container_mouseup=function(t){return"ABBR"!==t.target.nodeName||this.is_disabled?void 0:this.results_reset(t)},s.prototype.search_results_mousewheel=function(t){var e,s,i;return e=-(null!=(s=t.originalEvent)?s.wheelDelta:void 0)||(null!=(i=t.originialEvent)?i.detail:void 0),null!=e?(t.preventDefault(),"DOMMouseScroll"===t.type&&(e=40*e),this.search_results.scrollTop(e+this.search_results.scrollTop())):void 0},s.prototype.blur_test=function(){return!this.active_field&&this.container.hasClass("chosen-container-active")?this.close_field():void 0},s.prototype.close_field=function(){return t(document).unbind("click.chosen",this.click_test_action),this.active_field=!1,this.results_hide(),this.container.removeClass("chosen-container-active"),this.clear_backstroke(),this.show_search_field_default(),this.search_field_scale()},s.prototype.activate_field=function(){return this.container.addClass("chosen-container-active"),this.active_field=!0,this.search_field.val(this.search_field.val()),this.search_field.focus()},s.prototype.test_active_click=function(e){return this.container.is(t(e.target).closest(".chosen-container"))?this.active_field=!0:this.close_field()},s.prototype.results_build=function(){return this.parsing=!0,this.selected_option_count=null,this.results_data=i.select_to_array(this.form_field),this.is_multiple?this.search_choices.find("li.search-choice").remove():this.is_multiple||(this.single_set_selected_text(),this.disable_search||this.form_field.options.length<=this.disable_search_threshold?(this.search_field[0].readOnly=!0,this.container.addClass("chosen-container-single-nosearch")):(this.search_field[0].readOnly=!1,this.container.removeClass("chosen-container-single-nosearch"))),this.update_results_content(this.results_option_build({first:!0})),this.search_field_disabled(),this.show_search_field_default(),this.search_field_scale(),this.parsing=!1},s.prototype.result_do_highlight=function(t){var e,s,i,r,o;if(t.length){if(this.result_clear_highlight(),this.result_highlight=t,this.result_highlight.addClass("highlighted"),i=parseInt(this.search_results.css("maxHeight"),10),o=this.search_results.scrollTop(),r=i+o,s=this.result_highlight.position().top+this.search_results.scrollTop(),e=s+this.result_highlight.outerHeight(),e>=r)return this.search_results.scrollTop(e-i>0?e-i:0);if(o>s)return this.search_results.scrollTop(s)}},s.prototype.result_clear_highlight=function(){return this.result_highlight&&this.result_highlight.removeClass("highlighted"),this.result_highlight=null},s.prototype.results_show=function(){return this.is_multiple&&this.max_selected_options<=this.choices_count()?(this.form_field_jq.trigger("chosen:maxselected",{chosen:this}),!1):(this.container.addClass("chosen-with-drop"),this.form_field_jq.trigger("chosen:showing_dropdown",{chosen:this}),this.results_showing=!0,this.search_field.focus(),this.search_field.val(this.search_field.val()),this.winnow_results())},s.prototype.update_results_content=function(t){return this.search_results.html(t)},s.prototype.results_hide=function(){return this.results_showing&&(this.result_clear_highlight(),this.container.removeClass("chosen-with-drop"),this.form_field_jq.trigger("chosen:hiding_dropdown",{chosen:this})),this.results_showing=!1},s.prototype.set_tab_index=function(){var t;return this.form_field.tabIndex?(t=this.form_field.tabIndex,this.form_field.tabIndex=-1,this.search_field[0].tabIndex=t):void 0},s.prototype.set_label_behavior=function(){var e=this;return this.form_field_label=this.form_field_jq.parents("label"),!this.form_field_label.length&&this.form_field.id.length&&(this.form_field_label=t("label[for='"+this.form_field.id+"']")),this.form_field_label.length>0?this.form_field_label.bind("click.chosen",function(t){return e.is_multiple?e.container_mousedown(t):e.activate_field()}):void 0},s.prototype.show_search_field_default=function(){return this.is_multiple&&this.choices_count()<1&&!this.active_field?(this.search_field.val(this.default_text),this.search_field.addClass("default")):(this.search_field.val(""),this.search_field.removeClass("default"))},s.prototype.search_results_mouseup=function(e){var s;return s=t(e.target).hasClass("active-result")?t(e.target):t(e.target).parents(".active-result").first(),s.length?(this.result_highlight=s,this.result_select(e),this.search_field.focus()):void 0},s.prototype.search_results_mouseover=function(e){var s;return s=t(e.target).hasClass("active-result")?t(e.target):t(e.target).parents(".active-result").first(),s?this.result_do_highlight(s):void 0},s.prototype.search_results_mouseout=function(e){return t(e.target).hasClass("active-result")?this.result_clear_highlight():void 0},s.prototype.choice_build=function(e){var s,i,r=this;return s=t("<li />",{"class":"search-choice"}).html("<span>"+e.html+"</span>"),e.disabled?s.addClass("search-choice-disabled"):(i=t("<a />",{"class":"search-choice-close","data-option-array-index":e.array_index}),i.bind("click.chosen",function(t){return r.choice_destroy_link_click(t)}),s.append(i)),this.search_container.before(s)},s.prototype.choice_destroy_link_click=function(e){return e.preventDefault(),e.stopPropagation(),this.is_disabled?void 0:this.choice_destroy(t(e.target))},s.prototype.choice_destroy=function(t){return this.result_deselect(t[0].getAttribute("data-option-array-index"))?(this.show_search_field_default(),this.is_multiple&&this.choices_count()>0&&this.search_field.val().length<1&&this.results_hide(),t.parents("li").first().remove(),this.search_field_scale()):void 0},s.prototype.results_reset=function(){return this.form_field.options[0].selected=!0,this.selected_option_count=null,this.single_set_selected_text(),this.show_search_field_default(),this.results_reset_cleanup(),this.form_field_jq.trigger("change"),this.active_field?this.results_hide():void 0},s.prototype.results_reset_cleanup=function(){return this.current_selectedIndex=this.form_field.selectedIndex,this.selected_item.find("abbr").remove()},s.prototype.result_select=function(t){var e,s,i;return this.result_highlight?(e=this.result_highlight,this.result_clear_highlight(),this.is_multiple&&this.max_selected_options<=this.choices_count()?(this.form_field_jq.trigger("chosen:maxselected",{chosen:this}),!1):(this.is_multiple?e.removeClass("active-result"):(this.result_single_selected&&(this.result_single_selected.removeClass("result-selected"),i=this.result_single_selected[0].getAttribute("data-option-array-index"),this.results_data[i].selected=!1),this.result_single_selected=e),e.addClass("result-selected"),s=this.results_data[e[0].getAttribute("data-option-array-index")],s.selected=!0,this.form_field.options[s.options_index].selected=!0,this.selected_option_count=null,this.is_multiple?this.choice_build(s):this.single_set_selected_text(s.text),(t.metaKey||t.ctrlKey)&&this.is_multiple||this.results_hide(),this.search_field.val(""),(this.is_multiple||this.form_field.selectedIndex!==this.current_selectedIndex)&&this.form_field_jq.trigger("change",{selected:this.form_field.options[s.options_index].value}),this.current_selectedIndex=this.form_field.selectedIndex,this.search_field_scale())):void 0},s.prototype.single_set_selected_text=function(t){return null==t&&(t=this.default_text),t===this.default_text?this.selected_item.addClass("chosen-default"):(this.single_deselect_control_build(),this.selected_item.removeClass("chosen-default")),this.selected_item.find("span").text(t)},s.prototype.result_deselect=function(t){var e;return e=this.results_data[t],this.form_field.options[e.options_index].disabled?!1:(e.selected=!1,this.form_field.options[e.options_index].selected=!1,this.selected_option_count=null,this.result_clear_highlight(),this.results_showing&&this.winnow_results(),this.form_field_jq.trigger("change",{deselected:this.form_field.options[e.options_index].value}),this.search_field_scale(),!0)},s.prototype.single_deselect_control_build=function(){return this.allow_single_deselect?(this.selected_item.find("abbr").length||this.selected_item.find("span").first().after('<abbr class="search-choice-close"></abbr>'),this.selected_item.addClass("chosen-single-with-deselect")):void 0},s.prototype.get_search_text=function(){return this.search_field.val()===this.default_text?"":t("<div/>").text(t.trim(this.search_field.val())).html()},s.prototype.winnow_results_set_highlight=function(){var t,e;return e=this.is_multiple?[]:this.search_results.find(".result-selected.active-result"),t=e.length?e.first():this.search_results.find(".active-result").first(),null!=t?this.result_do_highlight(t):void 0},s.prototype.no_results=function(e){var s;return s=t('<li class="no-results">'+this.results_none_found+' "<span></span>"</li>'),s.find("span").first().html(e),this.search_results.append(s)},s.prototype.no_results_clear=function(){return this.search_results.find(".no-results").remove()},s.prototype.keydown_arrow=function(){var t;return this.results_showing&&this.result_highlight?(t=this.result_highlight.nextAll("li.active-result").first())?this.result_do_highlight(t):void 0:this.results_show()},s.prototype.keyup_arrow=function(){var t;return this.results_showing||this.is_multiple?this.result_highlight?(t=this.result_highlight.prevAll("li.active-result"),t.length?this.result_do_highlight(t.first()):(this.choices_count()>0&&this.results_hide(),this.result_clear_highlight())):void 0:this.results_show()},s.prototype.keydown_backstroke=function(){var t;return this.pending_backstroke?(this.choice_destroy(this.pending_backstroke.find("a").first()),this.clear_backstroke()):(t=this.search_container.siblings("li.search-choice").last(),t.length&&!t.hasClass("search-choice-disabled")?(this.pending_backstroke=t,this.single_backstroke_delete?this.keydown_backstroke():this.pending_backstroke.addClass("search-choice-focus")):void 0)},s.prototype.clear_backstroke=function(){return this.pending_backstroke&&this.pending_backstroke.removeClass("search-choice-focus"),this.pending_backstroke=null},s.prototype.keydown_checker=function(t){var e,s;switch(e=null!=(s=t.which)?s:t.keyCode,this.search_field_scale(),8!==e&&this.pending_backstroke&&this.clear_backstroke(),e){case 8:this.backstroke_length=this.search_field.val().length;break;case 9:this.results_showing&&!this.is_multiple&&this.result_select(t),this.mouse_on_container=!1;break;case 13:t.preventDefault();break;case 38:t.preventDefault(),this.keyup_arrow();break;case 40:t.preventDefault(),this.keydown_arrow()}},s.prototype.search_field_scale=function(){var e,s,i,r,o,l,n,h,c;if(this.is_multiple){for(i=0,n=0,o="position:absolute; left: -1000px; top: -1000px; display:none;",l=["font-size","font-style","font-weight","font-family","line-height","text-transform","letter-spacing"],h=0,c=l.length;c>h;h++)r=l[h],o+=r+":"+this.search_field.css(r)+";";return e=t("<div />",{style:o}),e.text(this.search_field.val()),t("body").append(e),n=e.width()+25,e.remove(),s=this.container.outerWidth(),n>s-10&&(n=s-10),this.search_field.css({width:n+"px"})}},s}(e)}).call(this);




+function(o){"use strict";function t(t){t&&3===t.which||(o(n).remove(),o(r).each(function(){var n=e(o(this)),r={relatedTarget:this};n.hasClass("open")&&(n.trigger(t=o.Event("hide.bs.dropdown",r)),t.isDefaultPrevented()||n.removeClass("open").trigger("hidden.bs.dropdown",r))}))}function e(t){var e=t.attr("data-target");e||(e=t.attr("href"),e=e&&/#[A-Za-z]/.test(e)&&e.replace(/.*(?=#[^\s]*$)/,""));var n=e&&o(e);return n&&n.length?n:t.parent()}var n=".dropdown-backdrop",r='[data-toggle="dropdown"]',d=function(t){o(t).on("click.bs.dropdown",this.toggle)};d.prototype.toggle=function(n){var r=o(this);if(!r.is(".disabled, :disabled")){var d=e(r),i=d.hasClass("open");if(t(),!i){"ontouchstart"in document.documentElement&&!d.closest(".navbar-nav").length&&o('<div class="dropdown-backdrop"/>').insertAfter(o(this)).on("click",t);var a={relatedTarget:this};if(d.trigger(n=o.Event("show.bs.dropdown",a)),n.isDefaultPrevented())return;r.trigger("focus"),d.toggleClass("open").trigger("shown.bs.dropdown",a)}return!1}},d.prototype.keydown=function(t){if(/(38|40|27)/.test(t.keyCode)){var n=o(this);if(t.preventDefault(),t.stopPropagation(),!n.is(".disabled, :disabled")){var d=e(n),i=d.hasClass("open");if(!i||i&&27==t.keyCode)return 27==t.which&&d.find(r).trigger("focus"),n.trigger("click");var a=" li:not(.divider):visible a",s=d.find('[role="menu"]'+a+', [role="listbox"]'+a);if(s.length){var p=s.index(s.filter(":focus"));38==t.keyCode&&p>0&&p--,40==t.keyCode&&p<s.length-1&&p++,~p||(p=0),s.eq(p).trigger("focus")}}}};var i=o.fn.dropdown;o.fn.dropdown=function(t){return this.each(function(){var e=o(this),n=e.data("bs.dropdown");n||e.data("bs.dropdown",n=new d(this)),"string"==typeof t&&n[t].call(e)})},o.fn.dropdown.Constructor=d,o.fn.dropdown.noConflict=function(){return o.fn.dropdown=i,this},o(document).on("click.bs.dropdown.data-api",t).on("click.bs.dropdown.data-api",".dropdown form",function(o){o.stopPropagation()}).on("click.bs.dropdown.data-api",r,d.prototype.toggle).on("keydown.bs.dropdown.data-api",r+', [role="menu"], [role="listbox"]',d.prototype.keydown)}(jQuery);

!function(e){var o=function(){var o=65,t='<div class="colorpicker"><div class="colorpicker_color"><div><div></div></div></div><div class="colorpicker_hue"><div></div></div><div class="colorpicker_new_color"></div><div class="colorpicker_current_color"></div><div class="colorpicker_hex"><input type="text" maxlength="6" size="6" /></div><div class="colorpicker_rgb_r colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_rgb_g colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_rgb_b colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_h colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_s colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_hsb_b colorpicker_field"><input type="text" maxlength="3" size="3" /><span></span></div><div class="colorpicker_submit"></div></div>',r={eventName:"click",onShow:function(){},onBeforeShow:function(){},onHide:function(){},onChange:function(){},onSubmit:function(){},color:"ff0000",livePreview:!0,flat:!1},i=function(o,t){var r=j(o);e(t).data("colorpicker").fields.eq(1).val(r.r).end().eq(2).val(r.g).end().eq(3).val(r.b).end()},a=function(o,t){e(t).data("colorpicker").fields.eq(4).val(o.h).end().eq(5).val(o.s).end().eq(6).val(o.b).end()},n=function(o,t){e(t).data("colorpicker").fields.eq(0).val(B(o)).end()},c=function(o,t){e(t).data("colorpicker").selector.css("backgroundColor","#"+B({h:o.h,s:100,b:100})),e(t).data("colorpicker").selectorIndic.css({left:parseInt(150*o.s/100,10),top:parseInt(150*(100-o.b)/100,10)})},d=function(o,t){e(t).data("colorpicker").hue.css("top",parseInt(150-150*o.h/360,10))},l=function(o,t){e(t).data("colorpicker").currentColor.css("backgroundColor","#"+B(o))},s=function(o,t){e(t).data("colorpicker").newColor.css("backgroundColor","#"+B(o))},p=function(t){var r=t.charCode||t.keyCode||-1;if(r>o&&90>=r||32==r)return!1;var i=e(this).parent().parent();i.data("colorpicker").livePreview===!0&&u.apply(this)},u=function(o){var t,r=e(this).parent().parent();r.data("colorpicker").color=t=this.parentNode.className.indexOf("_hex")>0?E(z(this.value)):this.parentNode.className.indexOf("_hsb")>0?H({h:parseInt(r.data("colorpicker").fields.eq(4).val(),10),s:parseInt(r.data("colorpicker").fields.eq(5).val(),10),b:parseInt(r.data("colorpicker").fields.eq(6).val(),10)}):T(O({r:parseInt(r.data("colorpicker").fields.eq(1).val(),10),g:parseInt(r.data("colorpicker").fields.eq(2).val(),10),b:parseInt(r.data("colorpicker").fields.eq(3).val(),10)})),o&&(i(t,r.get(0)),n(t,r.get(0)),a(t,r.get(0))),c(t,r.get(0)),d(t,r.get(0)),s(t,r.get(0)),r.data("colorpicker").onChange.apply(r,[t,B(t),j(t)])},f=function(){var o=e(this).parent().parent();o.data("colorpicker").fields.parent().removeClass("colorpicker_focus")},h=function(){o=this.parentNode.className.indexOf("_hex")>0?70:65,e(this).parent().parent().data("colorpicker").fields.parent().removeClass("colorpicker_focus"),e(this).parent().addClass("colorpicker_focus")},v=function(o){var t=e(this).parent().find("input").focus(),r={el:e(this).parent().addClass("colorpicker_slider"),max:this.parentNode.className.indexOf("_hsb_h")>0?360:this.parentNode.className.indexOf("_hsb")>0?100:255,y:o.pageY,field:t,val:parseInt(t.val(),10),preview:e(this).parent().parent().data("colorpicker").livePreview};e(document).bind("mouseup",r,g),e(document).bind("mousemove",r,m)},m=function(e){return e.data.field.val(Math.max(0,Math.min(e.data.max,parseInt(e.data.val+e.pageY-e.data.y,10)))),e.data.preview&&u.apply(e.data.field.get(0),[!0]),!1},g=function(o){return u.apply(o.data.field.get(0),[!0]),o.data.el.removeClass("colorpicker_slider").find("input").focus(),e(document).unbind("mouseup",g),e(document).unbind("mousemove",m),!1},k=function(){var o={cal:e(this).parent(),y:e(this).offset().top};o.preview=o.cal.data("colorpicker").livePreview,e(document).bind("mouseup",o,_),e(document).bind("mousemove",o,b)},b=function(e){return u.apply(e.data.cal.data("colorpicker").fields.eq(4).val(parseInt(360*(150-Math.max(0,Math.min(150,e.pageY-e.data.y)))/150,10)).get(0),[e.data.preview]),!1},_=function(o){return i(o.data.cal.data("colorpicker").color,o.data.cal.get(0)),n(o.data.cal.data("colorpicker").color,o.data.cal.get(0)),e(document).unbind("mouseup",_),e(document).unbind("mousemove",b),!1},x=function(){var o={cal:e(this).parent(),pos:e(this).offset()};o.preview=o.cal.data("colorpicker").livePreview,e(document).bind("mouseup",o,y),e(document).bind("mousemove",o,w)},w=function(e){return u.apply(e.data.cal.data("colorpicker").fields.eq(6).val(parseInt(100*(150-Math.max(0,Math.min(150,e.pageY-e.data.pos.top)))/150,10)).end().eq(5).val(parseInt(100*Math.max(0,Math.min(150,e.pageX-e.data.pos.left))/150,10)).get(0),[e.data.preview]),!1},y=function(o){return i(o.data.cal.data("colorpicker").color,o.data.cal.get(0)),n(o.data.cal.data("colorpicker").color,o.data.cal.get(0)),e(document).unbind("mouseup",y),e(document).unbind("mousemove",w),!1},C=function(){e(this).addClass("colorpicker_focus")},M=function(){e(this).removeClass("colorpicker_focus")},I=function(){var o=e(this).parent(),t=o.data("colorpicker").color;o.data("colorpicker").origColor=t,l(t,o.get(0)),o.data("colorpicker").onSubmit(t,B(t),j(t),o.data("colorpicker").el)},q=function(){var o=e("#"+e(this).data("colorpickerId"));o.data("colorpicker").onBeforeShow.apply(this,[o.get(0)]);var t=e(this).offset(),r=S(),i=t.top+this.offsetHeight,a=t.left;return i+176>r.t+r.h&&(i-=this.offsetHeight+176),a+356>r.l+r.w&&(a-=356),o.css({left:a+"px",top:i+"px"}),0!=o.data("colorpicker").onShow.apply(this,[o.get(0)])&&o.show(),e(document).bind("mousedown",{cal:o},P),!1},P=function(o){N(o.data.cal.get(0),o.target,o.data.cal.get(0))||(0!=o.data.cal.data("colorpicker").onHide.apply(this,[o.data.cal.get(0)])&&o.data.cal.hide(),e(document).unbind("mousedown",P))},N=function(e,o,t){if(e==o)return!0;if(e.contains)return e.contains(o);if(e.compareDocumentPosition)return!!(16&e.compareDocumentPosition(o));for(var r=o.parentNode;r&&r!=t;){if(r==e)return!0;r=r.parentNode}return!1},S=function(){var e="CSS1Compat"==document.compatMode;return{l:window.pageXOffset||(e?document.documentElement.scrollLeft:document.body.scrollLeft),t:window.pageYOffset||(e?document.documentElement.scrollTop:document.body.scrollTop),w:window.innerWidth||(e?document.documentElement.clientWidth:document.body.clientWidth),h:window.innerHeight||(e?document.documentElement.clientHeight:document.body.clientHeight)}},H=function(e){return{h:Math.min(360,Math.max(0,e.h)),s:Math.min(100,Math.max(0,e.s)),b:Math.min(100,Math.max(0,e.b))}},O=function(e){return{r:Math.min(255,Math.max(0,e.r)),g:Math.min(255,Math.max(0,e.g)),b:Math.min(255,Math.max(0,e.b))}},z=function(e){var o=6-e.length;if(o>0){for(var t=[],r=0;o>r;r++)t.push("0");t.push(e),e=t.join("")}return e},Y=function(e){var e=parseInt(e.indexOf("#")>-1?e.substring(1):e,16);return{r:e>>16,g:(65280&e)>>8,b:255&e}},E=function(e){return T(Y(e))},T=function(e){var o={h:0,s:0,b:0},t=Math.min(e.r,e.g,e.b),r=Math.max(e.r,e.g,e.b),i=r-t;return o.b=r,o.s=0!=r?255*i/r:0,o.h=0!=o.s?e.r==r?(e.g-e.b)/i:e.g==r?2+(e.b-e.r)/i:4+(e.r-e.g)/i:-1,o.h*=60,o.h<0&&(o.h+=360),o.s*=100/255,o.b*=100/255,o},j=function(e){var o={},t=Math.round(e.h),r=Math.round(255*e.s/100),i=Math.round(255*e.b/100);if(0==r)o.r=o.g=o.b=i;else{var a=i,n=(255-r)*i/255,c=(a-n)*(t%60)/60;360==t&&(t=0),60>t?(o.r=a,o.b=n,o.g=n+c):120>t?(o.g=a,o.b=n,o.r=a-c):180>t?(o.g=a,o.r=n,o.b=n+c):240>t?(o.b=a,o.r=n,o.g=a-c):300>t?(o.b=a,o.g=n,o.r=n+c):360>t?(o.r=a,o.g=n,o.b=a-c):(o.r=0,o.g=0,o.b=0)}return{r:Math.round(o.r),g:Math.round(o.g),b:Math.round(o.b)}},W=function(o){var t=[o.r.toString(16),o.g.toString(16),o.b.toString(16)];return e.each(t,function(e,o){1==o.length&&(t[e]="0"+o)}),t.join("")},B=function(e){return W(j(e))},D=function(){var o=e(this).parent(),t=o.data("colorpicker").origColor;o.data("colorpicker").color=t,i(t,o.get(0)),n(t,o.get(0)),a(t,o.get(0)),c(t,o.get(0)),d(t,o.get(0)),s(t,o.get(0))};return{init:function(o){if(o=e.extend({},r,o||{}),"string"==typeof o.color)o.color=E(o.color);else if(void 0!=o.color.r&&void 0!=o.color.g&&void 0!=o.color.b)o.color=T(o.color);else{if(void 0==o.color.h||void 0==o.color.s||void 0==o.color.b)return this;o.color=H(o.color)}return this.each(function(){if(!e(this).data("colorpickerId")){var r=e.extend({},o);r.origColor=o.color;var m="collorpicker_"+parseInt(1e3*Math.random());e(this).data("colorpickerId",m);var g=e(t).attr("id",m);r.flat?g.appendTo(this).show():g.appendTo(document.body),r.fields=g.find("input").bind("keyup",p).bind("change",u).bind("blur",f).bind("focus",h),g.find("span").bind("mousedown",v).end().find(">div.colorpicker_current_color").bind("click",D),r.selector=g.find("div.colorpicker_color").bind("mousedown",x),r.selectorIndic=r.selector.find("div div"),r.el=this,r.hue=g.find("div.colorpicker_hue div"),g.find("div.colorpicker_hue").bind("mousedown",k),r.newColor=g.find("div.colorpicker_new_color"),r.currentColor=g.find("div.colorpicker_current_color"),g.data("colorpicker",r),g.find("div.colorpicker_submit").bind("mouseenter",C).bind("mouseleave",M).bind("click",I),i(r.color,g.get(0)),a(r.color,g.get(0)),n(r.color,g.get(0)),d(r.color,g.get(0)),c(r.color,g.get(0)),l(r.color,g.get(0)),s(r.color,g.get(0)),r.flat?g.css({position:"relative",display:"block"}):e(this).bind(r.eventName,q)}})},showPicker:function(){return this.each(function(){e(this).data("colorpickerId")&&q.apply(this)})},hidePicker:function(){return this.each(function(){e(this).data("colorpickerId")&&e("#"+e(this).data("colorpickerId")).hide()})},setColor:function(o){if("string"==typeof o)o=E(o);else if(void 0!=o.r&&void 0!=o.g&&void 0!=o.b)o=T(o);else{if(void 0==o.h||void 0==o.s||void 0==o.b)return this;o=H(o)}return this.each(function(){if(e(this).data("colorpickerId")){var t=e("#"+e(this).data("colorpickerId"));t.data("colorpicker").color=o,t.data("colorpicker").origColor=o,i(o,t.get(0)),a(o,t.get(0)),n(o,t.get(0)),d(o,t.get(0)),c(o,t.get(0)),l(o,t.get(0)),s(o,t.get(0))}})}}}();e.fn.extend({ColorPicker:o.init,ColorPickerHide:o.hidePicker,ColorPickerShow:o.showPicker,ColorPickerSetColor:o.setColor})}(jQuery);

function display_message( msg){

	alert(msg);
}

function remove_product(e){


	$(e).parent().remove();
	return false;

}

 var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;',
    "/": '&#x2F;'
  };

function escapeHtml(string) {
    return String(string).replace(/[&<>"'\/]/g, function (s) {
      return entityMap[s];
    });
  }

function add_product_to_post( e, lang){
		var id_product = $(e).attr('href');
		var thumb = $(e).attr('thumb');
		var name = $(e).attr('name');	

		$('.select_products').val('');
		$('.selector_wrapper').html('');

		var html = '';
		html += '<div class="product_post" rel="'+id_product+'"  idlang="'+lang+'" style="display: inline-block;position: relative;background: #fff;cursor:move;box-shadow: 0 1px 2px rgba(0,0,0,0.2);margin-right: 15px; "><a href="'+id_product+'" lang="'+lang+'" class="acf-button-delete ir delete_product_ass" onclick="remove_product(this);return false;"></a><img src="//'+thumb+'"/><p style="position:absolute;background-color:#ccc;color:#fff;width:100%;padding:5px;display:block  ;bottom:0; margin-bottom:0px;">'+name+'</p></div>'

		$('.lang-'+lang+' .products_wrapper').append(html);
		return false;
}



function dropDown()
{
	elementClick = '#header .current';
	elementSlide =  'ul.toogle_content';
	activeClass = 'active';

	$(elementClick).on('click', function(e){
		e.stopPropagation();
		var subUl = $(this).next(elementSlide);
		if (subUl.is(':hidden'))
		{
			subUl.slideDown();
			$(this).addClass(activeClass);
		}
		else
		{
			subUl.slideUp();
			$(this).removeClass(activeClass);
		}
		$(elementClick).not(this).next(elementSlide).slideUp();
		$(elementClick).not(this).removeClass(activeClass);
		e.preventDefault();
	});

	$(elementSlide).on('click', function(e){
		e.stopPropagation();
	});

	$(document).on('click', function(e){
		e.stopPropagation();
		var elementHide = $(elementClick).next(elementSlide);
		$(elementHide).slideUp();
		$(elementClick).removeClass('active');
	});
}

function hideOtherLanguage(id)
{
	$('.translatable-field').hide();
	$('.lang-' + id).show();

	var id_old_language = id_language;
	id_language = id;

	if (id_old_language != id)
		changeEmployeeLanguage();

	updateCurrentText();
}

function updateCurrentText()
{
	$('#current_product').html($('#name_' + id_language).val());
}

function changeEmployeeLanguage()
{
	if (typeof allowEmployeeFormLang !== 'undefined' && allowEmployeeFormLang)
		$.post("index.php", {
			action: 'formLanguage', 
			tab: 'AdminEmployees',
			ajax: 1,
			token: employee_token,
			form_language_id: id_language
		});
}

function remove_tag(e){

	$(e).parent().remove();
}

$(document).ready(function(){

$('.order_close').click(function(){

			$('.order_overlay').fadeOut();
			$('.order_info').fadeOut();

			return false;
		});


	$('.show_order').click(function(){

		var rel = $(this).attr('rel');

		$.ajax({
			  type: "POST",
			  url: admin_ajax_url,
			  data: {
			  	action : 'show_order_info',
			  	order_id: rel,
			  	id_employee: id_employee
			  }
			})
			  .done(function( msg) {
			   
				$('#order_show_content').html(msg);
				$('.order_overlay').fadeIn();
				$('.order_info').fadeIn();
			 });
		return false;
	});


	$('.resolve_order').click(function(){

		var rel = $(this).attr('rel');

		$.ajax({
			  type: "POST",
			  url: admin_ajax_url,
			  data: {
			  	action : 'resolve_order',
			  	order_id: rel
			  }
			})
			  .done(function( msg) {
				location.reload();
			 });
		return false;
	});

	$('.unresolve_order').click(function(){

		var rel = $(this).attr('rel');

		$.ajax({
			  type: "POST",
			  url: admin_ajax_url,
			  data: {
			  	action : 'unresolve_order',
			  	order_id: rel
			  }
			})
			  .done(function( msg) {
					location.reload();
			 });
		return false;
	});

	$('.cancel_order').click(function(){

		var rel = $(this).attr('rel');

		$.ajax({
			  type: "POST",
			  url: admin_ajax_url,
			  data: {
			  	action : 'cancel_order',
			  	order_id: rel
			  }
			})
			  .done(function( msg) {
					location.reload();
			 });
		return false;
	});

	$('.delete_order').click(function(){

		var rel = $(this).attr('rel');

		var r = confirm("Are you sure you want to delete order No."+rel);
		if (r == true) {

			$.ajax({
				  type: "POST",
				  url: admin_ajax_url,
				  data: {
				  	action : 'delete_order',
				  	order_id: rel
				  }
				})
				  .done(function( msg) {
						location.reload();
				 });
		}




		return false;
	});


});