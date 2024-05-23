/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/shipment.js ***!
  \********************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var ShipmentManagement = /*#__PURE__*/function () {
  function ShipmentManagement() {
    _classCallCheck(this, ShipmentManagement);
  }
  return _createClass(ShipmentManagement, [{
    key: "init",
    value: function init() {
      $(document).on('click', '.shipment-actions .dropdown-menu a', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        $('#confirm-change-shipment-status-button').data('target', _self.data('target')).data('status', _self.data('value'));
        var $modal = $('#confirm-change-status-modal');
        $modal.find('.shipment-status-label').text(_self.text().toLowerCase());
        $modal.modal('show');
      });
      $(document).on('click', '#confirm-change-shipment-status-button', function (event) {
        event.preventDefault();
        var _self = $(event.currentTarget);
        _self.addClass('button-loading');
        $.ajax({
          type: 'POST',
          cache: false,
          url: _self.data('target'),
          data: {
            status: _self.data('status')
          },
          success: function success(res) {
            if (!res.error) {
              Botble.showSuccess(res.message);
              $('.max-width-1200').load(window.location.href + ' .max-width-1200 > *', function () {
                $('#confirm-change-status-modal').modal('hide');
                _self.removeClass('button-loading');
              });
            } else {
              Botble.showError(res.message);
              _self.removeClass('button-loading');
            }
          },
          error: function error(res) {
            Botble.handleError(res);
            _self.removeClass('button-loading');
          }
        });
      });
    }
  }]);
}();
$(document).ready(function () {
  new ShipmentManagement().init();
});
/******/ })()
;