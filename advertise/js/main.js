(function($) {
    "use strict";
 

    $(document).ready(function() {
            /* =================================
                   Hero/ Main Header             
             =================================== */
             expandHeight();
            $(window).bind('resize', expandHeight);

            function expandHeight() {
                var windowHeight = $(window).height();
                if ( windowHeight > 480 && windowHeight < 675 ){
                    $('.header').height(windowHeight);
                }
            }


            $( "#arrow-down" ).click(function() {

         // console.log($(this).data());

               // console.log(clicked);
                var target = $(this).data('starget');

                var pos = $('#'+ target +'').offset();

                $('html,body').animate({
                   scrollTop: pos.top
                }, 1000);

 
            });
                


    });

})(jQuery);