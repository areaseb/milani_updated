(()=>{function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}function t(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,a(r.key),r)}}function a(t){var a=function(t,a){if("object"!=e(t)||!t)return t;var n=t[Symbol.toPrimitive];if(void 0!==n){var r=n.call(t,a||"default");if("object"!=e(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===a?String:Number)(t)}(t,"string");return"symbol"==e(a)?a:a+""}var n=function(){return e=function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e)},(a=[{key:"init",value:function(){var e=$("#post_lang_choice");e.data("prev",e.val()),$(document).on("change","#post_lang_choice",(function(e){$(".change_to_language_text").text($(e.currentTarget).find("option:selected").text()),$("#confirm-change-language-modal").modal("show")})),$(document).on("click","#confirm-change-language-modal .btn-warning.float-start",(function(t){t.preventDefault(),(e=$("#post_lang_choice")).val(e.data("prev")).trigger("change"),$("#confirm-change-language-modal").modal("hide")})),$(document).on("click","#confirm-change-language-button",(function(t){t.preventDefault();var a=$(t.currentTarget),n=$("#language_flag_path").val();a.addClass("button-loading"),e=$("#post_lang_choice"),$.ajax({url:$("div[data-change-language-route]").data("change-language-route"),data:{lang_meta_current_language:e.val(),reference_id:$("#reference_id").val(),reference_type:$("#reference_type").val(),lang_meta_created_from:$("#lang_meta_created_from").val()},type:"POST",success:function(t){if($(".active-language").html('<img src="'+n+e.find("option:selected").data("flag")+'.svg" width="16" title="'+e.find("option:selected").text()+'" alt="'+e.find("option:selected").text()+'" />'),!t.error){$(".current_language_text").text(e.find("option:selected").text());var r="";$.each(t.data,(function(e,t){r+='<img src="'+n+t.lang_flag+'.svg" width="16" title="'+t.lang_name+'" alt="'+t.lang_name+'">',t.reference_id?r+='<a href="'+$("#route_edit").val()+'"> '+t.lang_name+' <i class="fa fa-edit"></i> </a><br />':r+='<a href="'+$("#route_create").val()+"?ref_from="+$("#content_id").val()+"&ref_lang="+e+'"> '+t.lang_name+' <i class="fa fa-plus"></i> </a><br />'})),$("#list-others-language").html(r),$("#confirm-change-language-modal").modal("hide"),e.data("prev",e.val()).trigger("change")}a.removeClass("button-loading")},error:function(e){Botble.showError(e.message),a.removeClass("button-loading")}})})),$(document).on("click",".change-data-language-item",(function(e){e.preventDefault(),window.location.href=$(e.currentTarget).find("span[data-href]").data("href")}))}}])&&t(e.prototype,a),n&&t(e,n),Object.defineProperty(e,"prototype",{writable:!1}),e;var e,a,n}();$(document).ready((function(){(new n).init(),$.ajaxSetup({data:{ref_from:$('meta[name="ref_from"]').attr("content"),ref_lang:$('meta[name="ref_lang"]').attr("content")}})}))})();