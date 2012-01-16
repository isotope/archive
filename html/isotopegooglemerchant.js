/**
 * Class IsotopeGoogleRequest
 *
 * @copyright  Isotope eCommerce Workgroup 2009-2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */
var IsotopeGoogleRequest =
{

	/**
	 * Toggle the visibility of an element
	 * @param object
	 * @param string
	 * @return void
	 */
	toggleVisibility: function(el, id)
	{
		var image = $(el).getFirst();
		var publish = (image.src.indexOf('invisible') != -1);
		
		new Request({
			url: window.location.href,
			onSuccess: function(txt)
			{
				window.location = txt;
			}
		}).get({'productid':id, 'redirect':'isGoogle'});
	}
}