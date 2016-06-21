(function($)
{

$( document ).ready( function()
{	
	LM_admin.init();
});

LM_admin = function()
{
	return {
		init : function()
		{
			$( '.lm_category_selector' ).change( function()
			{
				var $this  = $( this );
				var cat_id = $this.val();

				var $template_list = $( '#lm_hidden_template_' + cat_id );
				if( $template_list.length < 1 ) $template_list = $( '#lm_hidden_template_default' );

				$( '#' + $this.attr( 'id' ).replace( 'lm_category', 'lm_template' ) ).html( $template_list.val() );
			});
		}
	}
}();

})(jQuery);