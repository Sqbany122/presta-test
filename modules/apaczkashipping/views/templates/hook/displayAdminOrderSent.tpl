{*
* @author    Innovation Software Sp.z.o.o
* @copyright 2018 Innovation Software Sp.z.o.o
* @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @category  apaczkashipment
* @package   apaczkashipment
* @version   1.1
*}
<br>
<fieldset id="fieldset_2">
    <legend><img src="../modules/apaczkashipping/views/img/logoMed.png">{l s='Apaczka shipping' mod='apaczkashipping'}</legend>

    <div id="apaczka_orderDialog">

        <form method="POST"
              action="index.php?controller=AdminOrders&id_order={$id_order|escape:'htmlall':'UTF-8'}&vieworder&sendsubmit=1&token={$token|escape:'htmlall':'UTF-8'}"
              id="apaczka_orderForm">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td style="font-weight: bold; text-align: left">{l s='Twoje zamówienie zostało złożone w serwisie Apaczka.pl. Numer zamówienia to:  ' mod='apaczkashipping'}
                        {$order_number_apaczka|escape:'htmlall':'UTF-8'}
                    </td>
                <tr>
                    <td style="font-weight: bold; text-align: left">{l s=' List przewozowy możesz pobrać ze strony: ' mod='apaczkashipping'}{if $isTest}
                            <a href="http://test.apaczka.pl/orderList.htm">http://test.apaczka.pl/orderList.htm</a>
                        {else}
                            <a href="https://app.apaczka.pl/orderList.htm">https://app.apaczka.pl/orderList.htm</a>
                        {/if}</td>
                <tr>
                    <td style="font-weight: bold; text-align: left">{l s=' Lub przyciskiem poniżej' mod='apaczkashipping'}</td>
                </tr>

                <tr>
                    <td style="vertical-align: top">
                        <table>
                            <tbody>

                            <style>
                                th, td {
                                    padding: 5px;
                                    text-align: left;
                                }
                            </style>

                            <tr>
                                <td>
                                    {l s='Pobranie listu przewozowego:' mod='apaczkashipping'}</td>
                                <td>
                                    <input type="submit" id="sendwaybill" name="sendwaybill" class="button"
                                           value="Pobierz list przewozowy">

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {l s='Powrót:' mod='apaczkashipping'}</td>
                                <td>
                                    <input type="submit" onclick="linkFun();" id="submitreturn" name="submitreturn"
                                           class="button" value="Powrót do zamówień">

                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>

        </form>
    </div>

    <script type="text/javascript">
        function linkFun() {
            document.getElementById('apaczka_orderForm').action = "index.php?controller=AdminOrders&id_order={$id_order|escape:'htmlall':'UTF-8'}&sendsubmit=1&token={$token|escape:'htmlall':'UTF-8'}";

        }
    </script>

</fieldset>
