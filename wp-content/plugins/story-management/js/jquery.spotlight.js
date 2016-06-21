(function($)
{

$.fn.spotlight = function( options )
{
	var config = { 
		color      : '#ffff33',
		mode       : 'show',
		duration   : 1000,
		easing     : 'linear',
		oncomplete : null
	};

	var save_props = function($element)
	{
		var data = {
			backgroundImage : $element.css( 'backgroundImage' ),
			backgroundColor : $element.css( 'backgroundColor' ),
			opacity         : $element.css( 'opacity' )
		};
		
		$element.data( 'spotlight_original_props', data );
	}

	var restore_props = function($element)
	{
		var data = $element.data( 'spotlight_original_props' );

		$element.css( data );
	}

	if( options ) $.extend( config, options );

	return this.queue(function()
	{
		var $this     = $( this );
		var animation = { backgroundColor : $this.css( 'backgroundColor' ) };

		if( config.mode == 'hide' ) animation.opacity = 0;

		save_props( $this );

		$this
			.show()
			.css({
				backgroundImage : 'none',
				backgroundColor : config.color
			})
			.animate( animation, {
				duration : config.duration,
				easing   : config.easing,
				queue    : false,
				complete : function()
				{
					if( config.mode == 'hide' ) $this.hide();

					restore_props( $this );

					if( config.oncomplete ) config.oncomplete.apply( this, arguments );

					$this.dequeue();
				}
			});
	});
}

})(jQuery);