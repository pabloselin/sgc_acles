/*!
 * jQuery Cookie Plugin v1.3.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals.
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	function converted(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}
		try {
			return config.json ? JSON.parse(s) : s;
		} catch(er) {}
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			return (document.cookie = [
				config.raw ? key : encodeURIComponent(key),
				'=',
				config.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		var result = key ? undefined : {};
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = decode(parts.join('='));

			if (key && key === name) {
				result = converted(cookie);
				break;
			}

			if (!key) {
				result[name] = converted(cookie);
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== undefined) {
			// Must not alter options, thus extending a fresh object...
			$.cookie(key, '', $.extend({}, options, { expires: -1 }));
			return true;
		}
		return false;
	};

}));

(function($){jQuery.fn.Rut=function(options){var defaults={digito_verificador:null,on_error:function(){},on_success:function(){},validation:true,format:true,format_on:'change'};var opts=$.extend(defaults,options);return this.each(function(){if(defaults.format){jQuery(this).bind(defaults.format_on,function(){jQuery(this).val(jQuery.Rut.formatear(jQuery(this).val(),defaults.digito_verificador==null));});}if(defaults.validation){if(defaults.digito_verificador==null){jQuery(this).bind('blur',function(){var rut=jQuery(this).val();if(jQuery(this).val()!=""&&!jQuery.Rut.validar(rut)){defaults.on_error();}else if(jQuery(this).val()!=""){defaults.on_success();}});}else
{var id=jQuery(this).attr("id");jQuery(defaults.digito_verificador).bind('blur',function(){var rut=jQuery("#"+id).val()+"-"+jQuery(this).val();if(jQuery(this).val()!=""&&!jQuery.Rut.validar(rut)){defaults.on_error();}else if(jQuery(this).val()!=""){defaults.on_success();}});}}});}})(jQuery);jQuery.Rut={formatear:function(Rut,digitoVerificador){var sRut=new String(Rut);var sRutFormateado='';sRut=jQuery.Rut.quitarFormato(sRut);if(digitoVerificador){var sDV=sRut.charAt(sRut.length-1);sRut=sRut.substring(0,sRut.length-1);}while(sRut.length>3){sRutFormateado="."+sRut.substr(sRut.length-3)+sRutFormateado;sRut=sRut.substring(0,sRut.length-3);}sRutFormateado=sRut+sRutFormateado;if(sRutFormateado!=""&&digitoVerificador){sRutFormateado+="-"+sDV;}else if(digitoVerificador){sRutFormateado+=sDV;}return sRutFormateado;},quitarFormato:function(rut){var strRut=new String(rut);while(strRut.indexOf(".")!=-1){strRut=strRut.replace(".","");}while(strRut.indexOf("-")!=-1){strRut=strRut.replace("-","");}return strRut;},digitoValido:function(dv){if(dv!='0'&&dv!='1'&&dv!='2'&&dv!='3'&&dv!='4'&&dv!='5'&&dv!='6'&&dv!='7'&&dv!='8'&&dv!='9'&&dv!='k'&&dv!='K'){return false;}return true;},digitoCorrecto:function(crut){largo=crut.length;if(largo<2){return false;}if(largo>2){rut=crut.substring(0,largo-1);}else
{rut=crut.charAt(0);}dv=crut.charAt(largo-1);jQuery.Rut.digitoValido(dv);if(rut==null||dv==null){return 0;}dvr=jQuery.Rut.getDigito(rut);if(dvr!=dv.toLowerCase()){return false;}return true;},getDigito:function(rut){var dvr='0';suma=0;mul=2;for(i=rut.length-1;i>=0;i--){suma=suma+rut.charAt(i)*mul;if(mul==7){mul=2;}else
{mul++;}}res=suma%11;if(res==1){return'k';}else if(res==0){return'0';}else
{return 11-res;}},validar:function(texto){texto=jQuery.Rut.quitarFormato(texto);largo=texto.length;if(largo<2){return false;}for(i=0;i<largo;i++){if(!jQuery.Rut.digitoValido(texto.charAt(i))){return false;}}var invertido="";for(i=(largo-1),j=0;i>=0;i--,j++){invertido=invertido+texto.charAt(i);}var dtexto="";dtexto=dtexto+invertido.charAt(0);dtexto=dtexto+'-';cnt=0;for(i=1,j=2;i<largo;i++,j++){if(cnt==3){dtexto=dtexto+'.';j++;dtexto=dtexto+invertido.charAt(i);cnt=1;}else
{dtexto=dtexto+invertido.charAt(i);cnt++;}}invertido="";for(i=(dtexto.length-1),j=0;i>=0;i--,j++){invertido=invertido+dtexto.charAt(i);}if(jQuery.Rut.digitoCorrecto(texto)){return true;}return false;}};

/*! 
 * jQuery Steps v1.0.4 - 12/17/2013
 * Copyright (c) 2013 Rafael Staib (http://www.jquery-steps.com)
 * Licensed under MIT http://www.opensource.org/licenses/MIT
 */
