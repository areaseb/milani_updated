/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!************************************************************!*\
  !*** ./platform/packages/slug/resources/assets/js/slug.js ***!
  \************************************************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
var SlugBoxManagement = /*#__PURE__*/function () {
  function SlugBoxManagement() {
    _classCallCheck(this, SlugBoxManagement);
  }
  _createClass(SlugBoxManagement, [{
    key: "init",
    value: function init() {
      var $slugBox = $('#edit-slug-box');
      $(document).on('click', '#change_slug', function (event) {
        $('.default-slug').unwrap();
        var $slugInput = $('#editable-post-name');
        $slugInput.html('<input type="text" id="new-post-slug" class="form-control" value="' + $slugInput.text() + '" autocomplete="off">');
        $('#edit-slug-box .cancel').show();
        $('#edit-slug-box .save').show();
        $(event.currentTarget).hide();
      });
      $(document).on('click', '#edit-slug-box .cancel', function () {
        var currentSlug = $('#current-slug').val();
        var $permalink = $('#sample-permalink');
        $permalink.html('<a class="permalink" href="' + $('#slug_id').data('view') + currentSlug.replace('/', '') + '">' + $permalink.html() + '</a>');
        $('#editable-post-name').text(currentSlug);
        $('#edit-slug-box .cancel').hide();
        $('#edit-slug-box .save').hide();
        $('#change_slug').show();
      });
      var createSlug = function createSlug(name, id, exist) {
        $.ajax({
          url: $('#slug_id').data('url'),
          type: 'POST',
          data: {
            value: name,
            slug_id: id,
            model: $('input[name=model]').val()
          },
          success: function success(data) {
            var $permalink = $('#sample-permalink');
            var $slugId = $('#slug_id');
            if (exist) {
              $permalink.find('.permalink').prop('href', $slugId.data('view') + data.replace('/', ''));
            } else {
              $permalink.html('<a class="permalink" target="_blank" href="' + $slugId.data('view') + data.replace('/', '') + '">' + $permalink.html() + '</a>');
            }
            $('.page-url-seo p').text($slugId.data('view') + data.replace('/', ''));
            $('#editable-post-name').text(data);
            $('#current-slug').val(data);
            $('#edit-slug-box .cancel').hide();
            $('#edit-slug-box .save').hide();
            $('#change_slug').show();
            $slugBox.removeClass('hidden');
          },
          error: function error(data) {
            Botble.handleError(data);
          }
        });
      };
      $(document).on('click', '#edit-slug-box .save', function () {
        var $slugField = $('#new-post-slug');
        var name = $slugField.val();
        var id = $('#slug_id').data('id');
        if (id == null) {
          id = 0;
        }
        if (name != null && name !== '') {
          createSlug(name, id, false);
        } else {
          $slugField.closest('.form-group').addClass('has-error');
        }
      });
      $(document).on('blur', '#' + $slugBox.data('field-name'), function (e) {
        if ($slugBox.hasClass('hidden')) {
          var value = $(e.currentTarget).val();
          if (value !== null && value !== '') {
            createSlug(value, 0, true);
          }
        }
      });
    }
  }]);
  return SlugBoxManagement;
}();
$(function () {
  new SlugBoxManagement().init();
});
/******/ })()
;