/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


window.fbAsyncInit = function() {
  FB.init({appId: '107951112560019', status: true, cookie: true,
			  xfbml: true});
};
(function() {
  var e = document.createElement('script');
  e.type = 'text/javascript';
  e.src = document.location.protocol +
	 '//connect.facebook.net/en_US/all.js';
  e.async = true;
  document.getElementById('fb-root').appendChild(e);
}());