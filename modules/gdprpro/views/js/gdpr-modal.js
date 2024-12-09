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

if (typeof Object.assign != 'function') {
  // Must be writable: true, enumerable: false, configurable: true
  Object.defineProperty(Object, "assign", {
    value: function assign(target, varArgs) { // .length of function is 2
      'use strict';
      if (target == null) { // TypeError if undefined or null
        throw new TypeError('Cannot convert undefined or null to object');
      }

      var to = Object(target);

      for (var index = 1; index < arguments.length; index++) {
        var nextSource = arguments[index];

        if (nextSource != null) { // Skip over if undefined or null
          for (var nextKey in nextSource) {
            // Avoid bugs when hasOwnProperty is shadowed
            if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
              to[nextKey] = nextSource[nextKey];
            }
          }
        }
      }
      return to;
    },
    writable: true,
    configurable: true
  });
}


!function (e) {
    if ("object" == typeof exports && "undefined" != typeof module) module.exports = e(); else if ("function" == typeof define && define.amd) define([], e); else {
        ("undefined" != typeof window ? window : "undefined" != typeof global ? global : "undefined" != typeof self ? self : this).gdprModal = e()
    }
}(function () {
    return function e(t, o, n) {
        function r(f, a) {
            if (!o[f]) {
                if (!t[f]) {
                    var c = "function" == typeof require && require;
                    if (!a && c) return c(f, !0);
                    if (i) return i(f, !0);
                    var l = new Error("Cannot find module '" + f + "'");
                    throw l.code = "MODULE_NOT_FOUND", l
                }
                var u = o[f] = {exports: {}};
                t[f][0].call(u.exports, function (e) {
                    var o = t[f][1][e];
                    return r(o || e)
                }, u, u.exports, e, t, o, n)
            }
            return o[f].exports
        }

        for (var i = "function" == typeof require && require, f = 0; f < n.length; f++) r(n[f]);
        return r
    }({
        1: [function (e, t, o) {
            "use strict";
            Object.defineProperty(o, "__esModule", {value: !0});
            var n = function (e, t) {
                var o = e.children;
                return 1 === o.length && o[0].tagName === t
            }, r = o.visible = function (e) {
                return null != (e = e || document.querySelector(".gdprModal")) && !0 === e.ownerDocument.body.contains(e)
            };
            o.create = function (e, t) {
                var o = function () {
                    var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "", t = arguments[1],
                        o = document.createElement("div");
                    o.classList.add("gdprModal"), null != t.className && o.classList.add(t.className), o.innerHTML = "\n\t\t" + t.beforePlaceholder + '\n\t\t<div class="gdprModal__placeholder" role="dialog">\n\t\t\t' + e + "\n\t\t</div>\n\t\t" + t.afterPlaceholder + "\n\t";
                    var r = o.querySelector(".gdprModal__placeholder"), i = n(r, "IMG"), f = n(r, "VIDEO"),
                        a = n(r, "IFRAME");
                    return !0 === i && o.classList.add("gdprModal--img"), !0 === f && o.classList.add("gdprModal--video"), !0 === a && o.classList.add("gdprModal--iframe"), o
                }(e, t = function () {
                    var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {};
                    return !1 !== (e = Object.assign({}, e)).closable && (e.closable = !0), "function" == typeof e.className && (e.className = e.className()), "string" != typeof e.className && (e.className = null), "function" != typeof e.beforeShow && (e.beforeShow = function () {
                    }), "function" != typeof e.afterShow && (e.afterShow = function () {
                    }), "function" != typeof e.beforeClose && (e.beforeClose = function () {
                    }), "function" != typeof e.afterClose && (e.afterClose = function () {
                    }), "function" == typeof e.beforePlaceholder && (e.beforePlaceholder = e.beforePlaceholder()), "string" != typeof e.beforePlaceholder && (e.beforePlaceholder = ""), "function" == typeof e.afterPlaceholder && (e.afterPlaceholder = e.afterPlaceholder()), "string" != typeof e.afterPlaceholder && (e.afterPlaceholder = ""), e
                }(t)), i = function (e) {
                    return !1 !== t.beforeClose(f) && function (e, t) {
                        $('body').removeClass(gdprSettings.scrollLock ? 'overflow-hidden' : 'overflow-shown');
                        return e.classList.remove("gdprModal--visible"), setTimeout(function () {
                            requestAnimationFrame(function () {
                                return !1 === r(e) ? t() : (e.parentElement.removeChild(e), t())
                            })
                        }, 410), !0
                    }(o, function () {
                        if (t.afterClose(f), "function" == typeof e) return e(f)
                    })
                };
                !0 === t.closable && (o.onclick = function (e) {
                    e.target === this && (i(), function (e) {
                        "function" == typeof e.stopPropagation && e.stopPropagation(), "function" == typeof e.preventDefault && e.preventDefault()
                    }(e))
                });
                var f = {
                    element: function () {
                        return o
                    }, visible: function () {
                        return r(o)
                    }, show: function (e) {
                        $('body').addClass(gdprSettings.scrollLock ? 'overflow-hidden' : 'overflow-shown');
                        if($(window).width() < 650) {
                            $('.gdprModal').height($(document).height());
                        }
                        return !1 !== t.beforeShow(f) && function (e, t) {
                            return document.body.appendChild(e), setTimeout(function () {
                                requestAnimationFrame(function () {
                                    return e.classList.add("gdprModal--visible"), t()
                                })
                            }, 10), !0
                        }(o, function () {
                            if (t.afterShow(f), "function" == typeof e) return e(f)
                        })
                    }, init: function (e) {
                        return !1 !== t.beforeShow(f) && function (e, t) {
                            return document.body.appendChild(e)
                        }(o, function () {
                            if (t.afterShow(f), "function" == typeof e) return e(f)
                        })
                    },
                    close: i
                };
                return f
            }
        }, {}]
    }, {}, [1])(1)
});