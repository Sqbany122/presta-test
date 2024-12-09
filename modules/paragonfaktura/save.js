
$(document).ready(function(){
	$('#pfform input').click(function(){
		var value = $('#pfform input:checked').val();
		var id_cart = $('#pfform #pf_id').val();
		$.ajax({
		  type: "POST",
		  url: "modules/paragonfaktura/save.php",
		  data: { value: value, id_cart: id_cart }
		}).done(function( msg ) {

		});
	})
});
