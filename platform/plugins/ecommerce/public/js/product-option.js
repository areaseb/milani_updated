/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};
/*!**************************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/product-option.js ***!
  \**************************************************************************/


function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
$(document).ready(function () {
  var _window$productOption = window.productOptions,
    productOptionLang = _window$productOption.productOptionLang,
    coreBaseLang = _window$productOption.coreBaseLang,
    currentProductOption = _window$productOption.currentProductOption,
    options = _window$productOption.options;
  var productOptionForm = {
    productOptions: currentProductOption,
    init: function init() {
      this.eventListeners();
      this.generateProductOption();
      this.sortable();
    },
    sortable: function sortable() {
      $('.option-value-sortable tbody').sortable({
        stop: function stop() {
          var idsInOrder = $('.option-value-sortable tbody').sortable('toArray', {
            attribute: 'data-index'
          });
          idsInOrder.map(function (id, index) {
            $('.option-row[data-index="' + id + '"]').find('.option-value-order').val(index);
          });
        }
      });
      $('.accordion-product-option').sortable({
        stop: function stop() {
          var idsInOrder = $('.accordion-product-option').sortable('toArray', {
            attribute: 'data-index'
          });
          idsInOrder.map(function (id, index) {
            $('.accordion-item[data-index="' + id + '"]').find('.option-order').val(index);
          });
        }
      });
    },
    generateProductOption: function generateProductOption() {
      var self = this;
      var html = '';
      $('#accordion-product-option').html('');
      this.productOptions.map(function (item, index) {
        html += self.generateOptionTemplate(item, index);
      });
      $('#accordion-product-option').append(html);
      this.sortable();
    },
    eventListeners: function eventListeners() {
      var self = this;
      $('.product-option-form-wrap').on('click', '.add-from-global-option', function () {
        var selectedOption = $('#global-option').val();
        if (selectedOption != -1) {
          self.addFromGlobalOption(selectedOption);
        } else {
          toastr.error(productOptionLang.please_select_option);
        }
        return false;
      }).on('click', '.remove-option', function () {
        var index = $(this).data('index');
        self.productOptions.splice(index, 1);
        $(this).parents('.accordion-item').remove();
      }).on('keyup', '.option-name', function () {
        var index = $(this).parents('.accordion-item').data('product-option-index');
        var name = $(this).val();
        $(this).parents('.accordion-item').find('.accordion-button').text(name);
        self.productOptions[index].name = name;
      }).on('change', '.option-type', function () {
        var index = $(this).parents('.accordion-item').data('product-option-index');
        self.productOptions[index].option_type = $(this).val();
        self.generateProductOption();
      }).on('change', '.option-required', function () {
        var index = $(this).parents('.accordion-item').data('product-option-index');
        self.productOptions[index].required = $(this).is(':checked');
      }).on('click', '.add-new-row', function () {
        self.addNewRow($(this));
      }).on('click', '.remove-row', function () {
        $(this).parent().parent().remove();
      }).on('click', '.add-new-option', function () {
        self.productOptions.push({
          name: '',
          values: [{
            affect_price: 0,
            affect_type: 0
          }],
          option_type: 'N/A',
          required: false
        });
        self.generateProductOption();
      });
    },
    addNewRow: function addNewRow(element) {
      var table = element.parent().find('table tbody');
      var index = element.parents('.accordion-item').data('product-option-index');
      var tr = table.find('tr').last().clone();
      var labelName = 'options[' + index + '][values][' + table.find('tr').length + '][option_value]',
        affectName = 'options[' + index + '][values][' + table.find('tr').length + '][affect_price]',
        affectTypeName = 'options[' + index + '][values][' + table.find('tr').length + '][affect_type]';
      tr.find('.option-label').attr('name', labelName).attr('value', '');
      tr.find('.affect_price').attr('name', affectName).attr('value', '');
      tr.find('.affect_type').attr('name', affectTypeName).attr('value', '');
      tr.find('.option-value-order').val(table.find('tr').length);
      tr.attr('data-index', table.find('tr').length);
      table.append(tr);
    },
    addFromGlobalOption: function addFromGlobalOption(option_id) {
      var self = this;
      fetch(route('global-option.ajaxInfo', {
        id: option_id
      })).then(function (res) {
        return res.json();
      }).then(function (json) {
        self.productOptions.push({
          name: json.name,
          option_type: json.option_type,
          option_value: json.option_value,
          values: json.values,
          required: json.required
        });
        self.generateProductOption();
      });
    },
    generateOptionTemplate: function generateOptionTemplate(option, index) {
      var options = this.generateFieldOptions(option);
      var id = typeof option.id !== 'undefined' ? option.id : 0;
      var order = typeof option.order !== 'undefined' && option.order != 9999 ? option.order : index;
      var template = $('#template-option').html();
      var checked = option.required ? 'checked' : '';
      var values = this.generateOptionValues(option.values, option.option_type, index);
      return template.replace(/__index__/g, index).replace(/__order__/g, order).replace(/__id__/g, id).replace(/__optionName__/g, '#' + (parseInt(index) + 1) + ' ' + option.name).replace(/__nameLabel__/g, coreBaseLang.name).replace(/__option_name__/g, option.name).replace(/__namePlaceHolder__/g, coreBaseLang.name_placeholder).replace(/__optionTypeLabel__/g, productOptionLang.option_type).replace(/__optionTypeOption__/g, options).replace(/__checked__/g, checked).replace(/__requiredLabel__/g, productOptionLang.required).replace(/__optionValueSortable__/g, values);
    },
    generateFieldOptions: function generateFieldOptions(option) {
      var html = '';
      $.each(options, function (key, value) {
        if (_typeof(value) == 'object') {
          html += '<optgroup label="' + key + '">';
          $.each(value, function (option_key, option_value) {
            var option_checked = option.option_type === option_key ? 'selected' : '';
            html += '<option ' + option_checked + ' value="' + option_key + '">' + option_value + '</option>';
          });
          html += '</optgroup>';
        } else {
          var option_checked = option.option_type === key ? 'selected' : '';
          html += '<option ' + option_checked + ' value="' + key + '">' + value + '</option>';
        }
      });
      return html;
    },
    generateOptionValues: function generateOptionValues(values) {
      var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      var index = arguments.length > 2 ? arguments[2] : undefined;
      var label = productOptionLang.label,
        price = productOptionLang.price,
        priceType = productOptionLang.price_type,
        template = '',
        html = '';
      var option_type = type.split("\\");
      option_type = option_type[option_type.length - 1];
      if (option_type != '' && typeof type !== 'undefined' && type != 'N/A') {
        if (option_type == 'Field') {
          template = $('#template-option-values-of-field').html();
          var selectedFixed = values[0].affect_type === 0 ? 'selected' : '';
          var selectedPercent = values[0].affect_type === 1 ? 'selected' : '';
          html += template.replace(/__priceLabel__/g, price).replace(/__priceTypeLabel__/g, priceType).replace(/__index__/g, index).replace(/__affectPrice__/g, values[0].affect_price).replace(/__affectPriceLabel__/g, productOptionLang.affect_price_label).replace(/__selectedFixed__/g, selectedFixed).replace(/__fixedLang__/g, productOptionLang.fixed).replace(/__selectedPercent__/g, selectedPercent).replace(/__percentLang__/g, productOptionLang.percent);
        } else {
          if (values.length > 0) {
            var _template = $('#template-option-type-array').html();
            var valuesResult = '';
            var tmp = _template.replace(/__priceLabel__/g, price).replace(/__priceTypeLabel__/g, priceType).replace(/__index__/g, index).replace(/__label__/g, label);
            $.each(values, function (key, value) {
              var valueTemplate = $('#template-option-type-value').html();
              var order = typeof value.order === 'undefined' ? value.order : key;
              var selectedFixed = value.affect_type === 0 ? 'selected' : '';
              var selectedPercent = value.affect_type === 1 ? 'selected' : '';
              valuesResult += valueTemplate.replace(/__key__/g, key).replace(/__order__/g, order).replace(/__index__/g, index).replace(/__labelPlaceholder__/g, productOptionLang.label_placeholder).replace(/__affectPriceLabel__/g, productOptionLang.affect_price_label).replace(/__selectedFixed__/g, selectedFixed).replace(/__fixedLang__/g, productOptionLang.fixed).replace(/__selectedPercent__/g, selectedPercent).replace(/__option_value_input__/g, value.option_value).replace(/__affectPrice__/g, value.affect_price).replace(/__percentLang__/g, productOptionLang.percent);
            });
            html += tmp.replace(/__optionValue__/g, valuesResult);
          }
          html += "<button type=\"button\" class=\"btn btn-info mt-3 add-new-row\" id=\"add-new-row\">".concat(productOptionLang.add_new_row, "</button>");
        }
      }
      return html;
    }
  };
  productOptionForm.init();
});
/******/ })()
;