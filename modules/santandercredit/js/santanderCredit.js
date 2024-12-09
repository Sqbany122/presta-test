function jakKupic() {
	
  window.open('https://www.santanderconsumer.pl/raty-jak-kupic','jakKupic','width=710,height=500,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');
  
  return false;
  
}




function obliczRate (koszyk, wariantSklepu, nrSklepu) { 
	
	koszyk = koszyk.toString().replace(/,/gi,'.').replace(/[^0-9\.]/gi, '');

	window.open('https://wniosek.eraty.pl/symulator/oblicz/numerSklepu/'+nrSklepu+'/wariantSklepu/'+wariantSklepu+'/typProduktu/0/wartoscTowarow/'+koszyk, 'obliczRate','width=640,height=580,directories=no,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');

}




function santanderCreditValidateForm() {

  if ( $('#santanderAgreement').is(':checked') ) {
        $.blockUI({ message: '<h1><img src="' + baseDir + '/modules/santandercredit/images/busy.gif" /> <span id="blockUImsg">Proszę czekać, sprawdzam zamówienie...</span></h1>' });
        ajaxValidation();
      return true;
      
  } else {
  	
      alert ('Zanim złożysz zamówienie, zapoznaj się z procedurą udzielania kredytu konsumpcyjnego na zakup towarów i usług.');     
      
      return false;
      
  }
  
} 

function ajaxValidation() {
    var query = $.ajax({
            type: 'POST',
            url: baseDir + 'modules/santandercredit/ajaxValidation.php',
            data: 'method=validateOrder&orderId=' + $('#orderId').val(),
            dataType: 'json',
            timeout: 10000
        }).done(function(json) {
            if( typeof json === 'object'){
              if(typeof json.result === 'string' && json.result.indexOf("ERROR") >= 0){
                alert(json.result);
                $.unblockUI();                    
              } else {
                $("#blockUImsg").text("Łaczę się z serwisem bankowym...");              
                $("#orderId").attr('value',json.result);
                $("#submitBtn").click();                    
              }                
            }
          }).fail(function(json){
                alert("Błąd przy zapisie zamówienia.");
                $.unblockUI();  
          });
}