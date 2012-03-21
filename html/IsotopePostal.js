var IsotopePostal = {

	checkoutAddresses: function()
	{
		['billing_address', 'shipping_address'].each( function(address)
		{
			var country = document.id(('ctrl_' + address + '_country')),
				postal = document.id(('ctrl_' + address + '_postal')),
				city = document.id(('ctrl_' + address + '_city'));
			
			if (country && postal && city)
			{
				var timer,
					xhr = new Request.JSON(
					{
						url: 'ajax.php?action=isotope_postal',
						chain: 'cancel',
						onRequest: function()
						{
							city.setStyle('background', 'url(\'system/modules/isotope_postal/html/loading.gif\') right center no-repeat');
						},
						onComplete: function(result, xml)
						{
							city.setStyle('background', 'none');
							city.set('value', result.content.city || '');
						}
					});
				
				postal.addEvent('keyup', function()
				{
					clearTimeout(timer);
					
					timer = setTimeout( function()
					{
						xhr.get(('country=' + country.value + '&postal=' + postal.value));
					}, 300);
				});
			}
		});
	}
}