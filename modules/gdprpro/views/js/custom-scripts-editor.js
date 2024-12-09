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
$(document).ready(function () {
    var inlineCss = CodeMirror.fromTextArea(document.getElementById("inline_css"), {
        lineNumbers: true,
        mode: "text/css",
        matchBrackets: true,
        theme: "material"
    });

    var inlineJs = CodeMirror.fromTextArea(document.getElementById("inline_js"), {
        lineNumbers: true,
        mode: "text/javascript",
        matchBrackets: true,
        theme: "material"
    });
    // editor.setOption("theme", 'material');
});
