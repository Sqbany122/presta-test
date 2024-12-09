<section>
    <h3>{l s='Zakupy na raty z systemem eRaty Santander Consumer Bank' mod='santandercredit'}</h3>
    <p>
        Całkowita wartość Twojego zamówienia wynosi <span style="color: #ff0000;" class="bold">{$totalOrderC}</span>
    </p>
    <p>
        Przed złożeniem wniosku możesz zapoznać się z procedurą udzielenia kredytu ratalnego oraz obliczyć raty.
    </p>
    <p>               
        <a href="https://www.santanderconsumer.pl/raty-jak-kupic"  target="_blank">
            <img src="{$imgDir}/jakKupicSmall.png" style="cursor: pointer; float: left;" title="Zapoznaj się z procedurą" alt="Zapoznaj się z procedurą" border="0" />    
        </a>
        <!--default part href="https://wniosek.eraty.pl/symulator/oblicz/" (with backslash at the end)-->
        <a href="{$symulatorURL}numerSklepu/{$shopId}/wariantSklepu/1/typProduktu/0/wartoscTowarow/{$totalOrder}/" target="_blank">
            <img src="{$imgDir}/obliczRate.png" style="cursor: pointer; float: right;" title="Oblicz ratę" alt="Oblicz ratę" border="0" />    
        </a>
    </p>  
    <div style="clear: both"></div>
</section>

