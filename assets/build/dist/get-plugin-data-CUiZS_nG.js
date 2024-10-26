var i={exports:{}},s={},u=React;/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var v=u,w=Symbol.for("react.element"),x=Symbol.for("react.fragment"),y=Object.prototype.hasOwnProperty,R=v.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,m={key:!0,ref:!0,__self:!0,__source:!0};function l(r,e,o){var t,n={},_=null,p=null;o!==void 0&&(_=""+o),e.key!==void 0&&(_=""+e.key),e.ref!==void 0&&(p=e.ref);for(t in e)y.call(e,t)&&!m.hasOwnProperty(t)&&(n[t]=e[t]);if(r&&r.defaultProps)for(t in e=r.defaultProps,e)n[t]===void 0&&(n[t]=e[t]);return{$$typeof:w,type:r,key:_,ref:p,props:n,_owner:R.current}}s.Fragment=x;s.jsx=l;s.jsxs=l;i.exports=s;var O=i.exports;let f="";const a=wp.i18n.createI18n,c=(a==null?void 0:a())||wp.i18n,E=(r,e)=>{f=e,c.setLocaleData(r,e)},d=r=>c.__(r,f);var b=wp.i18n.sprintf;const j=(r,e)=>{const o=window[r];return e?o==null?void 0:o[e]:o};export{d as _,b as a,j as g,O as j,E as s};
//# sourceMappingURL=get-plugin-data-CUiZS_nG.js.map
