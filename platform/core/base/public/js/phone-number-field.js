(()=>{function t(n){return t="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},t(n)}function n(t,n){for(var o=0;o<n.length;o++){var r=n[o];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(t,e(r.key),r)}}function e(n){var e=function(n,e){if("object"!=t(n)||!n)return n;var o=n[Symbol.toPrimitive];if(void 0!==o){var r=o.call(n,e||"default");if("object"!=t(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===e?String:Number)(n)}(n,"string");return"symbol"==t(e)?e:e+""}var o=function(){return t=function t(){!function(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}(this,t)},(e=[{key:"init",value:function(){$(document).find(".js-phone-number-mask").each((function(t,n){window.intlTelInput(n,{geoIpLookup:function(t){$.get("https://ipinfo.io",(function(){}),"jsonp").always((function(n){t(n&&n.country?n.country:"")}))},initialCountry:"auto",utilsScript:"/vendor/core/core/base/libraries/intl-tel-input/js/utils.js"})}))}}])&&n(t.prototype,e),o&&n(t,o),Object.defineProperty(t,"prototype",{writable:!1}),t;var t,e,o}();$(document).ready((function(){(new o).init(),document.addEventListener("payment-form-reloaded",(function(){(new o).init()}))}))})();