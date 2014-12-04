this.require.define({"spine/controller/activateable":function(t,e,r){(function(){var t,e=[].slice;t={included:function(){var t;return t=this.prototype.className,t=t?t+" activateable"+"":"activateable",this.prototype.className=t},active:function(){var t;return t=arguments.length>=1?e.call(arguments,0):[],"function"==typeof t[0]?this.bind("active",t[0]):(t.unshift("active"),this.trigger.apply(this,t)),this},isActive:function(){return this.el.hasClass("active")},activate:function(t){return null==t&&(t={}),this.el.addClass("active"),t.silent||this.trigger("activate",t),this},deactivate:function(t){return null==t&&(t={}),this.el.removeClass("active"),t.silent||this.trigger("deactivate",t),this}},r.exports=t}).call(this)}}),this.require.define({"spine/controller/activator":function(t,e,r){(function(){var t,n,i,o,s={}.hasOwnProperty,u=function(t,e){function r(){this.constructor=t}for(var n in e)s.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t};i=e("./index"),n=e("./container"),t=function(t){function e(){return o=e.__super__.constructor.apply(this,arguments)}return u(e,t),e.include(n),e.prototype.autoDeactivate=!0,e.prototype.afterAdd=function(t){var e=this;return t.bind("active",{controller:t},function(t){return t.stopPropagation(),e._active.apply(e,arguments),!0})},e.prototype._active=function(t,e){var r,n,i,o,s;if(null==e&&(e={}),n=t.data.controller,n.isActive())return n;for(s=this.children(),i=0,o=s.length;o>i;i++)r=s[i],r===n?n.activate(e):this.autoDeactivate&&r.isActive()&&r.deactivate();return n},e}(i),r.exports=t}).call(this)}}),this.require.define({"spine/controller/container":function(t,e,r){(function(){var t;t={add:function(t,e){var r,n,i,o,s,u,a,c=this;for(null==t&&(t=[]),null==e&&(e="append"),i=function(){var e,n,i;for(i=[],e=0,n=t.length;n>e;e++)r=t[e],i.push(r.el||r);return i}(),(a=this.container||this.el)[e].apply(a,i),o=function(t){return null!=t.added?(t.added().one("release",{controller:t},function(t){return c._release.apply(c,arguments),t.stopPropagation(),!0}),"function"==typeof c.afterAdd?c.afterAdd(t):void 0):void 0},s=0,u=t.length;u>s;s++)n=t[s],o(n);return arguments.length&&this.refreshElements(),this},append:function(){return this.add(arguments)},prepend:function(){return this.add(arguments,"prepend")},children:function(t){var e,r,n,i,o,s;for(null==t&&(t=function(){return!0}),r=function(e){var r;return r=$(e).data("controller"),r&&t(r)},o=(this.container||this.el).children(),s=[],n=0,i=o.length;i>n;n++)e=o[n],r(e)&&s.push($(e).data("controller"));return s},init:function(){var t,e;return this.containerClass&&(this.append("<div class='"+this.containerClass+"'></div>"),this.container=this.$("."+this.containerClass)),this.controllers=$.extend({},this.controllers),this.append.apply(this,function(){var r,n;r=this.controllers,n=[];for(t in r)e=r[t],n.push(this[t]=new e);return n}.call(this))},_release:function(t){var e,r,n;n=this.controllers;for(e in n)r=n[e],this[e]===t.data.controller&&(delete this.controllers[e],delete this[e]);return t.data.controller}},r.exports=t}).call(this)}}),this.require.define({"spine/controller/index":function(t,e,r){(function(){var t,n,i,o=[].slice,s=function(t,e){return function(){return t.apply(e,arguments)}},u={}.hasOwnProperty,a=function(t,e){function r(){this.constructor=t}for(var n in e)u.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t};n=e("../core/module"),i=e("../core/jquerable"),$.fn.controller=function(){var t;return t=arguments.length>=1?o.call(arguments,0):[],t.unshift("controller"),this.data.apply(this,t)},t=function(t){function r(t){this.release=s(this.release,this);var e,n,i,o;this.options=t,o=this.options;for(n in o)i=o[n],this[n]=i;this.el||(this.el=document.createElement(this.tag)),this.el=$(this.el),this.el.controller(this),e=""+(this.constructor.__super__.className||"")+" "+(this.className||""),e.length>1&&this.el.addClass(e),this.attributes&&this.el.attr(this.attributes),this.events=$.extend({},this.events||{},this.constructor.prototype.events||{},this.constructor.__super__.events||{}),this.elements=$.extend({},this.elements||{},this.constructor.prototype.elements||{},this.constructor.__super__.elements||{}),this.localOnlyEvents=(this.localOnlyEvents||[]).concat(this.constructor.prototype.localOnlyEvents||[],this.constructor.__super__.localOnlyEvents||[]),(this.events||this.constructor.events||this.localOnlyEvents||this.constructor.localOnlyEvents)&&this.delegateEvents(),(this.elements||this.constructor.elements)&&this.refreshElements(),r.__super__.constructor.apply(this,arguments)}return a(r,t),r.include(i),r.prototype.eventSplitter=/^(\S+)\s*(.*)$/,r.prototype.tag="div",r.prototype.parent=function(){var t;return t=!1,this.el.parentsUntil(function(){var e;return e=Boolean($(t).controller()),t=this,e}).last().controller()},r.prototype.added=function(){return this.trigger("added"),this},r.prototype.release=function(){return this.trigger("release").unbind().el.off().remove()},r.prototype.$=function(t){return $(t,this.el)},r.prototype.delegateEvents=function(t){var e,r,n,i,o,s,u,a,c,l,h,p=this;for(null==t&&(t=this.events),l=this.localOnlyEvents,u=function(t){return p.bind(t,function(t){return t.stopPropagation()})},a=0,c=l.length;c>a;a++)e=l[a],u(e);h=[];for(n in t)o=t[n],this[o]&&(i=n.match(this.eventSplitter),r=i[1],s=i[2],o=function(t){return function(){return p[t].apply(p,arguments),!0}}(o)),""===s?h.push(this.el.bind(r,o)):h.push(this.el.on(r,s,o));return h},r.prototype.refreshElements=function(){var t,e,r;r=this.elements;for(t in r)e=r[t],this[e]=this.$(t);return this},r.prototype.delay=function(t,e){return setTimeout(this.proxy(t),e||0)},r.prototype.html=function(t){return this.el.html(t.el||t),this.refreshElements(),this},r.prototype.render=function(){return this.template&&this.html("function"==typeof this.template?this.template(this):e(this.template)(this)),this},r}(n),r.exports=t}).call(this)}}),this.require.define({"spine/controller/interactionable":function(t,e,r){(function(){var t,n,i,o,s={}.hasOwnProperty,u=function(t,e){function r(){this.constructor=t}for(var n in e)s.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t};t=e("./activateable"),n=e("./index"),i=function(e){function r(){return o=r.__super__.constructor.apply(this,arguments)}return u(r,e),r.include(t),r.prototype.events={click:"active"},r}(n),r.exports=i}).call(this)}}),this.require.define({"spine/controller/list":function(t,e,r){(function(){var t,n,i,o=function(t,e){return function(){return t.apply(e,arguments)}},s={}.hasOwnProperty,u=function(t,e){function r(){this.constructor=t}for(var n in e)s.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t};t=e("./activator"),n=e("./interactionable"),i=function(t){function e(){this._refresh=o(this._refresh,this),e.__super__.constructor.apply(this,arguments),this._refresh(this.model.all(),{clear:!0}),this.model.bind("refresh",this._refresh)}return u(e,t),e.prototype.itemRenderer=n,e.prototype.className="list",e.prototype.autoActivate=!0,e.prototype.containerClass="list-container",e.prototype._refresh=function(t,e){var r,n,i,o,s,u,a,c,l,h;if(null==e&&(e={}),r=this.children(),e.clear)for(c=this.children(),o=0,u=c.length;u>o;o++)n=c[o],n.release();for(s=0,a=t.length;a>s;s++)i=t[s],this.append(new this.itemRenderer({item:i}));return this.autoActivate&&(e.clear||1>r.length)?null!=(l=this.children())?null!=(h=l[0])?h.active():void 0:void 0:void 0},e}(t),r.exports=i}).call(this)}}),this.require.define({"spine/controller/routeable":function(t,e,r){(function(){var t,n;t=e("../route"),n={route:function(e,r){return t.add(e,this.proxy(r))},routes:function(t){var e,r,n;n=[];for(e in t)r=t[e],n.push(this.route(e,r));return n},navigate:function(){return t.navigate.apply(t,arguments)}},r!==void 0&&null!==r&&(r.exports=n)}).call(this)}}),this.require.define({"spine/controller/scrollable":function(t,e,r){(function(){var t,n,i,o=function(t,e){return function(){return t.apply(e,arguments)}},s={}.hasOwnProperty,u=function(t,e){function r(){this.constructor=t}for(var n in e)s.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t};n=e("./"),t=e("./container"),i=function(e){function r(){this.release=o(this.release,this),this.scrollUpdate=o(this.scrollUpdate,this),r.__super__.constructor.apply(this,arguments),this.el.perfectScrollbar()}return u(r,e),r.prototype.className="scrollable",r.prototype.containerClass="scrollable-container",r.include(t),r.prototype.scrollUpdate=function(){return this.el.perfectScrollbar("update")},r.prototype.release=function(){return r.__super__.release.apply(this,arguments),this.el.perfectScrollbar("destroy")},r}(n),r.exports=i}).call(this)}}),this.require.define({"spine/controller/stack":function(t,e,r){(function(){var t,n,i={}.hasOwnProperty,o=function(t,e){function r(){this.constructor=t}for(var n in e)i.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t};t=e("./activator"),n=function(t){function r(){var t,e,n,i,o=this;r.__super__.constructor.apply(this,arguments),i=this.routes,n=function(t,e){var r;return r=function(){var e;return(e=o[t]).active.apply(e,arguments)},o.route(e,r)};for(t in i)e=i[t],n(t,e);this["default"]&&this[this["default"]].active()}return o(r,t),r.include(e("./routeable")),r.prototype.routes={},r.prototype.className="stack",r.prototype.children=function(t){return null==t&&(t=function(t){return t.activate}),r.__super__.children.call(this,t)},r}(t),r.exports=n}).call(this)}}),this.require.define({"spine/controller/tabs":function(t,e,r){(function(){var t,n,i,o,s,u,a=function(t,e){return function(){return t.apply(e,arguments)}},c={}.hasOwnProperty,l=function(t,e){function r(){this.constructor=t}for(var n in e)c.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t};o=e("./stack"),t=e("./activator"),i=e("./interactionable"),n=e("./container"),s=function(e){function r(){return this._release=a(this._release,this),this._activate=a(this._activate,this),u=r.__super__.constructor.apply(this,arguments)}return l(r,e),r.prototype.className="tabbed",r.prototype.containerClass="tabbed-container",r.prototype.tabContainerClass="tabs-container",r.prototype.tabContainer=t,r.prototype.tabRenderer=i,r.prototype.init=function(){return this.tabs=new this.tabContainer({className:this.tabContainerClass}),this.append(this.tabs),r.__super__.init.apply(this,arguments),this.bind("activate",this._activate)},r.prototype.append=function(){var t,e;return r.__super__.append.apply(this,arguments),this.container?((e=this.tabs).append.apply(e,function(){var e,r,n;for(n=[],e=0,r=arguments.length;r>e;e++)t=arguments[e],n.push((t.tab=new this.tabRenderer({panel:t,template:this.tabTemplate})).render());return n}.apply(this,arguments)),this):this},r.prototype._activate=function(t){var e,r;return e=$(t.target).controller(),null!=(r=e[e.parent()===this?"tab":"panel"])&&"function"==typeof r.active&&r.active({silent:!0}),e},r.prototype._release=function(){var t,e;return null!=(e=(t=r.__super__._release.apply(this,arguments)).tab)&&e.release(),t},r}(o),r.exports=s}).call(this)}}),this.require.define({"spine/core/events":function(t,e,r){(function(){var t,e=[].slice;t={bind:function(t,e){var r,n,i,o,s;for(n=t.split(" "),r=this.hasOwnProperty("_callbacks")&&this._callbacks||(this._callbacks={}),o=0,s=n.length;s>o;o++)i=n[o],r[i]||(r[i]=[]),r[i].push(e);return this},one:function(t,e){return this.bind(t,function(){return this.unbind(t,arguments.callee),e.apply(this,arguments)})},trigger:function(){var t,r,n,i,o,s,u;if(t=arguments.length>=1?e.call(arguments,0):[],n=t.shift(),i=this.hasOwnProperty("_callbacks")&&(null!=(u=this._callbacks)?u[n]:void 0)){for(o=0,s=i.length;s>o&&(r=i[o],r.apply(this,t)!==!1);o++);return!0}},listenTo:function(t,e,r){return t.bind(e,r),this.listeningTo||(this.listeningTo=[]),this.listeningTo.push(t),this},listenToOnce:function(t,e,r){return t.one(e,r),this},stopListening:function(t,e,r){var n,i,o,s;if(!t){for(s=this.listeningTo,i=0,o=s.length;o>i;i++)t=s[i],t.unbind();return this.listeningTo=void 0}return t.unbind(e,r),n=this.listeningTo.indexOf(t),-1!==n?this.listeningTo.splice(n,1):void 0},unbind:function(t,e){var r,n,i,o,s,u,a,c,l,h;if(!t)return this._callbacks={},this;for(n=t.split(" "),u=0,c=n.length;c>u;u++)if(s=n[u],o=null!=(h=this._callbacks)?h[s]:void 0)if(e){for(i=a=0,l=o.length;l>a;i=++a)if(r=o[i],r===e){o=o.slice(),o.splice(i,1),this._callbacks[s]=o;break}}else delete this._callbacks[s];return this}},t.on=t.bind,t.off=t.unbind,r.exports=t}).call(this)}}),this.require.define({"spine/core/jquerable":function(t,e,r){(function(){var t,e;e=["bind","unbind","one","trigger","addClass","removeClass","animate","css"],t={included:function(){var t,r,n,i,o=this;for(i=[],r=0,n=e.length;n>r;r++)t=e[r],i.push(function(t){return o.prototype[t]=function(){var e;return(e=this.el)[t].apply(e,arguments),this}}(t));return i}},r.exports=t}).call(this)}}),this.require.define({"spine/core/module":function(t,e,r){(function(){var t,e,n=[].indexOf||function(t){for(var e=0,r=this.length;r>e;e++)if(e in this&&this[e]===t)return e;return-1};e=["included","extended"],t=function(){function t(){"function"==typeof this.init&&this.init.apply(this,arguments)}return t.include=function(t){var r,i,o;if(!t)throw Error("include(obj) requires obj");for(r in t)i=t[r],0>n.call(e,r)&&(this.prototype[r]=i);return null!=(o=t.included)&&o.apply(this),this},t.extend=function(t){var r,i,o;if(!t)throw Error("extend(obj) requires obj");for(r in t)i=t[r],0>n.call(e,r)&&(this[r]=i);return null!=(o=t.extended)&&o.apply(this),this},t.proxy=function(t){var e=this;return function(){return t.apply(e,arguments)}},t.prototype.proxy=function(t){var e=this;return function(){return t.apply(e,arguments)}},t}(),r.exports=t}).call(this)}}),this.require.define({"spine/index":function(t,e,r){(function(){var t;t=this.Spine={},r.exports=t}).call(this)}}),this.require.define({"spine/model/ajax/ajax":function(t,e,r){(function(){var t,e,n=[].slice;e=$({}),t={getURL:function(t){return("function"==typeof t.url?t.url():void 0)||t.url},getCollectionURL:function(t){return t?"function"==typeof t.url?this.generateURL(t):t.url:void 0},getScope:function(t){return("function"==typeof t.scope?t.scope():void 0)||t.scope},generateURL:function(){var e,r,i,o,s;return i=arguments[0],e=arguments.length>=2?n.call(arguments,1):[],i.className?(r=i.className.toLowerCase()+"s",s=t.getScope(i)):(r="string"==typeof i.constructor.url?i.constructor.url:i.constructor.className.toLowerCase()+"s",s=t.getScope(i)||t.getScope(i.constructor)),e.unshift(r),e.unshift(s),o=e.join("/"),o=o.replace(/(\/\/)/g,"/"),o=o.replace(/^\/|\/$/g,""),0!==o.indexOf("../")?t.host+"/"+o:o},enabled:!0,host:"",disable:function(t){var e;if(!this.enabled)return t();this.enabled=!1;try{return t()}catch(r){throw e=r}finally{this.enabled=!0}},queue:function(t){return t?e.queue(t):e.queue()},clearQueue:function(){return this.queue([])}},r.exports=t}).call(this)}}),this.require.define({"spine/model/ajax/base":function(t,e,r){(function(){var t,n;t=e("./ajax"),n=function(){function e(){}return e.prototype.defaults={contentType:"application/json",dataType:"json",processData:!1,headers:{"X-Requested-With":"XMLHttpRequest"}},e.prototype.queue=t.queue,e.prototype.ajax=function(t,e){return $.ajax(this.ajaxSettings(t,e))},e.prototype.ajaxQueue=function(e,r){var n,i,o,s,u;return i=null,n=$.Deferred(),o=n.promise(),t.enabled?(u=this.ajaxSettings(e,r),s=function(t){return i=$.ajax(u).done(n.resolve).fail(n.reject).then(t,t)},o.abort=function(t){var e;return i?i.abort(t):(e=$.inArray(s,this.queue()),e>-1&&this.queue().splice(e,1),n.rejectWith(u.context||u,[o,t,""]),o)},this.queue(s),o):o},e.prototype.ajaxSettings=function(t,e){return $.extend({},this.defaults,e,t)},e}(),r.exports=n}).call(this)}}),this.require.define({"spine/model/ajax/collection":function(t,e,r){(function(){var t,n,i,o=function(t,e){return function(){return t.apply(e,arguments)}},s={}.hasOwnProperty,u=function(t,e){function r(){this.constructor=t}for(var n in e)s.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t};n=e("./base"),t=e("./ajax"),i=function(e){function r(t){this.model=t,this.failResponse=o(this.failResponse,this),this.recordsResponse=o(this.recordsResponse,this)}return u(r,e),r.prototype.find=function(e,r){var n;return n=new this.model({id:e}),this.ajaxQueue(r,{type:"GET",url:t.getURL(n)}).done(this.recordsResponse).fail(this.failResponse)},r.prototype.all=function(e){return this.ajaxQueue(e,{type:"GET",url:t.getURL(this.model)}).done(this.recordsResponse).fail(this.failResponse)},r.prototype.fetch=function(t,e){var r,n=this;return null==t&&(t={}),null==e&&(e={}),(r=t.id)?(delete t.id,this.find(r,t).done(function(t){return n.model.refresh(t,e)})):this.all(t).done(function(t){return n.model.refresh(t,e)})},r.prototype.recordsResponse=function(t,e,r){return this.model.trigger("ajaxSuccess",null,e,r)},r.prototype.failResponse=function(t,e,r){return this.model.trigger("ajaxError",null,t,e,r)},r}(n),r.exports=i}).call(this)}}),this.require.define({"spine/model/ajax/index":function(t,e,r){(function(){var t,n,i,o,s,u,a,c=[].slice;t=e("./ajax"),i=e("./base"),a=e("./singleton"),o=e("./collection"),u={ajax:function(){return new a(this)},url:function(){var e;return e=arguments.length>=1?c.call(arguments,0):[],e.unshift(encodeURIComponent(this.id)),t.generateURL.apply(t,[this].concat(c.call(e)))}},s={ajax:function(){return new o(this)},url:function(){var e;return e=arguments.length>=1?c.call(arguments,0):[],t.generateURL.apply(t,[this].concat(c.call(e)))}},n={extended:function(){return this.extend(s),this.include(u)},ajaxFetch:function(){var t;return(t=this.ajax()).fetch.apply(t,arguments)},ajaxChange:function(t,e,r){return null==r&&(r={}),r.ajax!==!1?t.ajax()[e](r.ajax,r):void 0}},r.exports=n}).call(this)}}),this.require.define({"spine/model/ajax/singleton":function(t,e,r){(function(){var t,n,i,o,s=function(t,e){return function(){return t.apply(e,arguments)}},u={}.hasOwnProperty,a=function(t,e){function r(){this.constructor=t}for(var n in e)u.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t};n=e("./base"),t=e("./ajax"),i=e("../../utils/helper"),o=function(e){function r(t){this.record=t,this.failResponse=s(this.failResponse,this),this.recordResponse=s(this.recordResponse,this),this.model=this.record.constructor}return a(r,e),r.prototype.reload=function(e,r){return this.ajaxQueue(e,{type:"GET",url:t.getURL(this.record)}).done(this.recordResponse(r)).fail(this.failResponse(r))},r.prototype.create=function(e,r){return this.ajaxQueue(e,{type:"POST",data:JSON.stringify(this.record),url:t.getURL(this.model)}).done(this.recordResponse(r)).fail(this.failResponse(r))},r.prototype.update=function(e,r){return this.ajaxQueue(e,{type:"PUT",data:JSON.stringify(this.record),url:t.getURL(this.record)}).done(this.recordResponse(r)).fail(this.failResponse(r))},r.prototype.destroy=function(e,r){return this.ajaxQueue(e,{type:"DELETE",url:t.getURL(this.record)}).done(this.recordResponse(r)).fail(this.failResponse(r))},r.prototype.recordResponse=function(e){var r=this;return null==e&&(e={}),function(n,o,s){var u,a;return n=i.isBlank(n)?!1:r.model.fromJSON(n),t.disable(function(){return n?(n.id&&r.record.id!==n.id&&r.record.changeID(n.id),r.record.updateAttributes(n.attributes(),{fromAjax:!0})):void 0}),r.record.trigger("ajaxSuccess",n,o,s),null!=(u=e.success)&&u.apply(r.record),null!=(a=e.done)?a.apply(r.record):void 0}},r.prototype.failResponse=function(t){var e=this;return null==t&&(t={}),function(r,n,i){var o,s;return e.record.trigger("ajaxError",r,n,i),null!=(o=t.error)&&o.apply(e.record),null!=(s=t.fail)?s.apply(e.record):void 0}},r}(n),r.exports=o}).call(this)}}),this.require.define({"spine/model/index":function(t,e,r){(function(){var t,n,i,o,s,u,a,c,l,h={}.hasOwnProperty,p=function(t,e){function r(){this.constructor=t}for(var n in e)h.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t},f=[].slice,d=[].indexOf||function(t){for(var e=0,r=this.length;r>e;e++)if(e in this&&this[e]===t)return e;return-1};t=e("../core/events"),n=e("../utils/helper"),o=e("../core/module"),c=n.keys,u=n.isArray,a=n.isBlank,l=n.makeArray,s=n.createObject,i=function(e){function r(t){r.__super__.constructor.apply(this,arguments),t&&this.load(t),this.cid=this.constructor.uid("c-")}return p(r,e),r.extend(t),r.records=[],r.irecords={},r.crecords={},r.attributes=[],r.configure=function(){var t,e;return e=arguments[0],t=arguments.length>=2?f.call(arguments,1):[],this.className=e,this.deleteAll(),t.length&&(this.attributes=t),this.attributes&&(this.attributes=l(this.attributes)),this.attributes||(this.attributes=[]),this.unbind(),this},r.toString=function(){return""+this.className+"("+this.attributes.join(", ")+")"},r.find=function(t){var e;if(e=this.exists(t),!e)throw Error('"'+this.className+'" model could not find a record for the ID "'+t+'"');return e},r.exists=function(t){var e,r;return null!=(e=null!=(r=this.records[t])?r:this.irecords[t])?e.clone():void 0},r.refresh=function(t,e){var r,n,i,o,s;for(null==e&&(e={}),e.clear&&this.deleteAll(),n=this.fromJSON(t),u(n)||(n=[n]),o=0,s=n.length;s>o;o++)r=n[o],r.id&&this.irecords[r.id]?this.records[this.records.indexOf(this.irecords[r.id])]=r:(r.id||(r.id=r.cid),this.records.push(r)),this.irecords[r.id]=r,this.crecords[r.cid]=r;return this.sort(),i=this.cloneArray(n),e.silent||this.trigger("refresh",this.cloneArray(n),e),i},r.select=function(t){var e,r,n,i,o;for(i=this.records,o=[],r=0,n=i.length;n>r;r++)e=i[r],t(e)&&o.push(e.clone());return o},r.findByAttribute=function(t,e){var r,n,i,o;for(o=this.records,n=0,i=o.length;i>n;n++)if(r=o[n],r[t]===e)return r.clone();return null},r.findAllByAttribute=function(t,e){return this.select(function(r){return r[t]===e})},r.each=function(t){var e,r,n,i,o;for(i=this.records,o=[],r=0,n=i.length;n>r;r++)e=i[r],o.push(t(e.clone()));return o},r.all=function(){return this.cloneArray(this.records)},r.first=function(){var t;return null!=(t=this.records[0])?t.clone():void 0},r.last=function(){var t;return null!=(t=this.records[this.records.length-1])?t.clone():void 0},r.count=function(){return this.records.length},r.deleteAll=function(){return this.records=[],this.irecords={},this.crecords={}},r.destroyAll=function(t){var e,r,n,i,o;for(i=this.records,o=[],r=0,n=i.length;n>r;r++)e=i[r],o.push(e.destroy(t));return o},r.update=function(t,e,r){return this.find(t).updateAttributes(e,r)},r.create=function(t,e){var r;return r=new this(t),r.save(e)},r.destroy=function(t,e){return this.find(t).destroy(e)},r.change=function(t){return"function"==typeof t?this.bind("change",t):this.trigger.apply(this,["change"].concat(f.call(arguments)))},r.fetch=function(t){return"function"==typeof t?this.bind("fetch",t):this.trigger.apply(this,["fetch"].concat(f.call(arguments)))},r.toJSON=function(){return this.records},r.fromJSON=function(t){var e,r,n,i;if(t){if("string"==typeof t&&(t=JSON.parse(t)),u(t)){for(i=[],r=0,n=t.length;n>r;r++)e=t[r],i.push(new this(e));return i}return new this(t)}},r.fromForm=function(){var t;return(t=new this).fromForm.apply(t,arguments)},r.sort=function(){return this.comparator&&this.records.sort(this.comparator),this.records},r.cloneArray=function(t){var e,r,n,i;for(i=[],r=0,n=t.length;n>r;r++)e=t[r],i.push(e.clone());return i},r.idCounter=0,r.uid=function(t){var e;return null==t&&(t=""),e=t+this.idCounter++,this.exists(e)&&(e=this.uid(t)),e},r.prototype.isNew=function(){return!this.exists()},r.prototype.isValid=function(){return!this.validate()},r.prototype.validate=function(){},r.prototype.load=function(t){var e,r;t.id&&(this.id=t.id);for(e in t)r=t[e],t.hasOwnProperty(e)&&"function"==typeof this[e]?this[e](r):this[e]=r;return this},r.prototype.attributes=function(){var t,e,r,n,i;for(e={},i=this.constructor.attributes,r=0,n=i.length;n>r;r++)t=i[r],t in this&&(e[t]="function"==typeof this[t]?this[t]():this[t]);return this.id&&(e.id=this.id),e},r.prototype.eql=function(t){return!!(t&&t.constructor===this.constructor&&t.cid===this.cid||t.id&&t.id===this.id)},r.prototype.save=function(t){var e,r;return null==t&&(t={}),t.validate!==!1&&(e=this.validate())?(this.trigger("error",e),!1):(this.trigger("beforeSave",t),r=this.isNew()?this.create(t):this.update(t),this.stripCloneAttrs(),this.trigger("save",t),r)},r.prototype.stripCloneAttrs=function(){var t,e;if(!this.hasOwnProperty("cid")){for(t in this)h.call(this,t)&&(e=this[t],this.constructor.attributes.indexOf(t)>-1&&delete this[t]);return this}},r.prototype.updateAttribute=function(t,e,r){var n;return n={},n[t]=e,this.updateAttributes(n,r)},r.prototype.updateAttributes=function(t,e){return this.load(t),this.save(e)},r.prototype.changeID=function(t){var e;return e=this.constructor.irecords,e[t]=e[this.id],delete e[this.id],this.id=t,this.save({changedID:!0})},r.prototype.destroy=function(t){var e,r,n,i,o;for(null==t&&(t={}),this.trigger("beforeDestroy",t),n=this.constructor.records.slice(0),e=i=0,o=n.length;o>i;e=++i)if(r=n[e],this.eql(r)){n.splice(e,1);break}return this.constructor.records=n,delete this.constructor.irecords[this.id],delete this.constructor.crecords[this.cid],this.destroyed=!0,this.trigger("destroy",t),this.trigger("change","destroy",t),this.listeningTo&&this.stopListening(),this.unbind(),this},r.prototype.dup=function(t){var e;return e=new this.constructor(this.attributes()),t===!1?e.cid=this.cid:delete e.id,e},r.prototype.clone=function(){return s(this)},r.prototype.reload=function(){var t;return this.isNew()?this:(t=this.constructor.find(this.id),this.load(t.attributes()),t)},r.prototype.toJSON=function(){return this.attributes()},r.prototype.toString=function(){return"<"+this.constructor.className+" ("+JSON.stringify(this)+")>"},r.prototype.fromForm=function(t){var e,r,n,i,o;for(r={},o=$(t).serializeArray(),n=0,i=o.length;i>n;n++)e=o[n],r[e.name]=e.value;return this.load(r)},r.prototype.exists=function(){return this.constructor.exists(this.id)},r.prototype.update=function(t){var e,r;return this.trigger("beforeUpdate",t),r=this.constructor.irecords,r[this.id].load(this.attributes()),this.constructor.sort(),e=r[this.id].clone(),e.trigger("update",t),e.trigger("change","update",t),e},r.prototype.create=function(t){var e,r;return this.trigger("beforeCreate",t),this.id||(this.id=this.cid),r=this.dup(!1),this.constructor.records.push(r),this.constructor.irecords[this.id]=r,this.constructor.crecords[this.cid]=r,this.constructor.sort(),e=r.clone(),e.trigger("create",t),e.trigger("change","create",t),e},r.prototype.bind=function(t,e){var r,n,i,o,s,u,a=this;for(this.constructor.bind(t,r=function(t){return t&&a.eql(t)?e.apply(a,arguments):void 0}),u=t.split(" "),i=function(t){var n;return a.constructor.bind("unbind",n=function(i,o,s){if(i&&a.eql(i)){if(o&&o!==t)return;if(s&&s!==e)return;return a.constructor.unbind(t,r),a.constructor.unbind("unbind",n)}})},o=0,s=u.length;s>o;o++)n=u[o],i(n);return this},r.prototype.one=function(t,e){var r,n=this;return this.bind(t,r=function(){return n.unbind(t,r),e.apply(n,arguments)})},r.prototype.trigger=function(){var t,e;return t=arguments.length>=1?f.call(arguments,0):[],t.splice(1,0,this),(e=this.constructor).trigger.apply(e,t)},r.prototype.listenTo=function(t,e,r){return t.bind(e,r),this.listeningTo||(this.listeningTo=[]),this.listeningTo.push(t)},r.prototype.listenToOnce=function(t,e,r){var n,i;return i=this.listeningToOnce||(this.listeningToOnce=[]),i.push(t),t.bind(e,n=function(){var o;return o=i.indexOf(t),-1!==o&&i.splice(o,1),t.unbind(e,n),r.apply(t,arguments)})},r.prototype.stopListening=function(t,e,r){var n,i,o,s,u,a,c,l,h,p,f,y,g;if(0===arguments.length){for(o=[],p=[this.listeningTo,this.listeningToOnce],s=0,c=p.length;c>s;s++)if(i=p[s])for(f=this.listeningTo,u=0,l=f.length;l>u;u++)t=f[u],d.call(o,t)>=0||(t.unbind(),o.push(t));return this.listeningTo=void 0,this.listeningToOnce=void 0,void 0}if(t){for(e||t.unbind(),e&&t.unbind(e,r),y=[this.listeningTo,this.listeningToOnce],g=[],a=0,h=y.length;h>a;a++)i=y[a],i&&(n=i.indexOf(t),-1!==n?g.push(i.splice(n,1)):g.push(void 0));return g}},r.prototype.unbind=function(t,e){var r,n,i,o,s;if(0===arguments.length)return this.trigger("unbind");if(t){for(o=t.split(" "),s=[],n=0,i=o.length;i>n;n++)r=o[n],s.push(this.trigger("unbind",r,e));return s}},r}(o),i.prototype.on=i.prototype.bind,i.prototype.off=i.prototype.unbind,r.exports=i}).call(this)}}),this.require.define({"spine/model/local":function(t,e,r){(function(){var t;t={extended:function(){return this.change(this.saveLocal),this.fetch(this.loadLocal)},saveLocal:function(){return localStorage[this.className]=JSON.stringify(this),this.trigger("saveLocal")},loadLocal:function(){var t;return t=localStorage[this.className],this.refresh(t||[],$.extend({},options,{clear:!0})),this.trigger("loadLocal")}},r.exports=t}).call(this)}}),this.require.define({"spine/route/index":function(t,e,r){(function(){var t,n,i,o,s,u,a,c={}.hasOwnProperty,l=function(t,e){function r(){this.constructor=t}for(var n in e)c.call(e,n)&&(t[n]=e[n]);return r.prototype=e.prototype,t.prototype=new r,t.__super__=e.prototype,t},h=[].slice;t=e("../core/events"),n=e("../core/module"),s=/^#*/,u=/:([\w\d]+)/g,a=/\*([\w\d]+)/g,o=/[-[\]{}()+?.,\\^$|#\s]/g,i=function(e){function r(t,e){var r;if(this.path=t,this.callback=e,this.names=[],"string"==typeof t){for(u.lastIndex=0;null!==(r=u.exec(t));)this.names.push(r[1]);for(a.lastIndex=0;null!==(r=a.exec(t));)this.names.push(r[1]);t=t.replace(o,"\\$&").replace(u,"([^/]*)").replace(a,"(.*?)"),this.route=RegExp("^"+t+"$")}else this.route=t}var n;return l(r,e),r.extend(t),r.historySupport=null!=(null!=(n=window.history)?n.pushState:void 0),r.routes=[],r.options={trigger:!0,history:!1,shim:!1,replace:!1},r.add=function(t,e){var r,n,i;if("object"!=typeof t||t instanceof RegExp)return this.routes.push(new this(t,e));i=[];for(r in t)n=t[r],i.push(this.add(r,n));return i},r.setup=function(t){return null==t&&(t={}),this.options=$.extend({},this.options,t),this.options.history&&(this.history=this.historySupport&&this.options.history),this.options.shim?void 0:(this.history?$(window).bind("popstate",this.change):$(window).bind("hashchange",this.change),this.change())},r.unbind=function(){return this.options.shim?void 0:this.history?$(window).unbind("popstate",this.change):$(window).unbind("hashchange",this.change)},r.navigate=function(){var t,e,r,n;return t=arguments.length>=1?h.call(arguments,0):[],r={},e=t[t.length-1],"object"==typeof e?r=t.pop():"boolean"==typeof e&&(r.trigger=t.pop()),r=$.extend({},this.options,r),n=t.join("/"),this.path===n||(this.path=n,this.trigger("navigate",this.path),r.trigger&&this.matchRoute(this.path,r),r.shim)?void 0:this.history&&r.replace?history.replaceState({},document.title,this.path):this.history?history.pushState({},document.title,this.path):window.location.hash=this.path},r.getPath=function(){var t;return this.history?(t=window.location.pathname,"/"!==t.substr(0,1)&&(t="/"+t)):(t=window.location.hash,t=t.replace(s,"")),t},r.getHost=function(){return""+window.location.protocol+"//"+window.location.host},r.change=function(){var t;return t=this.getPath(),t!==this.path?(this.path=t,this.matchRoute(this.path)):void 0},r.matchRoute=function(t,e){var r,n,i,o;for(o=this.routes,n=0,i=o.length;i>n;n++)if(r=o[n],r.match(t,e))return this.trigger("change",r,t),r},r.prototype.match=function(t,e){var r,n,i,o,s,u;if(null==e&&(e={}),n=this.route.exec(t),!n)return!1;if(e.match=n,o=n.slice(1),this.names.length)for(r=s=0,u=o.length;u>s;r=++s)i=o[r],e[this.names[r]]=i;return this.callback.call(null,e)!==!1},r}(n),i.change=i.proxy(i.change),r.exports=i}).call(this)}}),this.require.define({"spine/utils/helper":function(t,e,r){(function(){var t;t={createObject:Object.create||function(t){var e;
return e=function(){},e.prototype=t,new e},isArray:function(t){return"[object Array]"===Object.prototype.toString.call(t)},isBlank:function(t){var e;if(!t)return!0;for(e in t)return!1;return!0},makeArray:function(t){return Array.prototype.slice.call(t,0)},keys:Object.keys||function(t){var e,r,n;n=[];for(e in t)r=t[e],n.push(e);return n}},r.exports=t}).call(this)}});

