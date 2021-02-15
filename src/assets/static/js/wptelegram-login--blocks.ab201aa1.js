/*! For license information please see wptelegram-login--blocks.ab201aa1.js.LICENSE.txt */
!function(e){var t={};function r(n){if(t[n])return t[n].exports;var o=t[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,r),o.l=!0,o.exports}r.m=e,r.c=t,r.d=function(e,t,n){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},r.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(r.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)r.d(n,o,function(t){return e[t]}.bind(null,o));return n},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="/",r(r.s=189)}({0:function(e,t){e.exports=window.React},1:function(e,t,r){"use strict";e.exports=r(113)},113:function(e,t,r){"use strict";r(114);var n=r(0),o=60103;if(t.Fragment=60107,"function"===typeof Symbol&&Symbol.for){var i=Symbol.for;o=i("react.element"),t.Fragment=i("react.fragment")}var s=n.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,u=Object.prototype.hasOwnProperty,a={key:!0,ref:!0,__self:!0,__source:!0};function l(e,t,r){var n,i={},l=null,c=null;for(n in void 0!==r&&(l=""+r),void 0!==t.key&&(l=""+t.key),void 0!==t.ref&&(c=t.ref),t)u.call(t,n)&&!a.hasOwnProperty(n)&&(i[n]=t[n]);if(e&&e.defaultProps)for(n in t=e.defaultProps)void 0===i[n]&&(i[n]=t[n]);return{$$typeof:o,type:e,key:l,ref:c,props:i,_owner:s.current}}t.jsx=l,t.jsxs=l},114:function(e,t,r){"use strict";var n=Object.getOwnPropertySymbols,o=Object.prototype.hasOwnProperty,i=Object.prototype.propertyIsEnumerable;function s(e){if(null===e||void 0===e)throw new TypeError("Object.assign cannot be called with null or undefined");return Object(e)}e.exports=function(){try{if(!Object.assign)return!1;var e=new String("abc");if(e[5]="de","5"===Object.getOwnPropertyNames(e)[0])return!1;for(var t={},r=0;r<10;r++)t["_"+String.fromCharCode(r)]=r;if("0123456789"!==Object.getOwnPropertyNames(t).map((function(e){return t[e]})).join(""))return!1;var n={};return"abcdefghijklmnopqrst".split("").forEach((function(e){n[e]=e})),"abcdefghijklmnopqrst"===Object.keys(Object.assign({},n)).join("")}catch(o){return!1}}()?Object.assign:function(e,t){for(var r,u,a=s(e),l=1;l<arguments.length;l++){for(var c in r=Object(arguments[l]))o.call(r,c)&&(a[c]=r[c]);if(n){u=n(r);for(var f=0;f<u.length;f++)i.call(r,u[f])&&(a[u[f]]=r[u[f]])}}return a}},169:function(e,t){e.exports=window.wp.blocks},170:function(e,t,r){"use strict";r.d(t,"a",(function(){return n}));var n=function(e,t){var r;return t?null===(r=window[e])||void 0===r?void 0:r[t]:window[e]}},171:function(e,t){e.exports=window.wp.blockEditor},189:function(e,t,r){e.exports=r(303)},190:function(e,t,r){},28:function(e,t){e.exports=window.wp.i18n},303:function(e,t,r){"use strict";r.r(t);var n=r(28),o=r(169),i={button_style:{type:"string",default:"large"},show_user_photo:{type:"boolean",default:!0},corner_radius:{type:"string",default:"20"},show_if_user_is:{type:"string",default:"0"}},s=r(170),u=function(e){return Object(s.a)("wptelegram_login",e)},a=r(1),l=function(e){var t=e.attributes,r=e.className,n=u("assets"),o=t.button_style,i=t.show_user_photo,s=t.corner_radius,l=null;"small"===o?l="100px":"medium"===o&&(l="150px");var c=null;return"small"===o?c="20px":"medium"===o&&(c="30px"),Object(a.jsxs)("div",{className:r,children:[Object(a.jsx)("img",{src:n.loginImageUrl,style:{borderRadius:s+"px",width:l}}),i?Object(a.jsx)("img",{src:n.loginAvatarUrl,style:{width:c}}):null]})},c=r(0),f=r(171),b=r(69),p=[{label:Object(n.__)("Large"),value:"large"},{label:Object(n.__)("Medium"),value:"medium"},{label:Object(n.__)("Small"),value:"small"}];r(190);Object(o.registerBlockType)("wptelegram/login",{title:Object(n.__)("WP Telegram Login"),icon:"smartphone",category:"wptelegram",attributes:i,edit:function(e){var t=e.attributes,r=e.setAttributes,o=e.className,i=t.button_style,s=t.show_user_photo,d=t.corner_radius,_=t.show_if_user_is,j=u("uiData"),O=Object(c.useCallback)((function(e){return r({button_style:e})}),[r]),m=Object(c.useCallback)((function(e){return r({show_user_photo:e})}),[r]),y=Object(c.useCallback)((function(e){return r({corner_radius:e})}),[r]),g=Object(c.useCallback)((function(e){return r({show_if_user_is:e})}),[r]);return Object(a.jsxs)(a.Fragment,{children:[Object(a.jsx)(f.InspectorControls,{children:Object(a.jsxs)(b.PanelBody,{title:Object(n.__)("Button Settings"),children:[Object(a.jsx)(b.RadioControl,{label:Object(n.__)("Button Style"),selected:i,onChange:O,options:p}),Object(a.jsx)(b.ToggleControl,{label:Object(n.__)("Show User Photo"),checked:s,onChange:m}),Object(a.jsx)(b.TextControl,{label:Object(n.__)("Corner Radius"),value:d,onChange:y,type:"number",min:"0",max:"20"}),Object(a.jsx)(b.SelectControl,{label:Object(n.__)("Show if user is"),value:_,onChange:g,options:j.show_if_user_is})]})}),Object(a.jsx)(l,{attributes:t,className:o})]})},save:function(e){return Object(a.jsx)(l,{attributes:e.attributes,className:null})},deprecated:[{attributes:i,save:function(e){return Object(a.jsx)(l,{attributes:e.attributes,className:null})}}]})},69:function(e,t){e.exports=window.wp.components}});
//# sourceMappingURL=wptelegram-login--blocks.ab201aa1.js.map