!function(a,b){function c(a,b){o(a).push(b)}function d(d,e,f){var g=d.children(e.headerTag),h=d.children(e.bodyTag);g.length>h.length?R(Z,"contents"):g.length<h.length&&R(Z,"titles");var i=e.startIndex;if(f.stepCount=g.length,e.saveState&&a.cookie){var j=a.cookie(U+q(d)),k=parseInt(j,0);!isNaN(k)&&k<f.stepCount&&(i=k)}f.currentIndex=i,g.each(function(e){var f=a(this),g=h.eq(e),i=g.data("mode"),j=null==i?$.html:r($,/^\s*$/.test(i)||isNaN(i)?i:parseInt(i,0)),k=j===$.html||g.data("url")===b?"":g.data("url"),l=j!==$.html&&"1"===g.data("loaded"),m=a.extend({},bb,{title:f.html(),content:j===$.html?g.html():"",contentUrl:k,contentMode:j,contentLoaded:l});c(d,m)})}function e(a,b){return a.currentIndex-b}function f(b,c){var d=i(b);b.unbind(d).removeData("uid").removeData("options").removeData("state").removeData("steps").removeData("eventNamespace").find(".actions a").unbind(d),b.removeClass(c.clearFixCssClass+" vertical");var e=b.find(".content > *");e.removeData("loaded").removeData("mode").removeData("url"),e.removeAttr("id").removeAttr("role").removeAttr("tabindex").removeAttr("class").removeAttr("style")._removeAria("labelledby")._removeAria("hidden"),b.find(".content > [data-mode='async'],.content > [data-mode='iframe']").empty();var f=a(h('<{0} class="{1}"></{0}>',b.get(0).tagName,b.attr("class"))),g=b._getId();return null!=g&&""!==g&&f._setId(g),f.html(b.find(".content").html()),b.after(f),b.remove(),f}function g(a,b){var c=a.find(".steps li").eq(b.currentIndex);a.triggerHandler("finishing",[b.currentIndex])?(c.addClass("done").removeClass("error"),a.triggerHandler("finished",[b.currentIndex])):c.addClass("error")}function h(a){for(var b=1;b<arguments.length;b++){var c=b-1,d=new RegExp("\\{"+c+"\\}","gm");a=a.replace(d,arguments[b])}return a}function i(a){var b=a.data("eventNamespace");return null==b&&(b="."+q(a),a.data("eventNamespace",b)),b}function j(a,b){var c=q(a);return a.find("#"+c+V+b)}function k(a,b){var c=q(a);return a.find("#"+c+W+b)}function l(a,b){var c=q(a);return a.find("#"+c+X+b)}function m(a){return a.data("options")}function n(a){return a.data("state")}function o(a){return a.data("steps")}function p(a,b){var c=o(a);return(0>b||b>=c.length)&&R(Y),c[b]}function q(a){var b=a.data("uid");return null==b&&(b=a._getId(),null==b&&(b="steps-uid-".concat(T),a._setId(b)),T++,a.data("uid",b)),b}function r(a,c){if(S("enumType",a),S("keyOrValue",c),"string"==typeof c){var d=a[c];return d===b&&R("The enum key '{0}' does not exist.",c),d}if("number"==typeof c){for(var e in a)if(a[e]===c)return c;R("Invalid enum value '{0}'.",c)}else R("Invalid key or value type.")}function s(a,b,c){return B(a,b,c,v(c,1))}function t(a,b,c){return B(a,b,c,e(c,1))}function u(a,b,c,d){if((0>d||d>=c.stepCount)&&R(Y),!(b.forceMoveForward&&d<c.currentIndex)){var e=c.currentIndex;return a.triggerHandler("stepChanging",[c.currentIndex,d])?(c.currentIndex=d,O(a,b,c),E(a,b,c,e),D(a,b,c),A(a,b,c),P(a,b,c,d,e),a.triggerHandler("stepChanged",[d,e])):a.find(".steps li").eq(e).addClass("error"),!0}}function v(a,b){return a.currentIndex+b}function w(b){var c=a.extend(!0,{},cb,b);return this.each(function(){var b=a(this),e={currentIndex:c.startIndex,currentStep:null,stepCount:0,transitionElement:null};b.data("options",c),b.data("state",e),b.data("steps",[]),d(b,c,e),J(b,c,e),G(b,c),c.autoFocus&&0===T&&j(b,c.startIndex).focus()})}function x(b,c,d,e,f){(0>e||e>d.stepCount)&&R(Y),f=a.extend({},bb,f),y(b,e,f),d.currentIndex>=e&&(d.currentIndex++,O(b,c,d)),d.stepCount++;var g=b.find(".content"),i=a(h("<{0}>{1}</{0}>",c.headerTag,f.title)),j=a(h("<{0}></{0}>",c.bodyTag));return(null==f.contentMode||f.contentMode===$.html)&&j.html(f.content),0===e?g.prepend(j).prepend(i):k(b,e-1).after(j).after(i),K(b,j,e),N(b,c,d,i,e),F(b,c,d,e),D(b,c,d),b}function y(a,b,c){o(a).splice(b,0,c)}function z(b){var c=a(this),d=m(c),e=n(c);if(d.suppressPaginationOnFocus&&c.find(":focus").is(":input"))return b.preventDefault(),!1;var f={left:37,right:39};b.keyCode===f.left?(b.preventDefault(),t(c,d,e)):b.keyCode===f.right&&(b.preventDefault(),s(c,d,e))}function A(b,c,d){if(d.stepCount>0){var e=p(b,d.currentIndex);if(!c.enableContentCache||!e.contentLoaded)switch(r($,e.contentMode)){case $.iframe:b.find(".content > .body").eq(d.currentIndex).empty().html('<iframe src="'+e.contentUrl+'" frameborder="0" scrolling="no" />').data("loaded","1");break;case $.async:var f=k(b,d.currentIndex)._aria("busy","true").empty().append(M(c.loadingTemplate,{text:c.labels.loading}));a.ajax({url:e.contentUrl,cache:!1}).done(function(a){f.empty().html(a)._aria("busy","false").data("loaded","1")})}}}function B(a,b,c,d){var e=c.currentIndex;if(d>=0&&d<c.stepCount&&!(b.forceMoveForward&&d<c.currentIndex)){var f=j(a,d),g=f.parent(),h=g.hasClass("disabled");return g._enableAria(),f.click(),e===c.currentIndex&&h?(g._disableAria(),!1):!0}return!1}function C(b){b.preventDefault();var c=a(this),d=c.parent().parent().parent().parent(),e=m(d),f=n(d),h=c.attr("href");switch(h.substring(h.lastIndexOf("#"))){case"#finish":g(d,f);break;case"#next":s(d,e,f);break;case"#previous":t(d,e,f)}}function D(a,b,c){if(b.enablePagination){var d=a.find(".actions a[href$='#finish']").parent(),e=a.find(".actions a[href$='#next']").parent();if(!b.forceMoveForward){var f=a.find(".actions a[href$='#previous']").parent();c.currentIndex>0?f._enableAria():f._disableAria()}b.enableFinishButton&&b.showFinishButtonAlways?0===c.stepCount?(d._disableAria(),e._disableAria()):c.stepCount>1&&c.stepCount>c.currentIndex+1?(d._enableAria(),e._enableAria()):(d._enableAria(),e._disableAria()):0===c.stepCount?(d._hideAria(),e._showAria()._disableAria()):c.stepCount>c.currentIndex+1?(d._hideAria(),e._showAria()._enableAria()):b.enableFinishButton?(d._showAria(),e._hideAria()):e._disableAria()}}function E(b,c,d,e){var f=j(b,d.currentIndex),g=a('<span class="current-info audible">'+c.labels.current+" </span>"),h=b.find(".content > .title");if(null!=e){var i=j(b,e);i.parent().addClass("done").removeClass("error")._deselectAria(),h.eq(e).removeClass("current").next(".body").removeClass("current"),g=i.find(".current-info"),f.focus()}f.prepend(g).parent()._selectAria().removeClass("done")._enableAria(),h.eq(d.currentIndex).addClass("current").next(".body").addClass("current")}function F(a,b,c,d){for(var e=q(a),f=d;f<c.stepCount;f++){var g=e+V+f,h=e+W+f,i=e+X+f,j=a.find(".title").eq(f)._setId(i);a.find(".steps a").eq(f)._setId(g)._aria("controls",h).attr("href","#"+i).html(M(b.titleTemplate,{index:f+1,title:j.html()})),a.find(".body").eq(f)._setId(h)._aria("labelledby",i)}}function G(a,b){var c=i(a);a.bind("finishing"+c,b.onFinishing),a.bind("finished"+c,b.onFinished),a.bind("stepChanging"+c,b.onStepChanging),a.bind("stepChanged"+c,b.onStepChanged),b.enableKeyNavigation&&a.bind("keyup"+c,z),a.find(".actions a").bind("click"+c,C)}function H(a,b,c,d){return 0>d||d>=c.stepCount||c.currentIndex===d?!1:(I(a,d),c.currentIndex>d&&(c.currentIndex--,O(a,b,c)),c.stepCount--,l(a,d).remove(),k(a,d).remove(),j(a,d).parent().remove(),0===d&&a.find(".steps li").first().addClass("first"),d===c.stepCount&&a.find(".steps li").eq(d).addClass("last"),F(a,b,c,d),D(a,b,c),!0)}function I(a,b){o(a).splice(b,1)}function J(b,c,d){var e='<{0} class="{1}">{2}</{0}>',f=r(_,c.stepsOrientation),g=f===_.vertical?" vertical":"",i=a(h(e,c.contentContainerTag,"content "+c.clearFixCssClass,b.html())),j=a(h(e,c.stepsContainerTag,"steps "+c.clearFixCssClass,'<ul role="tablist"></ul>')),k=i.children(c.headerTag),l=i.children(c.bodyTag);b.attr("role","application").empty().append(j).append(i).addClass(c.cssClass+" "+c.clearFixCssClass+g),l.each(function(c){K(b,a(this),c)}),l.eq(d.currentIndex)._showAria(),k.each(function(e){N(b,c,d,a(this),e)}),E(b,c,d),L(b,c,d)}function K(a,b,c){var d=q(a),e=d+W+c,f=d+X+c;b._setId(e).attr("role","tabpanel")._aria("labelledby",f).addClass("body")._hideAria()}function L(a,b,c){if(b.enablePagination){var d='<{0} class="actions {1}"><ul role="menu" aria-label="{2}">{3}</ul></{0}>',e='<li><a href="#{0}" role="menuitem">{1}</a></li>',f="";b.forceMoveForward||(f+=h(e,"previous",b.labels.previous)),f+=h(e,"next",b.labels.next),b.enableFinishButton&&(f+=h(e,"finish",b.labels.finish)),a.append(h(d,b.actionContainerTag,b.clearFixCssClass,b.labels.pagination,f)),D(a,b,c),A(a,b,c)}}function M(a,c){for(var d=a.match(/#([a-z]*)#/gi),e=0;e<d.length;e++){var f=d[e],g=f.substring(1,f.length-1);c[g]===b&&R("The key '{0}' does not exist in the substitute collection!",g),a=a.replace(f,c[g])}return a}function N(b,c,d,e,f){var g=q(b),h=g+V+f,j=g+W+f,k=g+X+f,l=b.find(".steps > ul"),m=M(c.titleTemplate,{index:f+1,title:e.html()}),n=a('<li role="tab"><a id="'+h+'" href="#'+k+'" aria-controls="'+j+'">'+m+"</a></li>");c.enableAllSteps||n._disableAria(),d.currentIndex>f&&n._enableAria().addClass("done"),e._setId(k).attr("tabindex","-1").addClass("title"),0===f?l.prepend(n):l.find("li").eq(f-1).after(n),0===f&&l.find("li").removeClass("first").eq(f).addClass("first"),f===d.stepCount-1&&l.find("li").removeClass("last").eq(f).addClass("last"),n.children("a").bind("click"+i(b),Q)}function O(b,c,d){c.saveState&&a.cookie&&a.cookie(U+q(b),d.currentIndex)}function P(b,c,d,e,f){var g=b.find(".content > .body"),h=r(ab,c.transitionEffect),i=c.transitionEffectSpeed,j=g.eq(e),k=g.eq(f);switch(h){case ab.fade:case ab.slide:var l=h===ab.fade?"fadeOut":"slideUp",m=h===ab.fade?"fadeIn":"slideDown";d.transitionElement=j,k[l](i,function(){var b=a(this)._hideAria().parent().parent(),c=n(b);c.transitionElement&&(c.transitionElement[m](i,function(){a(this)._showAria()}),c.transitionElement=null)}).promise();break;case ab.slideLeft:var o=k.outerWidth(!0),p=e>f?-o:o,q=e>f?o:-o;k.animate({left:p},i,function(){a(this)._hideAria()}).promise(),j.css("left",q+"px")._showAria().animate({left:0},i).promise();break;default:k._hideAria(),j._showAria()}}function Q(b){b.preventDefault();var c=a(this),d=c.parent().parent().parent().parent(),e=m(d),f=n(d),g=f.currentIndex;if(c.parent().is(":not(.disabled):not(.current)")){var h=c.attr("href"),i=parseInt(h.substring(h.lastIndexOf("-")+1),0);u(d,e,f,i)}return g===f.currentIndex?(j(d,g).focus(),!1):void 0}function R(a){throw arguments.length>1&&(a=h.apply(this,arguments)),new Error(a)}function S(a,b){null==b&&R("The argument '{0}' is null or undefined.",a)}var T=0,U="jQu3ry_5teps_St@te_",V="-t-",W="-p-",X="-h-",Y="Index out of range.",Z="One or more corresponding step {0} are missing.";a.fn.steps=function(b){return a.fn.steps[b]?a.fn.steps[b].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof b&&b?(a.error("Method "+b+" does not exist on jQuery.steps"),void 0):w.apply(this,arguments)},a.fn.steps.add=function(a){var b=m(this),c=n(this);return x(this,b,c,c.stepCount,a)},a.fn.steps.destroy=function(){var a=m(this);return f(this,a)},a.fn.steps.finish=function(){var a=n(this);g(this,a)},a.fn.steps.getCurrentIndex=function(){return n(this).currentIndex},a.fn.steps.getCurrentStep=function(){return p(this,n(this).currentIndex)},a.fn.steps.getStep=function(a){return p(this,a)},a.fn.steps.insert=function(a,b){var c=m(this),d=n(this);return x(this,c,d,a,b)},a.fn.steps.next=function(){var a=m(this),b=n(this);return s(this,a,b)},a.fn.steps.previous=function(){var a=m(this),b=n(this);return t(this,a,b)},a.fn.steps.remove=function(a){var b=m(this),c=n(this);return H(this,b,c,a)},a.fn.steps.setStep=function(){throw new Error("Not yet implemented!")},a.fn.steps.skip=function(){throw new Error("Not yet implemented!")};var $=a.fn.steps.contentMode={html:0,iframe:1,async:2},_=a.fn.steps.stepsOrientation={horizontal:0,vertical:1},ab=a.fn.steps.transitionEffect={none:0,fade:1,slide:2,slideLeft:3},bb=a.fn.steps.stepModel={title:"",content:"",contentUrl:"",contentMode:$.html,contentLoaded:!1},cb=a.fn.steps.defaults={headerTag:"h1",bodyTag:"div",contentContainerTag:"div",actionContainerTag:"div",stepsContainerTag:"div",cssClass:"wizard",clearFixCssClass:"clearfix",stepsOrientation:_.horizontal,titleTemplate:'<span class="number">#index#.</span> #title#',loadingTemplate:'<span class="spinner"></span> #text#',autoFocus:!1,enableAllSteps:!1,enableKeyNavigation:!0,enablePagination:!0,suppressPaginationOnFocus:!0,enableContentCache:!0,enableFinishButton:!0,preloadContent:!1,showFinishButtonAlways:!1,forceMoveForward:!1,saveState:!1,startIndex:0,transitionEffect:ab.none,transitionEffectSpeed:200,onStepChanging:function(){return!0},onStepChanged:function(){},onFinishing:function(){return!0},onFinished:function(){},labels:{current:"current step:",pagination:"Pagination",finish:"Finish",next:"Next",previous:"Previous",loading:"Loading ..."}};a.fn.extend({_aria:function(a,b){return this.attr("aria-"+a,b)},_removeAria:function(a){return this.removeAttr("aria-"+a)},_enableAria:function(){return this.removeClass("disabled")._aria("disabled","false")},_disableAria:function(){return this.addClass("disabled")._aria("disabled","true")},_hideAria:function(){return this.hide()._aria("hidden","true")},_showAria:function(){return this.show()._aria("hidden","false")},_selectAria:function(){return this.addClass("current")._aria("selected","true")},_deselectAria:function(){return this.removeClass("current")._aria("selected","false")},_getId:function(){return this.attr("id")},_setId:function(a){return this.attr("id",a)}})}(jQuery);
/*! jQuery Validation Plugin - v1.14.0 - 6/30/2015
 * http://jqueryvalidation.org/
 * Copyright (c) 2015 Jörn Zaefferer; Licensed MIT */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a(jQuery)}(function(a){a.extend(a.fn,{validate:function(b){if(!this.length)return void(b&&b.debug&&window.console&&console.warn("Nothing selected, can't validate, returning nothing."));var c=a.data(this[0],"validator");return c?c:(this.attr("novalidate","novalidate"),c=new a.validator(b,this[0]),a.data(this[0],"validator",c),c.settings.onsubmit&&(this.on("click.validate",":submit",function(b){c.settings.submitHandler&&(c.submitButton=b.target),a(this).hasClass("cancel")&&(c.cancelSubmit=!0),void 0!==a(this).attr("formnovalidate")&&(c.cancelSubmit=!0)}),this.on("submit.validate",function(b){function d(){var d,e;return c.settings.submitHandler?(c.submitButton&&(d=a("<input type='hidden'/>").attr("name",c.submitButton.name).val(a(c.submitButton).val()).appendTo(c.currentForm)),e=c.settings.submitHandler.call(c,c.currentForm,b),c.submitButton&&d.remove(),void 0!==e?e:!1):!0}return c.settings.debug&&b.preventDefault(),c.cancelSubmit?(c.cancelSubmit=!1,d()):c.form()?c.pendingRequest?(c.formSubmitted=!0,!1):d():(c.focusInvalid(),!1)})),c)},valid:function(){var b,c,d;return a(this[0]).is("form")?b=this.validate().form():(d=[],b=!0,c=a(this[0].form).validate(),this.each(function(){b=c.element(this)&&b,d=d.concat(c.errorList)}),c.errorList=d),b},rules:function(b,c){var d,e,f,g,h,i,j=this[0];if(b)switch(d=a.data(j.form,"validator").settings,e=d.rules,f=a.validator.staticRules(j),b){case"add":a.extend(f,a.validator.normalizeRule(c)),delete f.messages,e[j.name]=f,c.messages&&(d.messages[j.name]=a.extend(d.messages[j.name],c.messages));break;case"remove":return c?(i={},a.each(c.split(/\s/),function(b,c){i[c]=f[c],delete f[c],"required"===c&&a(j).removeAttr("aria-required")}),i):(delete e[j.name],f)}return g=a.validator.normalizeRules(a.extend({},a.validator.classRules(j),a.validator.attributeRules(j),a.validator.dataRules(j),a.validator.staticRules(j)),j),g.required&&(h=g.required,delete g.required,g=a.extend({required:h},g),a(j).attr("aria-required","true")),g.remote&&(h=g.remote,delete g.remote,g=a.extend(g,{remote:h})),g}}),a.extend(a.expr[":"],{blank:function(b){return!a.trim(""+a(b).val())},filled:function(b){return!!a.trim(""+a(b).val())},unchecked:function(b){return!a(b).prop("checked")}}),a.validator=function(b,c){this.settings=a.extend(!0,{},a.validator.defaults,b),this.currentForm=c,this.init()},a.validator.format=function(b,c){return 1===arguments.length?function(){var c=a.makeArray(arguments);return c.unshift(b),a.validator.format.apply(this,c)}:(arguments.length>2&&c.constructor!==Array&&(c=a.makeArray(arguments).slice(1)),c.constructor!==Array&&(c=[c]),a.each(c,function(a,c){b=b.replace(new RegExp("\\{"+a+"\\}","g"),function(){return c})}),b)},a.extend(a.validator,{defaults:{messages:{},groups:{},rules:{},errorClass:"error",validClass:"valid",errorElement:"label",focusCleanup:!1,focusInvalid:!0,errorContainer:a([]),errorLabelContainer:a([]),onsubmit:!0,ignore:":hidden",ignoreTitle:!1,onfocusin:function(a){this.lastActive=a,this.settings.focusCleanup&&(this.settings.unhighlight&&this.settings.unhighlight.call(this,a,this.settings.errorClass,this.settings.validClass),this.hideThese(this.errorsFor(a)))},onfocusout:function(a){this.checkable(a)||!(a.name in this.submitted)&&this.optional(a)||this.element(a)},onkeyup:function(b,c){var d=[16,17,18,20,35,36,37,38,39,40,45,144,225];9===c.which&&""===this.elementValue(b)||-1!==a.inArray(c.keyCode,d)||(b.name in this.submitted||b===this.lastElement)&&this.element(b)},onclick:function(a){a.name in this.submitted?this.element(a):a.parentNode.name in this.submitted&&this.element(a.parentNode)},highlight:function(b,c,d){"radio"===b.type?this.findByName(b.name).addClass(c).removeClass(d):a(b).addClass(c).removeClass(d)},unhighlight:function(b,c,d){"radio"===b.type?this.findByName(b.name).removeClass(c).addClass(d):a(b).removeClass(c).addClass(d)}},setDefaults:function(b){a.extend(a.validator.defaults,b)},messages:{required:"This field is required.",remote:"Please fix this field.",email:"Please enter a valid email address.",url:"Please enter a valid URL.",date:"Please enter a valid date.",dateISO:"Please enter a valid date ( ISO ).",number:"Please enter a valid number.",digits:"Please enter only digits.",creditcard:"Please enter a valid credit card number.",equalTo:"Please enter the same value again.",maxlength:a.validator.format("Please enter no more than {0} characters."),minlength:a.validator.format("Please enter at least {0} characters."),rangelength:a.validator.format("Please enter a value between {0} and {1} characters long."),range:a.validator.format("Please enter a value between {0} and {1}."),max:a.validator.format("Please enter a value less than or equal to {0}."),min:a.validator.format("Please enter a value greater than or equal to {0}.")},autoCreateRanges:!1,prototype:{init:function(){function b(b){var c=a.data(this.form,"validator"),d="on"+b.type.replace(/^validate/,""),e=c.settings;e[d]&&!a(this).is(e.ignore)&&e[d].call(c,this,b)}this.labelContainer=a(this.settings.errorLabelContainer),this.errorContext=this.labelContainer.length&&this.labelContainer||a(this.currentForm),this.containers=a(this.settings.errorContainer).add(this.settings.errorLabelContainer),this.submitted={},this.valueCache={},this.pendingRequest=0,this.pending={},this.invalid={},this.reset();var c,d=this.groups={};a.each(this.settings.groups,function(b,c){"string"==typeof c&&(c=c.split(/\s/)),a.each(c,function(a,c){d[c]=b})}),c=this.settings.rules,a.each(c,function(b,d){c[b]=a.validator.normalizeRule(d)}),a(this.currentForm).on("focusin.validate focusout.validate keyup.validate",":text, [type='password'], [type='file'], select, textarea, [type='number'], [type='search'], [type='tel'], [type='url'], [type='email'], [type='datetime'], [type='date'], [type='month'], [type='week'], [type='time'], [type='datetime-local'], [type='range'], [type='color'], [type='radio'], [type='checkbox']",b).on("click.validate","select, option, [type='radio'], [type='checkbox']",b),this.settings.invalidHandler&&a(this.currentForm).on("invalid-form.validate",this.settings.invalidHandler),a(this.currentForm).find("[required], [data-rule-required], .required").attr("aria-required","true")},form:function(){return this.checkForm(),a.extend(this.submitted,this.errorMap),this.invalid=a.extend({},this.errorMap),this.valid()||a(this.currentForm).triggerHandler("invalid-form",[this]),this.showErrors(),this.valid()},checkForm:function(){this.prepareForm();for(var a=0,b=this.currentElements=this.elements();b[a];a++)this.check(b[a]);return this.valid()},element:function(b){var c=this.clean(b),d=this.validationTargetFor(c),e=!0;return this.lastElement=d,void 0===d?delete this.invalid[c.name]:(this.prepareElement(d),this.currentElements=a(d),e=this.check(d)!==!1,e?delete this.invalid[d.name]:this.invalid[d.name]=!0),a(b).attr("aria-invalid",!e),this.numberOfInvalids()||(this.toHide=this.toHide.add(this.containers)),this.showErrors(),e},showErrors:function(b){if(b){a.extend(this.errorMap,b),this.errorList=[];for(var c in b)this.errorList.push({message:b[c],element:this.findByName(c)[0]});this.successList=a.grep(this.successList,function(a){return!(a.name in b)})}this.settings.showErrors?this.settings.showErrors.call(this,this.errorMap,this.errorList):this.defaultShowErrors()},resetForm:function(){a.fn.resetForm&&a(this.currentForm).resetForm(),this.submitted={},this.lastElement=null,this.prepareForm(),this.hideErrors();var b,c=this.elements().removeData("previousValue").removeAttr("aria-invalid");if(this.settings.unhighlight)for(b=0;c[b];b++)this.settings.unhighlight.call(this,c[b],this.settings.errorClass,"");else c.removeClass(this.settings.errorClass)},numberOfInvalids:function(){return this.objectLength(this.invalid)},objectLength:function(a){var b,c=0;for(b in a)c++;return c},hideErrors:function(){this.hideThese(this.toHide)},hideThese:function(a){a.not(this.containers).text(""),this.addWrapper(a).hide()},valid:function(){return 0===this.size()},size:function(){return this.errorList.length},focusInvalid:function(){if(this.settings.focusInvalid)try{a(this.findLastActive()||this.errorList.length&&this.errorList[0].element||[]).filter(":visible").focus().trigger("focusin")}catch(b){}},findLastActive:function(){var b=this.lastActive;return b&&1===a.grep(this.errorList,function(a){return a.element.name===b.name}).length&&b},elements:function(){var b=this,c={};return a(this.currentForm).find("input, select, textarea").not(":submit, :reset, :image, :disabled").not(this.settings.ignore).filter(function(){return!this.name&&b.settings.debug&&window.console&&console.error("%o has no name assigned",this),this.name in c||!b.objectLength(a(this).rules())?!1:(c[this.name]=!0,!0)})},clean:function(b){return a(b)[0]},errors:function(){var b=this.settings.errorClass.split(" ").join(".");return a(this.settings.errorElement+"."+b,this.errorContext)},reset:function(){this.successList=[],this.errorList=[],this.errorMap={},this.toShow=a([]),this.toHide=a([]),this.currentElements=a([])},prepareForm:function(){this.reset(),this.toHide=this.errors().add(this.containers)},prepareElement:function(a){this.reset(),this.toHide=this.errorsFor(a)},elementValue:function(b){var c,d=a(b),e=b.type;return"radio"===e||"checkbox"===e?this.findByName(b.name).filter(":checked").val():"number"===e&&"undefined"!=typeof b.validity?b.validity.badInput?!1:d.val():(c=d.val(),"string"==typeof c?c.replace(/\r/g,""):c)},check:function(b){b=this.validationTargetFor(this.clean(b));var c,d,e,f=a(b).rules(),g=a.map(f,function(a,b){return b}).length,h=!1,i=this.elementValue(b);for(d in f){e={method:d,parameters:f[d]};try{if(c=a.validator.methods[d].call(this,i,b,e.parameters),"dependency-mismatch"===c&&1===g){h=!0;continue}if(h=!1,"pending"===c)return void(this.toHide=this.toHide.not(this.errorsFor(b)));if(!c)return this.formatAndAdd(b,e),!1}catch(j){throw this.settings.debug&&window.console&&console.log("Exception occurred when checking element "+b.id+", check the '"+e.method+"' method.",j),j instanceof TypeError&&(j.message+=".  Exception occurred when checking element "+b.id+", check the '"+e.method+"' method."),j}}if(!h)return this.objectLength(f)&&this.successList.push(b),!0},customDataMessage:function(b,c){return a(b).data("msg"+c.charAt(0).toUpperCase()+c.substring(1).toLowerCase())||a(b).data("msg")},customMessage:function(a,b){var c=this.settings.messages[a];return c&&(c.constructor===String?c:c[b])},findDefined:function(){for(var a=0;a<arguments.length;a++)if(void 0!==arguments[a])return arguments[a];return void 0},defaultMessage:function(b,c){return this.findDefined(this.customMessage(b.name,c),this.customDataMessage(b,c),!this.settings.ignoreTitle&&b.title||void 0,a.validator.messages[c],"<strong>Warning: No message defined for "+b.name+"</strong>")},formatAndAdd:function(b,c){var d=this.defaultMessage(b,c.method),e=/\$?\{(\d+)\}/g;"function"==typeof d?d=d.call(this,c.parameters,b):e.test(d)&&(d=a.validator.format(d.replace(e,"{$1}"),c.parameters)),this.errorList.push({message:d,element:b,method:c.method}),this.errorMap[b.name]=d,this.submitted[b.name]=d},addWrapper:function(a){return this.settings.wrapper&&(a=a.add(a.parent(this.settings.wrapper))),a},defaultShowErrors:function(){var a,b,c;for(a=0;this.errorList[a];a++)c=this.errorList[a],this.settings.highlight&&this.settings.highlight.call(this,c.element,this.settings.errorClass,this.settings.validClass),this.showLabel(c.element,c.message);if(this.errorList.length&&(this.toShow=this.toShow.add(this.containers)),this.settings.success)for(a=0;this.successList[a];a++)this.showLabel(this.successList[a]);if(this.settings.unhighlight)for(a=0,b=this.validElements();b[a];a++)this.settings.unhighlight.call(this,b[a],this.settings.errorClass,this.settings.validClass);this.toHide=this.toHide.not(this.toShow),this.hideErrors(),this.addWrapper(this.toShow).show()},validElements:function(){return this.currentElements.not(this.invalidElements())},invalidElements:function(){return a(this.errorList).map(function(){return this.element})},showLabel:function(b,c){var d,e,f,g=this.errorsFor(b),h=this.idOrName(b),i=a(b).attr("aria-describedby");g.length?(g.removeClass(this.settings.validClass).addClass(this.settings.errorClass),g.html(c)):(g=a("<"+this.settings.errorElement+">").attr("id",h+"-error").addClass(this.settings.errorClass).html(c||""),d=g,this.settings.wrapper&&(d=g.hide().show().wrap("<"+this.settings.wrapper+"/>").parent()),this.labelContainer.length?this.labelContainer.append(d):this.settings.errorPlacement?this.settings.errorPlacement(d,a(b)):d.insertAfter(b),g.is("label")?g.attr("for",h):0===g.parents("label[for='"+h+"']").length&&(f=g.attr("id").replace(/(:|\.|\[|\]|\$)/g,"\\$1"),i?i.match(new RegExp("\\b"+f+"\\b"))||(i+=" "+f):i=f,a(b).attr("aria-describedby",i),e=this.groups[b.name],e&&a.each(this.groups,function(b,c){c===e&&a("[name='"+b+"']",this.currentForm).attr("aria-describedby",g.attr("id"))}))),!c&&this.settings.success&&(g.text(""),"string"==typeof this.settings.success?g.addClass(this.settings.success):this.settings.success(g,b)),this.toShow=this.toShow.add(g)},errorsFor:function(b){var c=this.idOrName(b),d=a(b).attr("aria-describedby"),e="label[for='"+c+"'], label[for='"+c+"'] *";return d&&(e=e+", #"+d.replace(/\s+/g,", #")),this.errors().filter(e)},idOrName:function(a){return this.groups[a.name]||(this.checkable(a)?a.name:a.id||a.name)},validationTargetFor:function(b){return this.checkable(b)&&(b=this.findByName(b.name)),a(b).not(this.settings.ignore)[0]},checkable:function(a){return/radio|checkbox/i.test(a.type)},findByName:function(b){return a(this.currentForm).find("[name='"+b+"']")},getLength:function(b,c){switch(c.nodeName.toLowerCase()){case"select":return a("option:selected",c).length;case"input":if(this.checkable(c))return this.findByName(c.name).filter(":checked").length}return b.length},depend:function(a,b){return this.dependTypes[typeof a]?this.dependTypes[typeof a](a,b):!0},dependTypes:{"boolean":function(a){return a},string:function(b,c){return!!a(b,c.form).length},"function":function(a,b){return a(b)}},optional:function(b){var c=this.elementValue(b);return!a.validator.methods.required.call(this,c,b)&&"dependency-mismatch"},startRequest:function(a){this.pending[a.name]||(this.pendingRequest++,this.pending[a.name]=!0)},stopRequest:function(b,c){this.pendingRequest--,this.pendingRequest<0&&(this.pendingRequest=0),delete this.pending[b.name],c&&0===this.pendingRequest&&this.formSubmitted&&this.form()?(a(this.currentForm).submit(),this.formSubmitted=!1):!c&&0===this.pendingRequest&&this.formSubmitted&&(a(this.currentForm).triggerHandler("invalid-form",[this]),this.formSubmitted=!1)},previousValue:function(b){return a.data(b,"previousValue")||a.data(b,"previousValue",{old:null,valid:!0,message:this.defaultMessage(b,"remote")})},destroy:function(){this.resetForm(),a(this.currentForm).off(".validate").removeData("validator")}},classRuleSettings:{required:{required:!0},email:{email:!0},url:{url:!0},date:{date:!0},dateISO:{dateISO:!0},number:{number:!0},digits:{digits:!0},creditcard:{creditcard:!0}},addClassRules:function(b,c){b.constructor===String?this.classRuleSettings[b]=c:a.extend(this.classRuleSettings,b)},classRules:function(b){var c={},d=a(b).attr("class");return d&&a.each(d.split(" "),function(){this in a.validator.classRuleSettings&&a.extend(c,a.validator.classRuleSettings[this])}),c},normalizeAttributeRule:function(a,b,c,d){/min|max/.test(c)&&(null===b||/number|range|text/.test(b))&&(d=Number(d),isNaN(d)&&(d=void 0)),d||0===d?a[c]=d:b===c&&"range"!==b&&(a[c]=!0)},attributeRules:function(b){var c,d,e={},f=a(b),g=b.getAttribute("type");for(c in a.validator.methods)"required"===c?(d=b.getAttribute(c),""===d&&(d=!0),d=!!d):d=f.attr(c),this.normalizeAttributeRule(e,g,c,d);return e.maxlength&&/-1|2147483647|524288/.test(e.maxlength)&&delete e.maxlength,e},dataRules:function(b){var c,d,e={},f=a(b),g=b.getAttribute("type");for(c in a.validator.methods)d=f.data("rule"+c.charAt(0).toUpperCase()+c.substring(1).toLowerCase()),this.normalizeAttributeRule(e,g,c,d);return e},staticRules:function(b){var c={},d=a.data(b.form,"validator");return d.settings.rules&&(c=a.validator.normalizeRule(d.settings.rules[b.name])||{}),c},normalizeRules:function(b,c){return a.each(b,function(d,e){if(e===!1)return void delete b[d];if(e.param||e.depends){var f=!0;switch(typeof e.depends){case"string":f=!!a(e.depends,c.form).length;break;case"function":f=e.depends.call(c,c)}f?b[d]=void 0!==e.param?e.param:!0:delete b[d]}}),a.each(b,function(d,e){b[d]=a.isFunction(e)?e(c):e}),a.each(["minlength","maxlength"],function(){b[this]&&(b[this]=Number(b[this]))}),a.each(["rangelength","range"],function(){var c;b[this]&&(a.isArray(b[this])?b[this]=[Number(b[this][0]),Number(b[this][1])]:"string"==typeof b[this]&&(c=b[this].replace(/[\[\]]/g,"").split(/[\s,]+/),b[this]=[Number(c[0]),Number(c[1])]))}),a.validator.autoCreateRanges&&(null!=b.min&&null!=b.max&&(b.range=[b.min,b.max],delete b.min,delete b.max),null!=b.minlength&&null!=b.maxlength&&(b.rangelength=[b.minlength,b.maxlength],delete b.minlength,delete b.maxlength)),b},normalizeRule:function(b){if("string"==typeof b){var c={};a.each(b.split(/\s/),function(){c[this]=!0}),b=c}return b},addMethod:function(b,c,d){a.validator.methods[b]=c,a.validator.messages[b]=void 0!==d?d:a.validator.messages[b],c.length<3&&a.validator.addClassRules(b,a.validator.normalizeRule(b))},methods:{required:function(b,c,d){if(!this.depend(d,c))return"dependency-mismatch";if("select"===c.nodeName.toLowerCase()){var e=a(c).val();return e&&e.length>0}return this.checkable(c)?this.getLength(b,c)>0:b.length>0},email:function(a,b){return this.optional(b)||/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(a)},url:function(a,b){return this.optional(b)||/^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(a)},date:function(a,b){return this.optional(b)||!/Invalid|NaN/.test(new Date(a).toString())},dateISO:function(a,b){return this.optional(b)||/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(a)},number:function(a,b){return this.optional(b)||/^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(a)},digits:function(a,b){return this.optional(b)||/^\d+$/.test(a)},creditcard:function(a,b){if(this.optional(b))return"dependency-mismatch";if(/[^0-9 \-]+/.test(a))return!1;var c,d,e=0,f=0,g=!1;if(a=a.replace(/\D/g,""),a.length<13||a.length>19)return!1;for(c=a.length-1;c>=0;c--)d=a.charAt(c),f=parseInt(d,10),g&&(f*=2)>9&&(f-=9),e+=f,g=!g;return e%10===0},minlength:function(b,c,d){var e=a.isArray(b)?b.length:this.getLength(b,c);return this.optional(c)||e>=d},maxlength:function(b,c,d){var e=a.isArray(b)?b.length:this.getLength(b,c);return this.optional(c)||d>=e},rangelength:function(b,c,d){var e=a.isArray(b)?b.length:this.getLength(b,c);return this.optional(c)||e>=d[0]&&e<=d[1]},min:function(a,b,c){return this.optional(b)||a>=c},max:function(a,b,c){return this.optional(b)||c>=a},range:function(a,b,c){return this.optional(b)||a>=c[0]&&a<=c[1]},equalTo:function(b,c,d){var e=a(d);return this.settings.onfocusout&&e.off(".validate-equalTo").on("blur.validate-equalTo",function(){a(c).valid()}),b===e.val()},remote:function(b,c,d){if(this.optional(c))return"dependency-mismatch";var e,f,g=this.previousValue(c);return this.settings.messages[c.name]||(this.settings.messages[c.name]={}),g.originalMessage=this.settings.messages[c.name].remote,this.settings.messages[c.name].remote=g.message,d="string"==typeof d&&{url:d}||d,g.old===b?g.valid:(g.old=b,e=this,this.startRequest(c),f={},f[c.name]=b,a.ajax(a.extend(!0,{mode:"abort",port:"validate"+c.name,dataType:"json",data:f,context:e.currentForm,success:function(d){var f,h,i,j=d===!0||"true"===d;e.settings.messages[c.name].remote=g.originalMessage,j?(i=e.formSubmitted,e.prepareElement(c),e.formSubmitted=i,e.successList.push(c),delete e.invalid[c.name],e.showErrors()):(f={},h=d||e.defaultMessage(c,"remote"),f[c.name]=g.message=a.isFunction(h)?h(b):h,e.invalid[c.name]=!0,e.showErrors(f)),g.valid=j,e.stopRequest(c,j)}},d)),"pending")}}});var b,c={};a.ajaxPrefilter?a.ajaxPrefilter(function(a,b,d){var e=a.port;"abort"===a.mode&&(c[e]&&c[e].abort(),c[e]=d)}):(b=a.ajax,a.ajax=function(d){var e=("mode"in d?d:a.ajaxSettings).mode,f=("port"in d?d:a.ajaxSettings).port;return"abort"===e?(c[f]&&c[f].abort(),c[f]=b.apply(this,arguments),c[f]):b.apply(this,arguments)})});
function sgcinsc_niceCurso(curso) {
  intcurso = parseInt(curso);
  switch (intcurso) {
    case 1:
      var nicecurso = '1° Básico';
    break;
    case 2:
      var nicecurso = '2° Básico';
    break;
    case 3:
      var nicecurso = '3° Básico';
    break;
    case 4:
      var nicecurso = '4° Básico';
    break;
    case 5:
      var nicecurso = '5° Básico';
    break;
    case 6:
      var nicecurso = '6° Básico';
    break;
    case 7:
      var nicecurso = '7° Básico';
    break;
    case 8:
      var nicecurso = '8° Básico';
    break;
    case 9:
      var nicecurso = 'I° Medio';
    break;
    case 10:
      var nicecurso = 'II° Medio';
    break;    
  }
  return nicecurso;
}

function sgcinsc_niceSeguro(seguro) {
  switch(seguro) {
    case 'alemana':
      var niceseguro = 'Clínica Alemana';
    break;
    case 'santamaria':
      var niceseguro = 'Clínica Santa María';
    break;
    case 'indisa':
      var niceseguro = 'Clínica Indisa';
    break;
    case 'uc':
      var niceseguro = 'Clínica Universidad Católica';
    break;
    case 'davila':
      var niceseguro = 'Clínica Dávila';
    break;
    default:
      var niceseguro = seguro;
    break;
  }
  return niceseguro;
}
/*
Funciones AJAX para ACLES
 */

//Muestra los cursos disponibles para cada nivel
function sgcinsc_renderAcles(curso, rut, modcond, inscparam, stage) {
  console.log('Stage:' + stage);
  inscparam = typeof idinsc !== 'undefined' ? idinsc : 0;
  var ajaxPlace = $('#ajaxAclesPlace');

  var nicecurso = sgcinsc_niceCurso(curso);
  ajaxPlace.empty().append('<i class="icon-refresh icon-spin"></i><br/>Cargando cursos disponibles...');
  jQuery.ajax({
    type: 'POST',
    url: sgcajax.ajaxurl,
    data: {
      action: 'sgcinsc_displaycursos',
      nivel: curso,
      rutalumno: rut,
      mod: modcond,
      idinsc: idinsc
    },
    success: function(data, textStatus, XMLHttpRequest) {
      ajaxPlace.empty().append(data);
      $('input[name="aclecurso[]"]').rules('add', {
        minlength: minacle,
        maxlength: maxacle,
        required: true,
        messages: {
          required: 'Necesitas inscribir A.C.L.E.s',
          minlength: 'Necesitas inscribir tu segundo A.C.L.E.',
          maxlength: 'Revisa tu selección. Sólo puedes inscribir ' + maxacle + ' A.C.L.E.'
        }

      });
      $('#sgcinsc_form span.cursel').empty().append(nicecurso);

        if(minacle == 2) {
          $('#sgcinsc_form p.maxcursos').empty().append('Usted debe inscribir <strong>' + minacle + ' ACLE</strong>');  
        } else if(minacle !== maxacle) {
          $('#sgcinsc_form p.maxcursos').empty().append('Usted debe inscribir entre <strong>' + minacle + ' ACLE</strong> y <strong>' + maxacle + ' ACLE</strong>');
        } else {
          $('#sgcinsc_form p.maxcursos').empty().append('Usted debe inscribir <strong>' + minacle + ' ACLE</strong>');
        }
      
      //Chequeo las que se chequearon en otros pasos.
      if(checkedarray.length > 0) {
        jQuery.each(checkedarray, function(index, element) {
          var exselected = $('input.aclecheckbox[value="'+element+'"]');
          exselected.prop('checked', true);
          $('div#curso-'+ element).addClass('selected');
          });
      }

      var preinsc = $('#ajaxAclesPlace p.oldacle');
      if(preinsc) {
        preinsc.each(function(idx, obj) {
          
          var preinscid = $(obj).data('id');
          var preinsc = $('.acleitemcurso[data-id="' + preinscid + '"]');

          $('span.aclename', preinsc).after('<span class="in">inscrito</span>');
          preinsc.addClass('preinsc');

          if(checkedarray == 0) {
            preinsc.addClass('selected');
            $('input', preinsc).prop('checked', true);  
          }
            

        });
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      ajaxPlace.empty().append('<h1 class="error">ERROR: ' + errorThrown + '</h1><p>Pruebe a volver al paso 1 y luego a éste.</p>');
    }
  });
}

//Muestra los cursos disponibles para cada nivel
function sgcinsc_renderAcleInfo(acleids, container) {    
  container.append('<p>Cargando...</p>');
  jQuery.ajax({
    type: 'POST',
    url: sgcajax.ajaxurl,
    data: {
      action: 'sgcinsc_getacles',
      acles : acleids
    },
    success: function(data, textStatus, XMLHttpRequest) {
      container.empty().append(data);      
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      container.empty().append('<h1 class="error">' + errorThrown + '</h1>');
    }
  });
}

function sgcinsc_renderFinalInfo(data) {
  //console.log(data);
  var datalen = data.length;
  var dataObj = {};

  var aclesinscs = [];

  for(i=0; i < datalen; i++) {
    if(data[i].name == 'aclecurso[]') {

      aclesinscs.push(data[i].value);

    } else {

      dataObj[data[i].name] = data[i].value;  

    }
  }

  

  var datosalumno = $('#sgcinsc_form .datos-alumno');
  var datosapoderado = $('#sgcinsc_form .datos-apoderado');
  var datosacles = $('#sgcinsc_form .datos-acle');
  var acles;
  var appendstuffalumno = '<h3>Datos del alumno(a)</h3> <ul>' + 
                    '<li><span class="fieldcont">Nombre: </span>' + dataObj['nombre_alumno'] + '</li>' +
                    '<li><span class="fieldcont">RUT: </span>' + dataObj['rut_alumno'] + '</li>' +
                    '<li><span class="fieldcont">Curso: </span>' + sgcinsc_niceCurso(dataObj['curso_alumno']) + ' ' + dataObj['letracurso'] + '</li>';
    appendstuffalumno += '<li><span class="fieldcont">Seguro Médico: </span>' + sgcinsc_niceSeguro(dataObj['seguro_alumno']) + '</li>';                  
  var appendstuffapoderado = '<h3>Datos del apoderado(a)</h3> <ul>' + 
                    '<li><span class="fieldcont">Nombre: </span>' + dataObj['nombre_apoderado'] + '</li>' +
                    '<li><span class="fieldcont">RUT: </span>' + dataObj['rut_apoderado'] + '</li>' +
                    '<li><span class="fieldcont">Email: </span>' + dataObj['email_apoderado'] + '</li>' +
                    '<li><span class="fieldcont">Teléfono: +56 2 </span>' + dataObj['fono_apoderado'] + '</li>' +
                    '<li><span class="fieldcont">Celular: +56 9 </span>' + dataObj['celu_apoderado'] + '</li>';                  
  datosalumno.empty().append(appendstuffalumno);
  datosapoderado.empty().append(appendstuffapoderado);
  
  sgcinsc_renderAcleInfo(aclesinscs, datosacles);
}

function countemptyacles(container, message) {
  $('.noacles').show();
  $(container).each(function(element) {
      var countacle = $('div.acleitem:visible', this).length;
      if(countacle == 0) {
        $(this).hide().addClass('noacles');
      }
  });
}

function sgc_getprevinsc(RUT) {
  //Obtiene los IDs de las inscripciones previas de un alumno por su RUT
  jQuery.ajax({
    type: 'POST',
    url: sgcajax.ajaxurl,
    data: {
      action: 'sgcinsc_getprevinsc',
      rut: RUT
    },
    success: function(data, textStatus, XMLHttpRequest) {
      console.log(data);
    }, 
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  })
}

function sgc_getprevinscid(idinsc) {
  //Obtiene los IDs de las inscripciones previas de un alumno por su RUT
  jQuery.ajax({
    type: 'POST',
    url: sgcajax.ajaxurl,
    data: {
      action: 'sgcinsc_getprevinscid',
      id: idinsc
    },
    success: function(data, textStatus, XMLHttpRequest) {
      console.log(data);
    }, 
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  })
}


/* La siguiente instrucción extiende las capacidades de jquery.validate() para que
  admita el método RUT, por ejemplo:

$('form').validate({
  rules : { rut : { required:true, rut:true} } ,
  messages : { rut : { required:'Escriba el rut', rut:'Revise que esté bien escrito'} }
})
// Nota: el meesage:rut sobrescribe la definición del mensaje de más abajo
*/
// comentar si jquery.Validate no se está usando

jQuery.validator.addMethod("rut2", function(value, element) { 
        return this.optional(element) || $.Rut.validar(value); 
}, "Revise el RUT, puede que esté mal escrito");

//Validator para emails
$.validator.addMethod("customemail", 
    function(value, element) {
        return /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i.test(value);
    }, 
    "Su email no parece ser válido"
);
$(document).ready(function() {
	//$('#article-acleinscstep1 .step').hide();
  //$('#article-acleinscstep1 #sgcinsc_submit').hide();
  checkedarray = new Array();
  modcond = $('form#sgcinsc_form').data('mod');
  idinsc = $('form#sgcinsc_form').data('id');
  stage = sgcajax.stage;

  $('#otroseguro, #emailalumno').hide();

  //Paso 1 se muestra por defecto
  $('#article-acleinscstep1 .step-one').show();

  $('.rut-validate').Rut({
    validation:false
  });

$('#sgcinsc_form').validate(
{ 
  debug: false,
  errorPlacement: function(error, element) {
        if(element.is('input[name="aclecurso[]"]')) {
            error.appendTo('#ajaxErrorPlace');
        } else if(element.is('input[name="celu_apoderado"]') || element.is('input[name="fono_apoderado"]') ) {
          error.appendTo(element.closest('.controls'));
        }
        else {            
            error.appendTo(element.parent('.controls'));
        }
      },  
  rules: {
    nombre_alumno: {
      minlength: 10,
      required: true
    },
    rut_alumno: {      
      required:true,
      rut2: true
    },
    curso_alumno: {
      required: true      
    },
    letracurso: {
      required: true      
    },
    seguro_alumno_select: {
      required: true
    },
    seguro_alumno: {
      required: true      
    },
    nombre_apoderado: {
      minlength: 10,
      required: true
    },
    rut_apoderado: {
      rut2: true,
      required: true
    },
    email_apoderado: {
      minlength: 10,
      required: {
        depends:function(){
                    $(this).val($.trim($(this).val()));
                        return true;
                      }
                  },
      customemail:true
    },
    fono_apoderado: {
      minlength: 8,
      maxlength: 8
    },
    celu_apoderado: {
      minlength: 8,
      maxlength: 8,
      required: true
    },
    acepta_terminos: {
      required: true
    },
    aclecurso: {
      required: true
    }    
  },
  messages: {    
    nombre_alumno: {
      required: 'Falta poner el nombre del alumno(a)',        
      minlength: 'El nombre es demasiado corto'
    },
    nombre_apoderado: {
      required: 'Falta poner el nombre del apoderado(a)',        
      minlength: 'El nombre es demasiado corto'
    },
    curso_alumno: 'Falta seleccionar curso alumno(a)',
    letracurso: 'Falta seleccionar la letra del curso del alumno(a)',
    email_apoderado: {
      required: 'Falta el email del alumno(a)',
      email: 'Por favor, escriba un email válido',
      minlength: 'El email parece ser demasiado corto'
    },
    seguro_alumno: {
      required: 'Por favor, indique el seguro médico del alumno(a)'
    },
    seguro_alumno_select: {
      required: 'Por favor, indique el seguro médico del alumno(a)'
    },
    rut_apoderado: {
      required: 'Falta el RUT del apoderado(a)'
    },
    rut_alumno: {
      required: 'Falta el RUT del alumno(a)'
    },    
    letracurso: 'Falta la letra del curso',
    celu_apoderado: {
      required: 'Falta el celular del apoderado(a)',
      minlength: 'El número es demasiado corto, deben ser 8 dígitos',
      maxlength: 'El número es demasiado largo, deben ser 8 dígitos'
    },
    fono_apoderado: {
      required: 'Falta el teléfono fijo del apoderado(a)',
      minlength: 'El número es demasiado corto, deben ser 8 dígitos',
      maxlength: 'El número es demasiado largo, deben ser 8 dígitos'
    },
    acepta_terminos: {
      required: 'Debe aceptar los términos para inscribir'
    },    
    confirmar_envio: {
      required: 'Por favor, confirme los datos para enviar la inscripción'
    }

  },
  highlight: function(element) {
    $(element).closest('.control-group').removeClass('success').addClass('error');
  },
  success: function(element) {
    if(element.is('label[for="rut_alumno"]') || element.is('label[for="rut_apoderado"]')) {
      var curinput = $('input[name="' + element.attr('for') + '"]');
      console.log(curinput.val());
      var elval = $.Rut.formatear(curinput.val(), true);
      curinput.val(elval);
    };
    element
      .text('Ok!').addClass('valid')
      .closest('.control-group').removeClass('error').addClass('success');
  }
});
  

  //Pasos  

  $('#sgcinsc_form').steps(
    {
    headerTag: 'h2.stepmark',
    bodyTag: 'fieldset',
    transitionEffect: 'slideLeft',    
    onStepChanging: function (event, currentIndex, newIndex)
                {                    
                    $("#sgcinsc_form").validate().settings.ignore = ":disabled,:hidden";
                    return $("#sgcinsc_form").valid();                                                                                                                     
                },
    onStepChanged: function(event, currentIndex, priorIndex) {
                    if(currentIndex == 1) {
                      alumrut = $('#sgcinsc_form input[name="rut_alumno"]').val();
                      curso = $('#sgcinsc_form select[name="curso_alumno"]').val();
                      cursosMinMax = sgcajax.minmaxacles;
                      minacle = cursosMinMax[curso][0];
                      maxacle = cursosMinMax[curso][1];
                    }                    
                    else if(currentIndex == 2){
                      sgcinsc_renderAcles(cursel, alumrut, modcond, idinsc, stage);                     
                    } else if(currentIndex == 3) {
                      formdata = $("#sgcinsc_form").serializeArray();                      
                      sgcinsc_renderFinalInfo(formdata);
                    }                    
                },
    onFinishing: function (event, currentIndex)
                {
                    $("#sgcinsc_form").validate().settings.ignore = ":disabled";
                    return $("#sgcinsc_form").valid();
                },
    onFinished: function (event, currentIndex)                
                    {                                                             
                      $("#sgcinsc_form").submit();                                       
                  },
    labels: {
            finish: 'Enviar Inscripción',
            next: 'Siguiente <i class="icon icon-chevron-right"></i>',
            previous: '<i class="icon icon-chevron-left"></i> Anterior',
            current: 'Paso actual:',
            pagination: 'Páginas',
            loading: 'Cargando',
            }
        }
    );

  //Mostrar casilla de otro cuando corresponda
  $('#sgcinsc_form select[name="seguro_alumno_select"]').on('change', function(){
    var selected = $('option:selected',this).attr('value');
    var trufield = $('#sgcinsc_form input[name="seguro_alumno"]');
    var otroseguro = $('#otroseguro');
      if(selected == 'otra'){
        otroseguro.show();
        trufield.attr('value', '');
      } else {        
        $('#sgcinsc_form input[name="seguro_alumno"]').attr('value', selected);
        otroseguro.hide();
      }

  });

  //Mostrar el seguro cuando modcond esté presente
  if(modcond == true && $('#sgcinsc_form select[name="seguro_alumno_select"] option[value="otra"]').prop('selected') == true ) {
      $('#sgcinsc_form select[name="seguro_alumno_select"] option[value="otra"]').prop('selected', true);
      $('#sgcinsc_form #otroseguro').show(); 
  }

  $('#sgcinsc_form .actions ul li a[href="#next"]').addClass('btn btn-success btn-large');
  $('#sgcinsc_form .actions ul li a[href="#previous"]').addClass('btn btn-success btn-large');
  $('#sgcinsc_form .actions ul li a[href="#finish"]').addClass('btn btn-danger btn-large');

	//Ajax request for showing available courses

  // 1. Guardar en algún lado la selección del curso.
  $('#sgcinsc_form select[name="curso_alumno"]').on('change', function(){
    cursel = $('option:selected', this).attr('value'); 


    //Vacío los cursos seleccionados si es que el apoderado cambia de curso.
    checkedarray = [];
      
  });

//aclechecks = $('#sgcinsc_form input.aclecheckbox'); 
//console.log(aclechecks);

  $('#sgcinsc_form #ajaxAclesPlace').on('click', 'input.aclecheckbox', function() {
       //Contar los chequeados y guardarlos en variables       
       $('.acleitemcurso').removeClass('selected');
      
        checkedarray = [];     
             

       //Revisa los chequeados en un mismo td
       if($(this).prop('checked') == true){
          var thistd = $(this).closest('div.curso');
          var notchecked = thistd.find('input.aclecheckbox:checked').not(this);
          $('div#curso-' + $(notchecked).attr('value')).removeClass('selected');
          notchecked.prop('checked', false);          
       }       

       $("#sgcinsc_form").validate();

       checkel = $('input.aclecheckbox:checked');       

       checkel.each(function(index) {
          //Poblar array para rellenar campos luego.          
          checkedarray.push($(this).attr('value'));          
          $('div#curso-'+ $(this).attr('value')).addClass('selected');          
          //Deschequear los que están en el mismo horario                   
       }); 

       //Avisar si tienes más de un curso seleccionado
          // if(checkel.length > maxacle) {
            
          // };      
       
       //No hay que dejar que pasen el máximo
  });
  // 2. Usar la variable para mostrar cursos disponibles.


	//Ajax request for storing user data


	//Generar certificado
  $('a#certinsc').on('click', function() {      
      var w = window.open('', "", "width=600, height=700, scrollbars=yes");
        //alert(ICJX_JXPath);      
      var html = $('#certificado').html();
    $(w.document.body).html(html);

  });

  //Limpiar filtro cacheado

  $('.filteracles select').prop('selectedIndex', 0);
  //Chequear días vacíos
  countemptyacles('.publicacles .dia', 'No hay A.C.L.E. para el día');
  countemptyacles('.publicacles .dia .horario', 'No hay A.C.L.E. para el horario');

  var defaultcurso = 'todos los cursos';
  var alertbox = $('div.alertacle');
  var defaultarea = 'todas las areas';
  var defaulthorario = 'todos los horarios';

  //Filtrar
  $('.filteracles select').on('change', function(event) {
      var filters = $('.filteracles select');
      var filteraction = $(this).data('filter');
      var acleitems = 'div.acleitem';
      var selectedvalue = $('option:selected', this).attr('value');

      var selectedcurso = $('.filteracles select[name="filtercurso"] option:selected').attr('value');
      var selectedarea = $('.filteracles select[name="aclesareas"] option:selected').attr('value');
      var selectedhorario = $('.filteracles select[name="acleshorario"] option:selected').attr('value');

      var filterstring = '[data-'+ filteraction + '~="' + selectedvalue + '"]';
      $(acleitems).show();
      //esconder todos los que no son
      //$(acleitems).not(filterstring).hide();
      //chequear si hay elementos filtrados previamente
      if(selectedcurso != 0) {
        $(acleitems).not('[data-curso~="'+selectedcurso+'"]').hide();
        $('.tipcurso', alertbox).html($('.filteracles select[name="filtercurso"] option:selected').text());
      } else {
        $('.tipcurso', alertbox).html(defaultcurso);
      }
      if(selectedarea != 0) {
        $(acleitems).not('[data-area~="'+selectedarea+'"]').hide();
        $('.tiparea', alertbox).html('del área ' + $('.filteracles select[name="aclesareas"] option:selected').text());
      } else {
        $('.tiparea', alertbox).html(defaultarea);
      }
      if(selectedhorario != 0) {
        $(acleitems).not('[data-horario~="'+selectedhorario+'"]').hide();
        $('.tiphorario', alertbox).html('en horario ' + $('.filteracles select[name="acleshorario"] option:selected').text());
      } else {
        $('.tiphorario', alertbox).html(defaulthorario);
      }

      alertbox.addClass('alert-success');

      countemptyacles('.publicacles .dia', 'No hay A.C.L.E. para el día');
      countemptyacles('.publicacles .dia .horario', 'No hay A.C.L.E. para el horario');

  });

  $('.filteracles .btn.clear').on('click', function(event) {

    $('.filteracles select').prop('selectedIndex', 0);
    $('div.acleitem').removeClass('filtered').show();
    countemptyacles('.publicacles .dia', 'No hay A.C.L.E. para el día');
    countemptyacles('.publicacles .dia .horario', 'No hay A.C.L.E. para el horario');
    $('.dia.noacles').removeClass('noacles');
    $('.tipcurso', alertbox).html(defaultcurso);
    $('.tiparea', alertbox).html(defaultarea);
    $('.tiphorario', alertbox).html(defaulthorario);

    alertbox.removeClass('alert-success');

  })

  $('a.populateacles').on('click', function() {
      var curso = $('reinfo').data('curso');
      var rut = $('reinfo').data('rut');
      sgcinsc_renderAcles(curso, rut, modcond, stage);
  });

    });