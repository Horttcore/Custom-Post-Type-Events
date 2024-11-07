/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/Blocks/Events/edit.js":
/*!***********************************!*\
  !*** ./src/Blocks/Events/edit.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _loop_control__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./loop-control */ "./src/Blocks/Events/loop-control.js");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





const Edit = ({
  attributes,
  setAttributes
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
    ...(0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)(),
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_loop_control__WEBPACK_IMPORTED_MODULE_1__["default"], {
      postType: "event",
      ...attributes,
      setAttributes: setAttributes
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_0___default()), {
      block: "custom-post-type-events/events",
      attributes: attributes
    })]
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Edit);

/***/ }),

/***/ "./src/Blocks/Events/loop-control.js":
/*!*******************************************!*\
  !*** ./src/Blocks/Events/loop-control.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__);







const LoopControl = props => {
  const {
    setAttributes,
    order,
    orderBy,
    numberOfItems,
    orderByValues,
    offset,
    postIn,
    postType,
    showPostsMax,
    offsetMax,
    postLayout,
    gridColumns,
    gridColumnsMax,
    useGrid
  } = props;
  const {
    posts,
    postTaxonomies,
    postTypeData
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => {
    const {
      getEntityRecords,
      getTaxonomy,
      getPostType
    } = select("core");
    // @ts-ignore
    const postTypeData = getPostType(postType);
    const postTaxonomiesData = [];
    let postsData = [];
    if (postTypeData && postTypeData.taxonomies) {
      postTypeData.taxonomies.map(tax => {
        // @ts-ignore
        const taxonomy = getTaxonomy(tax);
        if (taxonomy) {
          // @ts-ignore
          taxonomy.terms = getEntityRecords("taxonomy", tax, {
            per_page: 100
          });
          postTaxonomiesData.push(taxonomy);
        }
      });
    }

    // @ts-ignore
    postsData = getEntityRecords("postType", props.postType, {
      per_page: 100
    });
    return {
      postTypeData,
      posts: postsData,
      postTaxonomies: postTaxonomiesData
    };
  }, [props.postType]);
  const gridColumnsMaxValue = gridColumnsMax ? gridColumnsMax : 12;
  const layoutControls = [{
    icon: 'list-view',
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('List View'),
    onClick: () => setAttributes({
      postLayout: 'list',
      gridColumns: -1
    }),
    isActive: postLayout === 'list'
  }, {
    icon: 'grid-view',
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Grid View'),
    onClick: () => setAttributes({
      postLayout: 'grid',
      gridColumns: 4
    }),
    isActive: postLayout === 'grid'
  }];
  const taxonomySelects = [];
  const orderByValue = [orderBy, order].join('/');
  const orderBySelectValues = orderByValues ? orderByValues : [{
    value: "title/asc",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("A → Z")
  }, {
    value: "date/desc",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Newest to Oldest")
  }, {
    value: "date/asc",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Oldest to Newest")
  }, {
    value: "menu_order/asc",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Menu order")
  }];
  const showPostsMaxValue = showPostsMax ? showPostsMax : 100;
  const offsetMaxValue = offsetMax ? offsetMax : 100;
  const snakeToCamel = str => str.replace(/([-_][a-z])/g, group => group.toUpperCase().replace('-', '').replace('_', ''));
  if (postTaxonomies) {
    postTaxonomies.map((taxonomy, index) => {
      let termsFieldValue = [];
      if (taxonomy.terms !== null) {
        let selectedTerms = props[snakeToCamel(taxonomy.slug)] ? props[snakeToCamel(taxonomy.slug)] : [];
        termsFieldValue = selectedTerms.map(termId => {
          let wantedTerm = taxonomy.terms.find(term => term.id === termId);
          return wantedTerm === undefined || !wantedTerm ? false : wantedTerm.name;
        });
      }
      if (!taxonomy.terms) taxonomy.terms = [];
      taxonomySelects.push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, {
        title: taxonomy.name,
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.FormTokenField, {
          value: termsFieldValue,
          suggestions: taxonomy.terms.map(term => term.name),
          onChange: selectedTerms => {
            let selectedTermsArray = [];
            selectedTerms.map(termName => {
              const matchingTerm = taxonomy.terms.find(term => {
                return term.name === termName;
              });
              if (matchingTerm !== undefined) {
                selectedTermsArray.push(matchingTerm.id);
              }
            });
            let attr = [];
            attr[snakeToCamel(taxonomy.slug)] = selectedTermsArray;
            attr = {
              ...attr
            };
            setAttributes(attr);
          }
        })
      }, `taxonomy-${index}`));
    });
  }
  let postsFieldValue = [];
  let postNames = [];
  if (posts !== null && posts.length && postIn) {
    posts.map(post => {
      postNames.push(post.title.raw);
    });
    postsFieldValue = postIn.map(postId => {
      let wantedPost = posts.find(post => post.id === postId);
      return wantedPost === undefined || !wantedPost ? false : wantedPost.title.raw;
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
    children: [useGrid && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.BlockControls, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Toolbar
      // @ts-ignore
      , {
        controls: layoutControls
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.InspectorControls, {
      children: [taxonomySelects, !postsFieldValue && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Order"),
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.SelectControl, {
          value: orderByValue,
          onChange: orderByValue => {
            const [orderBy, order] = orderByValue.split('/');
            setAttributes({
              orderBy,
              order
            });
          },
          options: orderBySelectValues
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Number of items"),
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.RangeControl, {
          value: numberOfItems,
          onChange: numberOfItems => {
            setAttributes({
              numberOfItems
            });
          },
          min: 1,
          max: showPostsMaxValue,
          required: true
        })
      }), postLayout == 'grid' && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Grid columns"),
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.RangeControl, {
          value: gridColumns,
          onChange: gridColumns => {
            setAttributes({
              gridColumns
            });
          },
          min: 1,
          max: gridColumnsMaxValue
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)("Offset"),
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.RangeControl, {
          value: offset,
          onChange: offset => {
            setAttributes({
              offset
            });
          },
          min: 0,
          max: offsetMaxValue,
          required: true
        })
      }), posts && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, {
        title: postTypeData.name,
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.FormTokenField, {
          label: "",
          value: postsFieldValue,
          suggestions: postNames,
          maxSuggestions: 20,
          onChange: selectedPosts => {
            let selectedPostsArray = [];
            selectedPosts.map(postName => {
              const matchingPost = posts.find(post => {
                return post.title.raw === postName;
              });
              if (matchingPost !== undefined) {
                selectedPostsArray.push(matchingPost.id);
              }
            });
            setAttributes({
              postIn: selectedPostsArray
            });
          }
        })
      })]
    })]
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (LoopControl);

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["ReactJSXRuntime"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ ((module) => {

module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "./src/Blocks/Events/block.json":
/*!**************************************!*\
  !*** ./src/Blocks/Events/block.json ***!
  \**************************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"custom-post-type-events/events","title":"Events","category":"widgets","icon":"category","description":"List events","textdomain":"custom-post-type-events","attributes":{"postLayout":{"type":"string","default":"list"},"gridColumns":{"type":"integer","default":-1},"gridColumnsMax":{"type":"integer","default":12},"orderBy":{"type":"string","default":"title"},"order":{"type":"string","default":"asc"},"numberOfItems":{"type":"number","default":10},"offset":{"type":"number","default":0},"postIn":{"type":"array","default":[]},"useGrid":{"type":"boolean","default":true},"eventCategory":{"type":"array","default":[]}},"editorScript":"file:./index.js"}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!************************************!*\
  !*** ./src/Blocks/Events/index.js ***!
  \************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./block.json */ "./src/Blocks/Events/block.json");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/Blocks/Events/edit.js");



(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_1__, {
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"]
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map