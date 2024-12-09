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
/**
 * @return {boolean}
 */
function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

var jsonPrettyPrint = {
    replacer: function (match, pIndent, pKey, pVal, pEnd) {
        var key = '<span class=json-key>';
        var val = '<span class=json-value>';
        var str = '<span class=json-string>';
        var r = pIndent || '';
        if (pKey)
            r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
        if (pVal)
            r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
        return r + (pEnd || '');
    },

    toHtml: function (obj) {
        var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
        return "<pre>" + JSON.stringify(obj, null, 3)
            .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(jsonLine, jsonPrettyPrint.replacer) + "</pre>";
    }
};

var jsonPrettyPrintTable = {
    replacer: function (match, pIndent, pKey, pVal, pEnd) {
        var key = '<tr><td class=json-key>';
        var str = '<td class=json-string>';
        var r = pIndent || '';
        if (pKey)
            r = r + key + pKey.replace(/[": ]/g, '') + '</td>';
        if (pVal)
            r = r + (pVal[0] == '"' ? str : val) + pVal + '</td></tr>';
        return r + (pEnd || '');
    },

    toHtml: function (obj) {

        var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
        return  JSON.stringify(obj, null, 3)
            .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(jsonLine, jsonPrettyPrintTable.replacer);
    }
};

document.addEventListener("DOMContentLoaded", function (event) {
    $('#table-gdpr_activity_log td:nth-child(7)').each(function (index) {
        var content = $(this).html().trim();
        if (isJsonString(content)) {

            var table_start = '<table class=""><tr><th>Cookie name</th><th>Status</th></tr>';
            var table_end = '</table>';

            var jsonData = JSON.parse(content);
            var rows = '';

            var tr = '<tr><td class=json-key>';
            var tr_end = '<td class=json-string>';
            var check = '&#10004;';
            var cross = '&#10006;';



            for (var key in jsonData) {
                if (jsonData.hasOwnProperty(key)) {
                    if(jsonData[key] == 'true'){
                        rows = rows + tr + key + '</td>' + tr_end + check + '</td></tr>';
                    }else{
                        rows = rows + tr + key + '</td>' + tr_end + cross + '</td></tr>';
                    }
                    
                }
            }

            $(this).html(table_start + rows + table_end);
        }
    });
});


