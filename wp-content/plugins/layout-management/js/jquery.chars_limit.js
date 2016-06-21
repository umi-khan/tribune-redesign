(function($)
{

$.fn.limit_chars = function( options )
{
	var config = { 
		limit   : 118,
		counter : '.chars_counter'
	};

	var chars_counter;

	var update_chars_counter = function(element, chars_counter_box)
	{
		chars_counter = config.limit - element.val().length;
		var color     = get_color( chars_counter );

		chars_counter_box
			.css( {color : color} )
			.find( 'span' ).text( chars_counter );
	}

	var get_color = function(num_chars)
	{
		if( num_chars <= 10 ) return '#D40D12';

		if( num_chars <= 20 ) return '#5C0002';

		if( num_chars >= 20 ) return '#666';

		return '#D40D12';
	}

	if( options ) $.extend( config, options );

	this.each(function(index, element)
	{
		var $element = $( element );
		var $counter = $( config.counter );

		$element.keyup(function()
		{
			update_chars_counter( $element, $counter );
		});
		
		$counter.hide();

		update_chars_counter( $element, $counter );
		
		$counter.show();
	});
}

})(jQuery);