{if $totalOrder>=100}
<script type="text/javascript" src="{$module_dir}js/santanderCredit.js"></script>
<style>
p.payment_module a.scb:after {
      display: block;
      content: "\f054";
      position: absolute;
      right: 15px;
      margin-top: -11px;
      top: 50%;
      font-family: "FontAwesome";
      font-size: 25px;
      height: 22px;
      width: 14px;
      color: #777; }    
</style>
<div class="row">
    <div class="col-xs-12">        
          <p class="payment_module" style=" display: block;">    
            <a class="scb" href="{$link->getModuleLink('santandercredit','santanderCreditPayment')|escape:'html'}" title="Kup na raty z Santander Consumer Bank" style="padding:13px 40px 13px 4px;">
                <img src="{$module_dir}images/moduleLogo.jpg" alt="Kup na raty z Santander Consumer Bank" height="64"/>
                Kup na raty z Santander Consumer Bank
            </a>       
          </p>          
    </div>
</div>
{/if}
