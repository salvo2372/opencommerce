!function(e,t,n,i){"use strict";function s(t,n){this.options=e.extend(!0,{},a,n),this.element=e(t),this.init()}var a={theme:"default",alterType:""};e.extend(s.prototype,{init:function(){this._applyTheme(),this._alterElement()},_applyTheme:function(){this.element.addClass("theme-"+this.options.theme)},_alterElement:function(){switch(this.options.alterType){case"upper":this.element.text(this.element.text().toUpperCase());break;case"lower":this.element.text(this.element.text().toLowerCase())}}}),e.fn.alterMe=function(t){return this.each(function(){e.data(this,"alterMe")||e.data(this,"alterMe",new s(this,t))})}}(jQuery,window,document);