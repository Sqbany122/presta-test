{*
* @author    Innovation Software Sp.z.o.o
* @copyright 2018 Innovation Software Sp.z.o.o
* @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @category  apaczkashipment
* @package   apaczkashipment
* @version   1.1
*}
<button id="apaczka_showParcelsMap" class="btn btn-primary">
    {if $parcelLockerCode}
        {l s="Wybrany paczkomat: " mod="apaczkashipping"}
        <span style="font-weight: 700">
            {$parcelLockerCode}
        </span>
    {else}
        {l s="Wybierz paczkomat" mod="apaczkashipping"}
    {/if}
</button>
<input type="hidden" id="apaczka_parcelLockerCode" name="apaczka_parcelLockerCode" value="{$parcelLockerCode}">
<script src="https://mapa.apaczka.pl/client/apaczka.map.js"></script>
<div style="width: 80%; height: 100%; margin: 20px auto;">
    <div id="easypack-map">
        <div id="apaczka-ajaxOverlay">
            <img id="apaczka-ajaxLoader"
                 src="{$modules_dir|escape:'htmlall':'UTF-8'}/apaczkashipping/views/img/spinner.svg">
            <div id="apaczka-message"></div>
        </div>
    </div>
</div>
