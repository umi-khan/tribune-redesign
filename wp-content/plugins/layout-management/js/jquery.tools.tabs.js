/*
 * jQuery Tools 1.2.2 - The missing UI library for the Web
 * 
 * [tabs]
 * 
 * NO COPYRIGHTS OR LICENSES. DO WHAT YOU LIKE.
 * 
 * http://flowplayer.org/tools/
 * 
 * File generated: Sun Jun 06 20:59:37 GMT 2010
 */
(function(c){function p(d,a,b){var e=this,l=d.add(this),h=d.find(b.tabs),j=a.jquery?a:d.children(a),i;h.length||(h=d.children());j.length||(j=d.parent().find(a));j.length||(j=c(a));c.extend(this,{click:function(f,g){var k=h.eq(f);if(typeof f=="string"&&f.replace("#","")){k=h.filter("[href*="+f.replace("#","")+"]");f=Math.max(h.index(k),0)}if(b.rotate){var n=h.length-1;if(f<0)return e.click(n,g);if(f>n)return e.click(0,g)}if(!k.length){if(i>=0)return e;f=b.initialIndex;k=h.eq(f)}if(f===i)return e;
g=g||c.Event();g.type="onBeforeClick";l.trigger(g,[f]);if(!g.isDefaultPrevented()){o[b.effect].call(e,f,function(){g.type="onClick";l.trigger(g,[f])});i=f;h.removeClass(b.current);k.addClass(b.current);return e}},getConf:function(){return b},getTabs:function(){return h},getPanes:function(){return j},getCurrentPane:function(){return j.eq(i)},getCurrentTab:function(){return h.eq(i)},getIndex:function(){return i},next:function(){return e.click(i+1)},prev:function(){return e.click(i-1)}});c.each("onBeforeClick,onClick".split(","),
function(f,g){c.isFunction(b[g])&&c(e).bind(g,b[g]);e[g]=function(k){c(e).bind(g,k);return e}});if(b.history&&c.fn.history){c.tools.history.init(h);b.event="history"}h.each(function(f){c(this).bind(b.event,function(g){e.click(f,g);return g.preventDefault()})});j.find("a[href^=#]").click(function(f){e.click(c(this).attr("href"),f)});if(location.hash)e.click(location.hash);else if(b.initialIndex===0||b.initialIndex>0)e.click(b.initialIndex)}c.tools=c.tools||{version:"1.2.2"};c.tools.tabs={conf:{tabs:"a",
current:"current",onBeforeClick:null,onClick:null,effect:"default",initialIndex:0,event:"click",rotate:false,history:false},addEffect:function(d,a){o[d]=a}};var o={"default":function(d,a){this.getPanes().hide().eq(d).show();a.call()},fade:function(d,a){var b=this.getConf(),e=b.fadeOutSpeed,l=this.getPanes();e?l.fadeOut(e):l.hide();l.eq(d).fadeIn(b.fadeInSpeed,a)},slide:function(d,a){this.getPanes().slideUp(200);this.getPanes().eq(d).slideDown(400,a)},ajax:function(d,a){this.getPanes().eq(0).load(this.getTabs().eq(d).attr("href"),
a)}},m;c.tools.tabs.addEffect("horizontal",function(d,a){m||(m=this.getPanes().eq(0).width());this.getCurrentPane().animate({width:0},function(){c(this).hide()});this.getPanes().eq(d).animate({width:m},function(){c(this).show();a.call()})});c.fn.tabs=function(d,a){var b=this.data("tabs");if(b)return b;if(c.isFunction(a))a={onBeforeClick:a};a=c.extend({},c.tools.tabs.conf,a);this.each(function(){b=new p(c(this),d,a);c(this).data("tabs",b)});return a.api?b:this}})(jQuery);
