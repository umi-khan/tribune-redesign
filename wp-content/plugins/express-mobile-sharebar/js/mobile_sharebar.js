(function($) {

function windowSize() {
  windowHeight = window.innerHeight ? window.innerHeight : $(window).height();
  windowWidth = window.innerWidth ? window.innerWidth : $(window).width();
}

windowSize();
 $( window ).resize(function() {
    windowSize();
 });

  var stickyHeaderTop = $('#socialshare').offset().top;
  
  $(window).scroll(function(){
          if( $(window).scrollTop() > stickyHeaderTop ) {
                //Mobile Share Bar
                if(windowWidth < 450){
                    $('.mobile-share-bar').css('display', 'block');                 
                }else{
                     $('.mobile-share-bar').css('display', 'none');                              
                }
          } else {
				        $('.mobile-share-bar').css('display', 'none');
          }
    });
})(jQuery);