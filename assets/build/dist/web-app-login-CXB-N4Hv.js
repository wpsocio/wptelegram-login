window.Telegram.WebApp.ready();function w(){const e=new URL(window.location.href);return e.hash="",e.searchParams.delete("action"),e.toString()}function l(e,r){const a=new URL(e);a.searchParams.has("redirect_to")||a.searchParams.set("redirect_to",w());const i=new URLSearchParams(r);for(const[t,n]of i.entries())a.searchParams.set(t,n);return a.toString()}var o,s;(o=window.wptelegram_login)!=null&&o.web_app_data&&(({is_user_logged_in:e,login_auth_url:r,confirm_login:a,i18n:i},t)=>{if(!e&&t.initData){const n=()=>{window.location.href=l(r,t.initData)};a?t.showPopup(i.popup,c=>{c==="login"&&n()}):n()}})((s=window.wptelegram_login)==null?void 0:s.web_app_data,window.Telegram.WebApp);
//# sourceMappingURL=web-app-login-CXB-N4Hv.js.map