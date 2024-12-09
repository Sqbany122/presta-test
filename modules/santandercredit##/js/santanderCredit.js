function jakKupic() {
    window.open('https://www.santanderconsumer.pl/raty-jak-kupic', 'jakKupic', 'width=710,height=500,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');
    return false;
}

function obliczRate(nrSklepu, symUrl, qtySelector, basketSelector) {
    // qtySelector, basketSelector should be parameter
/*    var qtySelector, basketSelector;
    qtySelector = '#quantity_wanted';
    basketSelector = "div.current-price > span"; */
    // ------
	var koszyk, ilo, qty, basket;
    koszyk = 0;
    ilo = 0
    qty = $(qtySelector);
    basket = $(basketSelector);
    if(qty.length == 1 && basket.length == 1) {
        ilo = qty.val();
        koszyk = basket.attr('content') * ilo;
        koszyk = Math.round(koszyk * 100, 2) / 100; //some stupid code just to solve strange js numeric results problem
        if (koszyk > 100) {
            window.open(symUrl + 'numerSklepu/' + nrSklepu + '/wariantSklepu/1/typProduktu/0/wartoscTowarow/' + koszyk);
        } else {
            alert("Kredytujemy zakupy w cenie powyżej 100zł");
        }
    } else {
        alert('quantity or price selector problem, call to Admin');
    }
}

function santanderCreditValidateForm() {
    if ($('#santanderAgreement').is(':checked')) {        
        $('#scbSubmitBtn').removeAttr('disabled');
    } else {
        $('#scbSubmitBtn').attr('disabled','disabled');
    }
}
