var imgshow_Src	 = new Array();
var imgshow_Captions = new Array();
var imgshow_cImage = 0; // current image
var imgshow_iPeriod  = 500; // Delay between images
var imgshow_iTimer 	 = null; // Timer to show next image
var imgshow_iPath 	 = '';

var imgshow_cAttr   = (navigator.appName == 'Microsoft Internet Explorer') ? "className" : "class";

function imgshow_glow(obj)
{
	obj.setAttribute(imgshow_cAttr, "active");
}

function imgshow_fade(obj)
{
	if (('img_gallery_'+imgshow_cImage) != obj.getAttribute('id'))
		obj.setAttribute(imgshow_cAttr, "faded");
}

function imgshow_clearTimer()
{
	if (imgshow_iTimer)
		clearTimeout(imgshow_iTimer);
}

function imgshow_delay()
{
	imgshow_clearTimer();
	imgshow_iTimer 	= setTimeout("imgshow_show()", imgshow_iPeriod * 5);		// Delay timer for some
}

function imgshow_set(number)
{
	if (typeof(number) == 'object')
	{
		var id = number.getAttribute('id');
		number = parseInt(id.replace('img_gallery_', ''));
	}
	
	imgshow_cImage = number;
	imgshow_set_info();
	
	imgshow_clearTimer();
	imgshow_iTimer = setTimeout("imgshow_show()", imgshow_iPeriod);
}

function imgshow_show()
{
	imgshow_cImage = (imgshow_cImage < imgshow_Captions.length - 1) ? (imgshow_cImage + 1) : 0;
	
	imgshow_set_info();
	imgshow_clearTimer();
	imgshow_iTimer = setTimeout("imgshow_show()", imgshow_iPeriod);
}

function imgshow_previous()
{
	 imgshow_cImage = parseInt((imgshow_cImage > 0) ? (imgshow_cImage - 1) : (imgshow_Captions.length - 1));
	 
	 imgshow_set_info();
	 imgshow_delay();
}

function imgshow_next()
{
	 // Check if position is at end of available items or end of items to show
	 imgshow_cImage = parseInt((imgshow_cImage < imgshow_Captions.length - 1) ? (imgshow_cImage + 1) : 0);
	 
	 imgshow_set_info();
	 imgshow_delay();
}

function imgshow_set_info()
{
	var children = new Array;
	if(document.getElementById('exp_img_show_navigator'))
		children = document.getElementById('exp_img_show_navigator').getElementsByTagName('img');
	var len 	 = children.length;
	
	for(iLoop = 0; iLoop < len; iLoop++)
		children[iLoop].setAttribute(imgshow_cAttr, ((imgshow_cImage == iLoop) ? "active" : "faded"));
	
	document.getElementById('exp_img_gallery_image').src 		 = imgshow_Src[imgshow_cImage].src;
	document.getElementById('exp_img_gallery_caption').innerHTML = imgshow_Captions[imgshow_cImage];	
}

function imgshow_init(src, caption, spliter, imgPeriod, imgshow_iPath)
{
	var images 		 = src.split(spliter);
	imgshow_Captions = caption.split(spliter);
	
	imgshow_iPeriod  = imgPeriod;
	imgshow_iPath    = imgshow_iPath;
	
	for (var i = 0; i < images.length; i++)
	{
		imgshow_Src[i] 	= new Image(120,120);
		imgshow_Src[i].src = images[i];
	}
	
	imgshow_iTimer 	= setTimeout("imgshow_show()", imgshow_iPeriod);
}

function imgshow_toggle(display)
{
	if(document.getElementById('exp_gallery_wraper')) 
	{
		document.getElementById('exp_gallery_wraper').style.display = display; 
	}	
}

document.onkeyup = function image_show_keyboard_navigation_handler(e) {
 		var e = window.event || e;
		var keyunicode = e.charCode || e.keyCode;
		
		if (keyunicode == 37)	// right arrow key
			imgshow_next();
		else if (keyunicode == 39)	// left arrow key
			imgshow_previous();
};

jQuery(document).ready(function(){

	jQuery("#exp_img_show_navigator img").bind("click", function(event) {
    	imgshow_set(this);
  	});
  	
  	jQuery("#exp_img_show_navigator img").bind("mouseover", function(event) {
    	imgshow_glow(this);
  	});
  	
	jQuery("#exp_img_show_navigator img").bind("mouseout", function(event) {
    	imgshow_fade(this);
  	});
});