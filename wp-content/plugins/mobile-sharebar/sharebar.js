var shareBar = shareBar || (function(){
    var _args = {};
    return {
        init : function(Args) {
            _args = Args;
        },
        show : function(){
            var _buttonWidth = [];
            var _buttonWidthSmall = [];
            var additionalClass = '';
            if(_args['rtl'] == true){
                additionalClass = additionalClass+' sharebar-rtl';
            }
            var buttonCount = 0;
            var whatsappText = _args['text'].replace(/%25URL%25/,_args['url']).replace(/(%25NL%25)/g,'%0A');
            var twitterText = _args['text'].replace(/%25URL%25/,'').replace(/%25NL%25/g,' ');
            var button_fb = '';
            var button_twitter = '';
            var button_wa = '';
            var button_google = '';
            if(_args['whatsapp'] == true){ 
                if((navigator.userAgent.match(/(iPhone)/g) || navigator.userAgent.match(/(Android)/g))){
                    button_wa = '<a href="WhatsApp://send?text='+whatsappText+'" class="sharebar-button sharebar-whatsapp"><div>'+_args['share_wa']+'</div></a>';
                    buttonCount = buttonCount+1;
                }
            }
            if(_args['google'] == true){ 
                button_google = '<a href="https://plus.google.com/share?url='+_args['url']+'" target="_blank" class="sharebar-button sharebar-google"><div>'+_args['share_g']+'</div></a>';
                buttonCount = buttonCount+1;
            }
            if(_args['facebook'] == true){ 
                button_fb = '<a href="https://www.facebook.com/sharer/sharer.php?u='+_args['url']+'" target="_blank" class="sharebar-button sharebar-facebook"><div>'+_args['share_fb']+'</div></a>';
                buttonCount = buttonCount+1;
            }
            if(_args['twitter'] == true){ 
                button_twitter = '<a href="https://twitter.com/intent/tweet?url='+_args['url']+'&text='+twitterText+'" target="_blank" class="sharebar-button sharebar-twitter"><div>'+_args['share_tw']+'</div></a>';
                buttonCount = buttonCount+1;
            }
            if(_args['position'] != 'top' && _args['position'] != 'bottom'){_args['position'] = 'top';}
            jQuery('body').prepend('<div id="mbl-sharebar" class="sharebar-'+_args['position']+' mbl-'+buttonCount+'-buttons'+additionalClass+'">'+button_fb+button_twitter+button_wa+button_google+'</div>');
            checkFontSize();
            checkOrientation();
            if( ((navigator.userAgent.match(/(Android)/g) || navigator.userAgent.match(/(webOS)/g) || navigator.userAgent.match(/(iPad)/g) || navigator.userAgent.match(/(iPod)/g) || navigator.userAgent.match(/(BlackBerry)/g) || navigator.userAgent.match(/(Windows Phone)/g)) && _args['everywhere'] == true && buttonCount > 1) || (navigator.userAgent.match(/(iPhone)/g) || navigator.userAgent.match(/(Android)/g))){
                jQuery(window).bind("scroll", function() {
                    checkFontSize();
                    checkOrientation();
                    if(jQuery('#mbl-sharebar').is(':hidden') && jQuery(window).scrollTop() > 150){
                        jQuery('#mbl-sharebar').show();
                    }else if(jQuery(window).scrollTop() <= 150){
                        jQuery('#mbl-sharebar').hide();
                    }
                });
                jQuery(window).bind("resize", function(){
                    checkFontSize();
                    checkOrientation();
                });
                jQuery(window).bind('orientationchange', function() {
                    checkFontSize();
                    checkOrientation();
                });
                jQuery('#mbl-sharebar .sharebar-button').each(function(index){

                });
            }
                function checkFontSize(){
                    var fontSize = (Math.floor(jQuery(window).width()/2*700/10000));
                    if(fontSize > 25){
                        fontSize = 25;
                    }
                    jQuery('#mbl-sharebar .sharebar-button').css({'font-size':fontSize+'px'});
                }
                function checkOrientation(){
                    jQuery('#mbl-sharebar').hide().removeClass('sharebar-landscape');
                    jQuery('#mbl-sharebar').css({'margin-top':'0px','margin-bottom':'0px'});
                    jQuery('#mbl-sharebar.sharebar-top').css({'top':'0px'});
                    jQuery('#mbl-sharebar.sharebar-bottom').css({'bottom':'0px'});
                    if(window.orientation != 0){
                        jQuery('#mbl-sharebar').addClass('sharebar-landscape');
                        jQuery('#mbl-sharebar.sharebar-top').css({'margin-top':(jQuery('#mbl-sharebar.sharebar-top').height()/-2)+'px'});
                        jQuery('#mbl-sharebar.sharebar-bottom').css({'margin-bottom':(jQuery('#mbl-sharebar.sharebar-bottom').height()/-2)+'px'});
                    }
                    if(jQuery('#mbl-sharebar').is(':hidden') && jQuery(window).scrollTop() > 150){
                        if( ((navigator.userAgent.match(/(Android)/g) || navigator.userAgent.match(/(webOS)/g) || navigator.userAgent.match(/(iPad)/g) || navigator.userAgent.match(/(iPod)/g) || navigator.userAgent.match(/(BlackBerry)/g) || navigator.userAgent.match(/(Windows Phone)/g)) && _args['everywhere'] == true && buttonCount > 1) || (navigator.userAgent.match(/(iPhone)/g) || navigator.userAgent.match(/(Android)/g))){
                            jQuery('#mbl-sharebar').show();
                        }
                    }
                }
        }
    };
}());

