!function(i,t){"function"==typeof define&&(define.amd||define.cmd)?define(t):i.Loading=t()}(this,function(){var i="ui-loading",t=i+"-icon";$.fn.isLoading=function(){var i=$(this).eq(0);if(i.is("a,label,input,button"))return i.hasClass("loading");var n=i.find("."+t);return!(!n.length||!n.is(":visible"))},$.fn.loading=function(i){return $(this).each(function(){var t=$(this);t.is("a,label,input,button")?t.addClass("loading"):t.data("loading",new n(t,i))})},$.fn.unloading=function(t){var n=t||0;return"number"!=typeof t&&(n=200),void 0===t&&(t=n),$(this).each(function(e,a){var s=$(this);if(s.is("a,label,input,button"))s.removeClass("loading");else if("function"==typeof history.pushState)if(n>0){var o=s.height();s.css({height:"auto",webkitTransition:"none",transition:"none",overflow:"hidden"});var l=s.height();s.height(o),s.removeClass(i+"-animation"),a.offsetWidth=a.offsetWidth,!1!==t&&s.addClass(i+"-animation"),s.css({webkitTransition:"height "+n+"ms",transition:"height "+n+"ms"}),setTimeout(function(){s.css("overflow","")},n),s.height(l)}else s.css({webkitTransition:"none",transition:"none"}),s.height("auto").removeClass(i);else s.height("auto")})};var n=function(n,e){var a={primary:!1,small:!1,create:!1},s=$.extend({},a,e||{}),o=n,l=null,r=null;return this._create=function(){var n=this.el.container;l=n.find("."+i),r=n.find("."+t),1==s.create&&0==l.size()?(l=n.is("ul,ol")?$("<li></li>").addClass(i):$("<div></div>").addClass(i),n.append(l)):0==s.create&&(l=n),0==r.size()&&(r=(s.small?$("<s></s>"):$("<i></i>")).addClass(t),l.empty().addClass(i).append(r),s.primary&&l.addClass(i+"-primary")),r.attr({"aria-busy":"true","aria-label":"正在加载中"}),n.attr("data-position",n.css("position")),this.el.loading=l,this.el.icon=r},this.el={container:o,loading:l,icon:r},this.show(),this};return n.prototype.show=function(){var i=this.el;return i.loading&&i.icon||this._create(),i.loading.show(),this.display=!0,this},n.prototype.hide=function(){var i=this.el,n=i.container,e=i.loading;return e&&(n.get(0)!=e.get(0)?e.hide():n.find("."+t).length&&(e.empty(),this.el.icon=null)),this.display=!1,this},n.prototype.remove=function(){var t=this.el,n=t.container,e=t.loading,a=t.icon;return e&&a&&(n.get(0)==e.get(0)?(e.removeClass(i),a.remove()):e.remove(),this.el.loading=null,this.el.icon=null),this.display=!1,this},n.prototype.end=function(i){var n=this.el.container;return n&&(n.unloading(i),0==n.find("."+t).length&&(this.el.icon=null)),this.display=!1,this},n});