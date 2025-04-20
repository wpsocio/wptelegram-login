import{g as q}from"./_commonjsHelpers-CqkleIqs.js";function h(r,e){for(var t=0;t<e.length;t++){const s=e[t];if(typeof s!="string"&&!Array.isArray(s)){for(const i in s)if(i!=="default"&&!(i in r)){const u=Object.getOwnPropertyDescriptor(s,i);u&&Object.defineProperty(r,i,u.get?u:{enumerable:!0,get:()=>s[i]})}}}return Object.freeze(Object.defineProperty(r,Symbol.toStringTag,{value:"Module"}))}var p={exports:{}},a={},l,w;function P(){return w||(w=1,l=React),l}/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var O;function D(){if(O)return a;O=1;var r=P(),e=Symbol.for("react.element"),t=Symbol.for("react.fragment"),s=Object.prototype.hasOwnProperty,i=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,u={key:!0,ref:!0,__self:!0,__source:!0};function v(_,n,x){var o,c={},f=null,y=null;x!==void 0&&(f=""+x),n.key!==void 0&&(f=""+n.key),n.ref!==void 0&&(y=n.ref);for(o in n)s.call(n,o)&&!u.hasOwnProperty(o)&&(c[o]=n[o]);if(_&&_.defaultProps)for(o in n=_.defaultProps,n)c[o]===void 0&&(c[o]=n[o]);return{$$typeof:e,type:_,key:f,ref:y,props:c,_owner:i.current}}return a.Fragment=t,a.jsx=v,a.jsxs=v,a}var g;function S(){return g||(g=1,p.exports=D()),p.exports}var L=S(),d,j;function k(){return j||(j=1,d=wp.i18n),d}var R=k();const T=q(R),I=h({__proto__:null,default:T},[R]);let b="";const m=R.createI18n,E=(m==null?void 0:m())||I,N=(r,e)=>{b=e,E.setLocaleData(r,e)},A=r=>E.__(r,b),C=(r,e)=>{const t=window[r];return e?t==null?void 0:t[e]:t};export{A as _,R as a,C as g,L as j,P as r,N as s};
//# sourceMappingURL=get-plugin-data-CQBhaGcS.js.map
