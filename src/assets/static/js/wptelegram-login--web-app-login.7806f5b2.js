!function(){"use strict";function a(a,n){var r=new URL(a);return r.searchParams.has("redirect_to")||r.searchParams.set("redirect_to",function(){var a=new URL(window.location.href);return a.hash="",a.searchParams.delete("action"),a.toString()}()),new URLSearchParams(n).forEach((function(a,n){r.searchParams.set(n,a)})),r.toString()}window.Telegram.WebApp.ready(),function(n,r){var e=n.is_user_logged_in,i=n.login_auth_url,t=n.confirm_login,o=n.i18n;if(!e&&r.initData){var c=function(){window.location.href=a(i,r.initData)};t?r.showPopup(o.popup,(function(a){"login"===a&&c()})):c()}}(window.wptelegram_web_app_data,window.Telegram.WebApp)}();
//# sourceMappingURL=wptelegram-login--web-app-login.7806f5b2.js.map