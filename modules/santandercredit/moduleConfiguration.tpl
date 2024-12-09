<style type="text/css">
    form.prestahome * {
        text-align: left;
    }
    form.prestahome dl {
        float: left;
        margin: 10px 0;
    }
    form.prestahome dt label {
        width: 500px; float: left; clear: both; margin: 0;
    }
    form.prestahome dd {
        float: left; clear: both; margin: 5px 0;
    }
    form.prestahome .button {
        float: left; clear: both; cursor: pointer; overflow: hidden;
    }
    form.prestahome input[type=text] {
        min-width: 200px; padding: 5px;
    }
    #santanderCreditBox {
        float: left;
        line-height: 25px;
        text-align: center;
    }
    #santanderCreditBox div {
        text-align: center;
        float: left;
        border: 1px solid #CCCED7;
        border-radius: 5px;
        padding: 5px;
        margin: 0 20px 20px 0;
    }
    #santanderCreditBox label {
        float: none;
        cursor: pointer;
        width: auto;
        font-weight: normal;
    }
</style>

<div id="santanderCreditBox">
    <form action="{$requestUri}" method="post" class="prestahome">
        <fieldset>
            <legend><img src="../img/admin/contact.gif" />{l s='Ustawienia' mod='santandercredit'} </legend>

            <div style="margin: 20px auto; float: none;">
                {l s='Twój numer sklepu to numer Punktu Sprzedaży wskazany przez Santander Consumer Bank w "Umowie o współpracy – tryb Internet"' mod='santandercredit'}
                <br>
                <input type="text" name="santanderCreditShopId" style="width: 80px; min-width: auto;" value="{$santanderCreditShopId}" />

                {if $santanderCreditShopId eq $shopTestId}
                    <br clear="all"><div class="warning" style="margin: 10px 0 0 0; float: none;">Uwaga! Używasz testowego numeru sklepu. <br>Służy on tylko i wyłącznie do testowania płatności ratalnej!</div>
                {/if}
            </div>

            <div>
                {l s='Status zamówienia do czasu uzyskania decyzji kredytowej: ' mod='santandercredit'}
                <br>
                <label for="leftColumn">
                    <input id="leftColumn" type="radio" name="santanderCreditUseOrderState" value="PS_OS_PAYMENT" 
                        {if $santanderCreditUseOrderState eq 'PS_OS_PAYMENT'} checked="checked" {/if}> {$psOsPaymentName}
                </label>
                &nbsp;&nbsp;
                <label for="rightColumn">
                    <input id="rightColumn" type="radio" name="santanderCreditUseOrderState" value="SANTANDERCREDIT_OS_AUTHORIZATION" 
                        {if $santanderCreditUseOrderState eq 'SANTANDERCREDIT_OS_AUTHORIZATION'} checked="checked" {/if}> {$santandercreditOsAuthName}
                </label>
            </div>            
            
            <div>
                {l s='W której kolumnie wyświetlić banner Raty Santander?' mod='santandercredit'}
                <br>
                <label for="leftColumn"><input id="leftColumn" type="radio" name="santanderCreditBlock" value="left" 
                    {if $santanderCreditBlock eq 'left'} checked="checked" {/if}> Lewej
                </label>
                &nbsp;&nbsp;
                <label for="rightColumn"><input id="rightColumn" type="radio" name="santanderCreditBlock" value="right" 
                    {if $santanderCreditBlock eq 'right'} checked="checked" {/if}> Prawej
                </label>
            </div>

            <div>
                {l s='Nagłówek bloku eRaty' mod='santandercredit'}
                <br>
                <input type="text" name="santanderCreditBlockTitle" style="width: 200px; min-width: auto;" value="{$santanderCreditBlockTitle}" />
            </div>

            <div>
                {l s='Czy wyświetlić ikonkę "Oblicz Ratę" na stronie produktu?' mod='santandercredit'}
                <br>
                <label for="santanderCreditSymulatorTak">
                    <input id="santanderCreditSymulatorTak" type="radio" name="santanderCreditSymulator" value="tak"  
                           {if $santanderCreditSymulator == 'tak'} checked="checked" {/if}> Tak
                </label>
                &nbsp;&nbsp;
                <label for="santanderCreditSymulatorNie">
                    <input id="santanderCreditSymulatorNie" type="radio" name="santanderCreditSymulator" value="nie" 
                        {if $santanderCreditSymulator == 'nie'} checked="checked" {/if}> Nie
                </label>
            </div>

            <br clear="all">

            <div style="margin: 20px auto; float: none;">
                Jeśli chcesz zmienić banner wyświetlany w kolumnach, zmień plik "bannerBlok.jpg" w katalogu "/modules/santandercredit/images/".
                <br>
                <img src="{$bannerUrl}">
            </div>

            <input type="submit" name="santanderCreditSubmit" value="{l s='Zapisz' mod='santandercredit'}" class="button" />
        </fieldset>
    </form>
</div>