jade = (function(exports){
/*!
 * Jade - runtime
 * Copyright(c) 2010 TJ Holowaychuk <tj@vision-media.ca>
 * MIT Licensed
 */

/**
 * Lame Array.isArray() polyfill for now.
 */

if (!Array.isArray) {
  Array.isArray = function(arr){
    return '[object Array]' == Object.prototype.toString.call(arr);
  };
}

/**
 * Lame Object.keys() polyfill for now.
 */

if (!Object.keys) {
  Object.keys = function(obj){
    var arr = [];
    for (var key in obj) {
      if (obj.hasOwnProperty(key)) {
        arr.push(key);
      }
    }
    return arr;
  }
}

/**
 * Merge two attribute objects giving precedence
 * to values in object `b`. Classes are special-cased
 * allowing for arrays and merging/joining appropriately
 * resulting in a string.
 *
 * @param {Object} a
 * @param {Object} b
 * @return {Object} a
 * @api private
 */

exports.merge = function merge(a, b) {
  var ac = a['class'];
  var bc = b['class'];

  if (ac || bc) {
    ac = ac || [];
    bc = bc || [];
    if (!Array.isArray(ac)) ac = [ac];
    if (!Array.isArray(bc)) bc = [bc];
    ac = ac.filter(nulls);
    bc = bc.filter(nulls);
    a['class'] = ac.concat(bc).join(' ');
  }

  for (var key in b) {
    if (key != 'class') {
      a[key] = b[key];
    }
  }

  return a;
};

/**
 * Filter null `val`s.
 *
 * @param {Mixed} val
 * @return {Mixed}
 * @api private
 */

function nulls(val) {
  return val != null;
}

/**
 * Render the given attributes object.
 *
 * @param {Object} obj
 * @param {Object} escaped
 * @return {String}
 * @api private
 */

exports.attrs = function attrs(obj, escaped){
  var buf = []
    , terse = obj.terse;

  delete obj.terse;
  var keys = Object.keys(obj)
    , len = keys.length;

  if (len) {
    buf.push('');
    for (var i = 0; i < len; ++i) {
      var key = keys[i]
        , val = obj[key];

      if ('boolean' == typeof val || null == val) {
        if (val) {
          terse
            ? buf.push(key)
            : buf.push(key + '="' + key + '"');
        }
      } else if (0 == key.indexOf('data') && 'string' != typeof val) {
        buf.push(key + "='" + JSON.stringify(val) + "'");
      } else if ('class' == key && Array.isArray(val)) {
        buf.push(key + '="' + exports.escape(val.join(' ')) + '"');
      } else if (escaped && escaped[key]) {
        buf.push(key + '="' + exports.escape(val) + '"');
      } else {
        buf.push(key + '="' + val + '"');
      }
    }
  }

  return buf.join(' ');
};

/**
 * Escape the given string of `html`.
 *
 * @param {String} html
 * @return {String}
 * @api private
 */

exports.escape = function escape(html){
  return String(html)
    .replace(/&(?!(\w+|\#\d+);)/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
};

/**
 * Re-throw the given `err` in context to the
 * the jade in `filename` at the given `lineno`.
 *
 * @param {Error} err
 * @param {String} filename
 * @param {String} lineno
 * @api private
 */

exports.rethrow = function rethrow(err, filename, lineno){
  if (!filename) throw err;

  var context = 3
    , str = require('fs').readFileSync(filename, 'utf8')
    , lines = str.split('\n')
    , start = Math.max(lineno - context, 0)
    , end = Math.min(lines.length, lineno + context);

  // Error context
  var context = lines.slice(start, end).map(function(line, i){
    var curr = i + start + 1;
    return (curr == lineno ? '  > ' : '    ')
      + curr
      + '| '
      + line;
  }).join('\n');

  // Alter exception message
  err.path = filename;
  err.message = (filename || 'Jade') + ':' + lineno
    + '\n' + context + '\n\n' + err.message;
  throw err;
};

  return exports;

})({});
