this.wp=this.wp||{},this.wp.viewport=function(t){var e={};function n(r){if(e[r])return e[r].exports;var i=e[r]={i:r,l:!1,exports:{}};return t[r].call(i.exports,i,i.exports,n),i.l=!0,i.exports}return n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:r})},n.r=function(t){Object.defineProperty(t,"__esModule",{value:!0})},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=204)}({2:function(t,e){!function(){t.exports=this.lodash}()},204:function(t,e,n){"use strict";n.r(e);var r={};n.d(r,"setIsMatching",function(){return a});var i={};n.d(i,"isViewportMatch",function(){return s});var o=n(2),c=n(5);var u=function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},e=arguments.length>1?arguments[1]:void 0;switch(e.type){case"SET_IS_MATCHING":return e.values}return t};function a(t){return{type:"SET_IS_MATCHING",values:t}}function s(t,e){return-1===e.indexOf(" ")&&(e=">= "+e),!!t[e]}Object(c.registerStore)("core/viewport",{reducer:u,actions:r,selectors:i});var f=n(7),p=function(t){return Object(f.createHigherOrderComponent)(Object(c.withSelect)(function(e){return Object(o.mapValues)(t,function(t){return e("core/viewport").isViewportMatch(t)})}),"withViewportMatch")},d=function(t){return Object(f.createHigherOrderComponent)(Object(f.compose)([p({isViewportMatch:t}),Object(f.ifCondition)(function(t){return t.isViewportMatch})]),"ifViewportMatches")};n.d(e,"ifViewportMatches",function(){return d}),n.d(e,"withViewportMatch",function(){return p});var h={"<":"max-width",">=":"min-width"},w=Object(o.debounce)(function(){var t=Object(o.mapValues)(l,function(t){return t.matches});Object(c.dispatch)("core/viewport").setIsMatching(t)},{leading:!0}),l=Object(o.reduce)({huge:1440,wide:1280,large:960,medium:782,small:600,mobile:480},function(t,e,n){return Object(o.forEach)(h,function(r,i){var o=window.matchMedia("(".concat(r,": ").concat(e,"px)"));o.addListener(w);var c=[i,n].join(" ");t[c]=o}),t},{});window.addEventListener("orientationchange",w),w(),w.flush()},5:function(t,e){!function(){t.exports=this.wp.data}()},7:function(t,e){!function(){t.exports=this.wp.compose}()}});