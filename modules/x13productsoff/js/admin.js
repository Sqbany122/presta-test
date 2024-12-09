$(document).ready(function()
{
	$('.btn-attributes-update').on('click', function(e)
	{
		e.preventDefault();

		var id_shop = $(this).data('shopid');
		$.ajax(
		{
			url: update_url+id_shop+'&get_list=1',
			context: this,
			dataType: 'json',
			cache: 'false',
			success: function(data)
			{
				var html = '';
				$('#change-default-attribute-list-' + id_shop).hide();
				$('#check-update-empty-warning-' + id_shop).hide();
				$('#check-update-warning-' + id_shop).show();
				
				for(var i in data['products']) {
					html += '<tr><td>' + data['products'][i]['id_product'] + '</td><td>' + data['products'][i]['name'] + '</td><td>' + update_mode + '</td></tr>';
				}

				for(var i in data['products_enabled']) {
					html += '<tr><td>' + data['products_enabled'][i]['id_product'] + '</td><td>' + data['products_enabled'][i]['name'] + '</td><td>' + enable_mode + '</td></tr>';
				}
			
				if (html != '') {
					$('#change-default-attribute-list-' + id_shop).show();
					$('#change-default-attribute-list-' + id_shop + ' tbody').html(html);
				} else {
					$('#check-update-empty-warning-' + id_shop).show();
				}
				$('#check-update-warning-' + id_shop).hide();
			
				return;
			},
			error: function(res)
			{
				alert('Wystąpił błąd');
			}
		});
	});
});