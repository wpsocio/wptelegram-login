/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),
/* 1 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: ./src/admin/blocks/telegram-login/editor.scss
var editor = __webpack_require__(0);

// CONCATENATED MODULE: ./src/admin/blocks/telegram-login/block.js
//  Import CSS.

var el = wp.element.createElement;
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;
var _wp$components = wp.components,
    PanelBody = _wp$components.PanelBody,
    RadioControl = _wp$components.RadioControl,
    ToggleControl = _wp$components.ToggleControl,
    TextControl = _wp$components.TextControl,
    SelectControl = _wp$components.SelectControl;

var getFinalOutput = function getFinalOutput(attributes) {
  var className = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var button_style = attributes.button_style,
      show_user_photo = attributes.show_user_photo,
      corner_radius = attributes.corner_radius;
  var button_width = 'small' === button_style ? '100px' : 'medium' === button_style ? '150px' : null;
  var avatar_width = 'small' === button_style ? '20px' : 'medium' === button_style ? '30px' : null;
  var avatar = 'on' === show_user_photo ? el("img", {
    src: wptelegram_login_I18n.login_avatar_url,
    style: {
      width: avatar_width
    }
  }) : null;
  return el("div", {
    className: className
  }, el("img", {
    src: wptelegram_login_I18n.login_image_url,
    style: {
      borderRadius: corner_radius + 'px',
      width: button_width
    }
  }), avatar);
};

registerBlockType('wptelegram/login', {
  title: __('WP Telegram Login'),
  icon: 'smartphone',
  category: 'widgets',
  attributes: {
    button_style: {
      type: 'string',
      default: 'large'
    },
    show_user_photo: {
      type: 'string',
      default: 'on'
    },
    corner_radius: {
      type: 'string',
      default: '20'
    },
    show_if_user_is: {
      type: 'string',
      default: '0'
    }
  },
  edit: function edit(_ref) {
    var attributes = _ref.attributes,
        setAttributes = _ref.setAttributes,
        className = _ref.className;
    var button_style = attributes.button_style,
        show_user_photo = attributes.show_user_photo,
        corner_radius = attributes.corner_radius,
        show_if_user_is = attributes.show_if_user_is;
    var controls = [el(InspectorControls, null, el(PanelBody, {
      title: __('Button Settings')
    }, el(RadioControl, {
      label: __('Button Style'),
      selected: button_style,
      onChange: function onChange(newStyle) {
        return setAttributes({
          button_style: newStyle
        });
      },
      options: [{
        label: 'Large',
        value: 'large'
      }, {
        label: 'Medium',
        value: 'medium'
      }, {
        label: 'Small',
        value: 'small'
      }]
    }), el(ToggleControl, {
      label: __('Show User Photo'),
      checked: 'on' === show_user_photo,
      onChange: function onChange() {
        return setAttributes({
          show_user_photo: 'on' === show_user_photo ? 'off' : 'on'
        });
      }
    }), el(TextControl, {
      label: __('Corner Radius'),
      value: corner_radius,
      onChange: function onChange(newRadius) {
        return setAttributes({
          corner_radius: newRadius
        });
      },
      type: "number",
      min: "0",
      max: "20"
    }), el(SelectControl, {
      label: __('Show if user is'),
      value: show_if_user_is,
      onChange: function onChange(value) {
        return setAttributes({
          show_if_user_is: value
        });
      },
      options: wptelegram_login_I18n.show_if_user_is_opts
    })))];
    return [controls, getFinalOutput(attributes, className)];
  },
  save: function save(props) {
    return getFinalOutput(props.attributes);
  }
});
// CONCATENATED MODULE: ./src/admin/blocks/index.js
/**
 * Gutenberg Blocks
 *
 * All blocks should be included here since this is the file that
 * Webpack is compiling as the input file.
 */


/***/ })
/******/ ]);