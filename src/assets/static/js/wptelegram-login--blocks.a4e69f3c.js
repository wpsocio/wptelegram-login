/*! For license information please see wptelegram-login--blocks.a4e69f3c.js.LICENSE.txt */
!function(){"use strict";var e={1725:function(e){var t=Object.getOwnPropertySymbols,r=Object.prototype.hasOwnProperty,n=Object.prototype.propertyIsEnumerable;function o(e){if(null===e||void 0===e)throw new TypeError("Object.assign cannot be called with null or undefined");return Object(e)}e.exports=function(){try{if(!Object.assign)return!1;var e=new String("abc");if(e[5]="de","5"===Object.getOwnPropertyNames(e)[0])return!1;for(var t={},r=0;r<10;r++)t["_"+String.fromCharCode(r)]=r;if("0123456789"!==Object.getOwnPropertyNames(t).map((function(e){return t[e]})).join(""))return!1;var n={};return"abcdefghijklmnopqrst".split("").forEach((function(e){n[e]=e})),"abcdefghijklmnopqrst"===Object.keys(Object.assign({},n)).join("")}catch(o){return!1}}()?Object.assign:function(e,i){for(var s,a,l=o(e),u=1;u<arguments.length;u++){for(var c in s=Object(arguments[u]))r.call(s,c)&&(l[c]=s[c]);if(t){a=t(s);for(var f=0;f<a.length;f++)n.call(s,a[f])&&(l[a[f]]=s[a[f]])}}return l}},6374:function(e,t,r){r(1725);var n=r(9196),o=60103;if(t.Fragment=60107,"function"===typeof Symbol&&Symbol.for){var i=Symbol.for;o=i("react.element"),t.Fragment=i("react.fragment")}var s=n.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,a=Object.prototype.hasOwnProperty,l={key:!0,ref:!0,__self:!0,__source:!0};function u(e,t,r){var n,i={},u=null,c=null;for(n in void 0!==r&&(u=""+r),void 0!==t.key&&(u=""+t.key),void 0!==t.ref&&(c=t.ref),t)a.call(t,n)&&!l.hasOwnProperty(n)&&(i[n]=t[n]);if(e&&e.defaultProps)for(n in t=e.defaultProps)void 0===i[n]&&(i[n]=t[n]);return{$$typeof:o,type:e,key:u,ref:c,props:i,_owner:s.current}}t.jsx=u,t.jsxs=u},184:function(e,t,r){e.exports=r(6374)},9196:function(e){e.exports=window.React}},t={};function r(n){var o=t[n];if(void 0!==o)return o.exports;var i=t[n]={exports:{}};return e[n](i,i.exports,r),i.exports}!function(){function e(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function t(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function n(r){for(var n=1;n<arguments.length;n++){var o=null!=arguments[n]?arguments[n]:{};n%2?t(Object(o),!0).forEach((function(t){e(r,t,o[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(r,Object.getOwnPropertyDescriptors(o)):t(Object(o)).forEach((function(e){Object.defineProperty(r,e,Object.getOwnPropertyDescriptor(o,e))}))}return r}var o,i,s=window.wp.blocks,a=window.wp.i18n,l="",u=a.createI18n,c=(null===u||void 0===u?void 0:u())||a,f=function(e){return c.__(e,l)},p={button_style:{type:"string"},show_user_photo:{type:"boolean"},corner_radius:{type:"string"},show_if_user_is:{type:"string"}},b=n(n({},p),{},{lang:{type:"string"}}),g=function(e){return function(e,t){var r;return t?null===(r=window[e])||void 0===r?void 0:r[t]:window[e]}("wptelegram_login",e)},d=r(184),w=function(e){var t=e.attributes,r=e.className,n=g("assets"),o=t.button_style,i=t.show_user_photo,s=t.corner_radius,a=null;"small"===o?a="100px":"medium"===o&&(a="150px");var l=null;return"small"===o?l="20px":"medium"===o&&(l="30px"),(0,d.jsxs)("div",{className:r,children:[(0,d.jsx)("img",{src:n.loginImageUrl,style:{borderRadius:s+"px",width:a}}),i?(0,d.jsx)("img",{src:n.loginAvatarUrl,style:{width:l}}):null]})},y=window.wp.element,_=window.wp.blockEditor,m=window.wp.components,v=[{label:f("Large"),value:"large"},{label:f("Medium"),value:"medium"},{label:f("Small"),value:"small"}],h=(null===(o=window.wptelegram_login)||void 0===o?void 0:o.savedSettings)||{},j=(null===(i=window.wptelegram_login)||void 0===i?void 0:i.savedSettings)||{};(0,s.registerBlockType)("wptelegram/login",{title:f("WP Telegram Login"),icon:"smartphone",category:"wptelegram",attributes:b,edit:function(t){var r=t.attributes,o=t.setAttributes,i=t.className,s=n(n({},h),r),a=s.button_style,l=s.show_user_photo,u=s.corner_radius,c=s.lang,p=s.show_if_user_is;(0,y.useEffect)((function(){for(var t in h)t in r||o(e({},t,h[t]))}),[]);var b=g("uiData"),j=(0,y.useCallback)((function(e){return o({button_style:e})}),[o]),O=(0,y.useCallback)((function(e){return o({show_user_photo:e})}),[o]),x=(0,y.useCallback)((function(e){return o({corner_radius:e})}),[o]),C=(0,y.useCallback)((function(e){return o({show_if_user_is:e})}),[o]),P=(0,y.useCallback)((function(e){return o({lang:e})}),[o]);return(0,d.jsxs)(d.Fragment,{children:[(0,d.jsx)(_.InspectorControls,{children:(0,d.jsxs)(m.PanelBody,{title:f("Button Settings"),children:[(0,d.jsx)(m.RadioControl,{label:f("Button Style"),selected:a,onChange:j,options:v}),(0,d.jsx)(m.ToggleControl,{label:f("Show User Photo"),checked:l,onChange:O}),(0,d.jsx)(m.TextControl,{label:f("Corner Radius"),value:u,onChange:x,type:"number",min:"0",max:"20"}),(0,d.jsx)(m.SelectControl,{label:f("Language"),value:c,onChange:P,options:b.lang}),(0,d.jsx)(m.SelectControl,{label:f("Show if user is"),value:p,onChange:C,options:b.show_if_user_is})]})}),(0,d.jsx)(w,{attributes:r,className:i})]})},save:function(e){return(0,d.jsx)(w,{attributes:e.attributes,className:null})},deprecated:[{attributes:{button_style:{type:"string",default:"large"},show_user_photo:{type:"boolean",default:!0},corner_radius:{type:"string",default:"20"},show_if_user_is:{type:"string",default:"0"}},save:function(e){return(0,d.jsx)(w,{attributes:e.attributes,className:null})}},{attributes:p,migrate:function(e){return n(n({},j),e)},save:function(e){return(0,d.jsx)(w,{attributes:e.attributes,className:null})}},{attributes:b,migrate:function(e){return n(n({},j),e)},save:function(e){return(0,d.jsx)(w,{attributes:e.attributes,className:null})}}]})}()}();
//# sourceMappingURL=wptelegram-login--blocks.a4e69f3c.js.map