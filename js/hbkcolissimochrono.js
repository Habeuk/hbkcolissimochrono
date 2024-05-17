/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/widget.js":
/*!**************************!*\
  !*** ./src/js/widget.js ***!
  \**************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
window.SelectColissimoRelais = point => {
  // console.log(" Call back frame ");
  // console.log(point);
  if (window.CommandColissimoPickUp) {
    const hbk = window.CommandColissimoPickUp;
    hbk.closeModal();
    // Set point relais in hidden input.
    const edit = document.querySelector(".hbkcolissimochrono-pickup-book-edit");
    if (edit) {
      edit.value = JSON.stringify(hbk.buildPointJson(point));
      console.log("pickup-book-edit : ", edit.value, "\n point : ", point);
    }
    // Set point relais to html
    const editHtml = document.querySelector(".hbkcolissimochrono-pickup-edit .pickup-html");
    if (editHtml) {
      editHtml.innerHTML = hbk.buildPointHtml(point);
    }
  }
};

/**
 * --
 */
class Widget {
  constructor(context, token, shippingMethod, address) {
    this.context = context;
    this.token = token;
    this.address = address;
    this.shippingMethod = shippingMethod;
    this.selector = ".hbkcolissimochrono_pickup";
    this.PoPin = "";
  }

  /**
   * --
   * @returns
   */
  generateIconClose() {
    const iconSvg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    const iconPath = document.createElementNS("http://www.w3.org/2000/svg", "path");
    iconSvg.setAttribute("viewBox", "0 0 14 14");
    iconSvg.setAttribute("height", "14px");
    iconSvg.setAttribute("width", "14px");
    iconSvg.setAttribute("focusable", false);
    iconSvg.classList.add("svg-close");
    iconSvg.classList.add("js-close");
    iconPath.setAttribute("d", "M13 13L1 1M13 1L1 13");
    iconPath.setAttribute("stroke", "currentColor");
    iconPath.setAttribute("stroke-width", "1.1");
    iconPath.setAttribute("fill", "none");
    iconSvg.appendChild(iconPath);
    return iconSvg;
  }

  /**
   * Ajoute l'icone de fermeture.
   */
  addIconClose() {
    const PoPin = this.PoPin;
    // add cover
    if (!PoPin.querySelector(".overlay")) {
      const covertDk = document.createElement("div");
      covertDk.setAttribute("class", "overlay");
      PoPin.appendChild(covertDk);
      // On ne souhaite pas qu'on puisse fermé le popin via un click sur le overlay.
      // PoPin.querySelector(".overlay").addEventListener("click", () => {
      //   this.closeModal();
      // });
    }
    // add button close.
    if (!PoPin.querySelector(".js-close")) {
      PoPin.querySelector(".hbk__container .hbk_content").appendChild(this.generateIconClose());
      PoPin.querySelector(".js-close").addEventListener("click", () => {
        this.closeModal();
      });
    }
  }

  /**
   * Pour le bon fonctionnement du widget, il faut que la fermeture passe toujours par le clique sur son bonton close.
   */
  actionCloseByColissimo() {
    this.context.querySelector(this.selector + " .widget_colissimo_close").addEventListener("click", () => {
      this.PoPin.classList.remove("open");
      document.querySelector("body").classList.remove("modal-open");
    });
  }
  closeModal() {
    this.context.querySelector(this.selector + " .widget_colissimo_close").click();
  }
  setHeader() {
    this.context.querySelector(this.selector + " .hbk_content .header").innerHTML = "Selectionner un relais <span class='text-wbu-secondary'>(" + this.shippingMethod + " )</span>";
  }
  buildPointJson(point) {
    return {
      nom: point.nom,
      adresse1: point.adresse1,
      adresse2: point.adresse2,
      codePostal: point.codePostal,
      identifiant: point.identifiant,
      localite: point.localite
    };
  }
  buildPointHtml(point) {
    const importantPoint = this.buildPointJson(point);
    let stringPoint = "";
    if (importantPoint.nom) stringPoint += importantPoint.nom + "<br>";
    if (importantPoint.adresse1) stringPoint += importantPoint.adresse1 + "<br>";
    if (importantPoint.adresse2) stringPoint += importantPoint.adresse2 + "<br>";
    if (importantPoint.codePostal) stringPoint += importantPoint.codePostal + "<br>";
    //
    return stringPoint;
  }
  openModal() {
    this.PoPin = this.context.querySelector(this.selector);
    this.PoPin.classList.add("open");
    this.addIconClose();
    document.querySelector("body").classList.add("modal-open");
    this.buildWidget();
    this.actionCloseByColissimo();
    this.setHeader();
  }
  buildWidget() {
    console.log("this.address : ", this.address);
    // URL du serveur colissimo -->
    var url_serveur = "https://ws.colissimo.fr";
    window.jQuery(this.selector + " .widget-pickup").frameColissimoOpen({
      URLColissimo: url_serveur,
      callBackFrame: "SelectColissimoRelais",
      ceCountry: this.address.country_code,
      ceAddress: this.address.address_line1,
      ceZipCode: this.address.postal_code,
      ceTown: this.address.locality,
      token: this.token
    });
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Widget);

/***/ }),

/***/ "./src/scss/edit.scss":
/*!****************************!*\
  !*** ./src/scss/edit.scss ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/scss/hbkcolissimochrono.scss":
/*!******************************************!*\
  !*** ./src/scss/hbkcolissimochrono.scss ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


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
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**************************************!*\
  !*** ./src/js/hbkcolissimochrono.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _scss_hbkcolissimochrono_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../scss/hbkcolissimochrono.scss */ "./src/scss/hbkcolissimochrono.scss");
/* harmony import */ var _scss_edit_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../scss/edit.scss */ "./src/scss/edit.scss");
/* harmony import */ var _widget_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./widget.js */ "./src/js/widget.js");



(function (Drupal) {
  /**
   * Permet d'ouvrir le popup pour la selection du pickup.
   * C'est drupal qui determine s'il faut l'ouvrir ou pas.
   * Le popup peut ne pas s'ouvrir s'il ya deja une adresse selectionné.
   *
   * @param {*} ajax
   * @param {*} response
   * @param {*} status
   */
  Drupal.AjaxCommands.prototype.CommandColissimoPickUp = function (DrupalAjax, datas, status) {
    console.log(" DrupalAjax : ", DrupalAjax, "\n Content : ", datas, "\n Status : ", status);
    const token = datas.token;
    const address = datas.arguments.address;
    const shippingMethod = datas.arguments.shipping_method;
    // Si l'adresse n'est pas definit on n'ouvre pas le pop-up.
    if (address.country_code) {
      window.CommandColissimoPickUp = new _widget_js__WEBPACK_IMPORTED_MODULE_2__["default"](document, token, shippingMethod, address);
      window.CommandColissimoPickUp.openModal();
    }
  };
  //
  Drupal.behaviors.hbkcolissimochronoHbkcolissimochrono = {
    attach(context, settings) {
      console.log("It works!");
    }
  };
  //
})(Drupal);
})();

/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QU8sRUFBRXlELFFBQVEsRUFBRTtNQUN4QmhFLE9BQU8sQ0FBQ0MsR0FBRyxDQUFDLFdBQVcsQ0FBQztJQUMxQjtFQUNGLENBQUM7RUFDRDtBQUNGLENBQUMsRUFBRW9ELE1BQU0sQ0FBQyxDIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vQHN0ZXBoYW5lODg4L3didS1hdG9taXF1ZS10aGVtZS8uL3NyYy9qcy93aWRnZXQuanMiLCJ3ZWJwYWNrOi8vQHN0ZXBoYW5lODg4L3didS1hdG9taXF1ZS10aGVtZS8uL3NyYy9zY3NzL2VkaXQuc2Nzcz84NjY5Iiwid2VicGFjazovL0BzdGVwaGFuZTg4OC93YnUtYXRvbWlxdWUtdGhlbWUvLi9zcmMvc2Nzcy9oYmtjb2xpc3NpbW9jaHJvbm8uc2Nzcz9kYTA4Iiwid2VicGFjazovL0BzdGVwaGFuZTg4OC93YnUtYXRvbWlxdWUtdGhlbWUvd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vQHN0ZXBoYW5lODg4L3didS1hdG9taXF1ZS10aGVtZS93ZWJwYWNrL3J1bnRpbWUvZGVmaW5lIHByb3BlcnR5IGdldHRlcnMiLCJ3ZWJwYWNrOi8vQHN0ZXBoYW5lODg4L3didS1hdG9taXF1ZS10aGVtZS93ZWJwYWNrL3J1bnRpbWUvaGFzT3duUHJvcGVydHkgc2hvcnRoYW5kIiwid2VicGFjazovL0BzdGVwaGFuZTg4OC93YnUtYXRvbWlxdWUtdGhlbWUvd2VicGFjay9ydW50aW1lL21ha2UgbmFtZXNwYWNlIG9iamVjdCIsIndlYnBhY2s6Ly9Ac3RlcGhhbmU4ODgvd2J1LWF0b21pcXVlLXRoZW1lLy4vc3JjL2pzL2hia2NvbGlzc2ltb2Nocm9uby5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJ3aW5kb3cuU2VsZWN0Q29saXNzaW1vUmVsYWlzID0gKHBvaW50KSA9PiB7XG4gIC8vIGNvbnNvbGUubG9nKFwiIENhbGwgYmFjayBmcmFtZSBcIik7XG4gIC8vIGNvbnNvbGUubG9nKHBvaW50KTtcbiAgaWYgKHdpbmRvdy5Db21tYW5kQ29saXNzaW1vUGlja1VwKSB7XG4gICAgY29uc3QgaGJrID0gd2luZG93LkNvbW1hbmRDb2xpc3NpbW9QaWNrVXA7XG4gICAgaGJrLmNsb3NlTW9kYWwoKTtcbiAgICAvLyBTZXQgcG9pbnQgcmVsYWlzIGluIGhpZGRlbiBpbnB1dC5cbiAgICBjb25zdCBlZGl0ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcihcIi5oYmtjb2xpc3NpbW9jaHJvbm8tcGlja3VwLWJvb2stZWRpdFwiKTtcbiAgICBpZiAoZWRpdCkge1xuICAgICAgZWRpdC52YWx1ZSA9IEpTT04uc3RyaW5naWZ5KGhiay5idWlsZFBvaW50SnNvbihwb2ludCkpO1xuICAgICAgY29uc29sZS5sb2coXCJwaWNrdXAtYm9vay1lZGl0IDogXCIsIGVkaXQudmFsdWUsIFwiXFxuIHBvaW50IDogXCIsIHBvaW50KTtcbiAgICB9XG4gICAgLy8gU2V0IHBvaW50IHJlbGFpcyB0byBodG1sXG4gICAgY29uc3QgZWRpdEh0bWwgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKFwiLmhia2NvbGlzc2ltb2Nocm9uby1waWNrdXAtZWRpdCAucGlja3VwLWh0bWxcIik7XG4gICAgaWYgKGVkaXRIdG1sKSB7XG4gICAgICBlZGl0SHRtbC5pbm5lckhUTUwgPSBoYmsuYnVpbGRQb2ludEh0bWwocG9pbnQpO1xuICAgIH1cbiAgfVxufTtcblxuLyoqXG4gKiAtLVxuICovXG5jbGFzcyBXaWRnZXQge1xuICBjb25zdHJ1Y3Rvcihjb250ZXh0LCB0b2tlbiwgc2hpcHBpbmdNZXRob2QsIGFkZHJlc3MpIHtcbiAgICB0aGlzLmNvbnRleHQgPSBjb250ZXh0O1xuICAgIHRoaXMudG9rZW4gPSB0b2tlbjtcbiAgICB0aGlzLmFkZHJlc3MgPSBhZGRyZXNzO1xuICAgIHRoaXMuc2hpcHBpbmdNZXRob2QgPSBzaGlwcGluZ01ldGhvZDtcbiAgICB0aGlzLnNlbGVjdG9yID0gXCIuaGJrY29saXNzaW1vY2hyb25vX3BpY2t1cFwiO1xuICAgIHRoaXMuUG9QaW4gPSBcIlwiO1xuICB9XG5cbiAgLyoqXG4gICAqIC0tXG4gICAqIEByZXR1cm5zXG4gICAqL1xuICBnZW5lcmF0ZUljb25DbG9zZSgpIHtcbiAgICBjb25zdCBpY29uU3ZnID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudE5TKFwiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmdcIiwgXCJzdmdcIik7XG4gICAgY29uc3QgaWNvblBhdGggPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50TlMoXCJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2Z1wiLCBcInBhdGhcIik7XG4gICAgaWNvblN2Zy5zZXRBdHRyaWJ1dGUoXCJ2aWV3Qm94XCIsIFwiMCAwIDE0IDE0XCIpO1xuICAgIGljb25Tdmcuc2V0QXR0cmlidXRlKFwiaGVpZ2h0XCIsIFwiMTRweFwiKTtcbiAgICBpY29uU3ZnLnNldEF0dHJpYnV0ZShcIndpZHRoXCIsIFwiMTRweFwiKTtcbiAgICBpY29uU3ZnLnNldEF0dHJpYnV0ZShcImZvY3VzYWJsZVwiLCBmYWxzZSk7XG4gICAgaWNvblN2Zy5jbGFzc0xpc3QuYWRkKFwic3ZnLWNsb3NlXCIpO1xuICAgIGljb25TdmcuY2xhc3NMaXN0LmFkZChcImpzLWNsb3NlXCIpO1xuICAgIGljb25QYXRoLnNldEF0dHJpYnV0ZShcImRcIiwgXCJNMTMgMTNMMSAxTTEzIDFMMSAxM1wiKTtcbiAgICBpY29uUGF0aC5zZXRBdHRyaWJ1dGUoXCJzdHJva2VcIiwgXCJjdXJyZW50Q29sb3JcIik7XG4gICAgaWNvblBhdGguc2V0QXR0cmlidXRlKFwic3Ryb2tlLXdpZHRoXCIsIFwiMS4xXCIpO1xuICAgIGljb25QYXRoLnNldEF0dHJpYnV0ZShcImZpbGxcIiwgXCJub25lXCIpO1xuICAgIGljb25TdmcuYXBwZW5kQ2hpbGQoaWNvblBhdGgpO1xuICAgIHJldHVybiBpY29uU3ZnO1xuICB9XG5cbiAgLyoqXG4gICAqIEFqb3V0ZSBsJ2ljb25lIGRlIGZlcm1ldHVyZS5cbiAgICovXG4gIGFkZEljb25DbG9zZSgpIHtcbiAgICBjb25zdCBQb1BpbiA9IHRoaXMuUG9QaW47XG4gICAgLy8gYWRkIGNvdmVyXG4gICAgaWYgKCFQb1Bpbi5xdWVyeVNlbGVjdG9yKFwiLm92ZXJsYXlcIikpIHtcbiAgICAgIGNvbnN0IGNvdmVydERrID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcImRpdlwiKTtcbiAgICAgIGNvdmVydERrLnNldEF0dHJpYnV0ZShcImNsYXNzXCIsIFwib3ZlcmxheVwiKTtcbiAgICAgIFBvUGluLmFwcGVuZENoaWxkKGNvdmVydERrKTtcbiAgICAgIC8vIE9uIG5lIHNvdWhhaXRlIHBhcyBxdSdvbiBwdWlzc2UgZmVybcOpIGxlIHBvcGluIHZpYSB1biBjbGljayBzdXIgbGUgb3ZlcmxheS5cbiAgICAgIC8vIFBvUGluLnF1ZXJ5U2VsZWN0b3IoXCIub3ZlcmxheVwiKS5hZGRFdmVudExpc3RlbmVyKFwiY2xpY2tcIiwgKCkgPT4ge1xuICAgICAgLy8gICB0aGlzLmNsb3NlTW9kYWwoKTtcbiAgICAgIC8vIH0pO1xuICAgIH1cbiAgICAvLyBhZGQgYnV0dG9uIGNsb3NlLlxuICAgIGlmICghUG9QaW4ucXVlcnlTZWxlY3RvcihcIi5qcy1jbG9zZVwiKSkge1xuICAgICAgUG9QaW4ucXVlcnlTZWxlY3RvcihcIi5oYmtfX2NvbnRhaW5lciAuaGJrX2NvbnRlbnRcIikuYXBwZW5kQ2hpbGQodGhpcy5nZW5lcmF0ZUljb25DbG9zZSgpKTtcbiAgICAgIFBvUGluLnF1ZXJ5U2VsZWN0b3IoXCIuanMtY2xvc2VcIikuYWRkRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsICgpID0+IHtcbiAgICAgICAgdGhpcy5jbG9zZU1vZGFsKCk7XG4gICAgICB9KTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUG91ciBsZSBib24gZm9uY3Rpb25uZW1lbnQgZHUgd2lkZ2V0LCBpbCBmYXV0IHF1ZSBsYSBmZXJtZXR1cmUgcGFzc2UgdG91am91cnMgcGFyIGxlIGNsaXF1ZSBzdXIgc29uIGJvbnRvbiBjbG9zZS5cbiAgICovXG4gIGFjdGlvbkNsb3NlQnlDb2xpc3NpbW8oKSB7XG4gICAgdGhpcy5jb250ZXh0LnF1ZXJ5U2VsZWN0b3IodGhpcy5zZWxlY3RvciArIFwiIC53aWRnZXRfY29saXNzaW1vX2Nsb3NlXCIpLmFkZEV2ZW50TGlzdGVuZXIoXCJjbGlja1wiLCAoKSA9PiB7XG4gICAgICB0aGlzLlBvUGluLmNsYXNzTGlzdC5yZW1vdmUoXCJvcGVuXCIpO1xuICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcihcImJvZHlcIikuY2xhc3NMaXN0LnJlbW92ZShcIm1vZGFsLW9wZW5cIik7XG4gICAgfSk7XG4gIH1cblxuICBjbG9zZU1vZGFsKCkge1xuICAgIHRoaXMuY29udGV4dC5xdWVyeVNlbGVjdG9yKHRoaXMuc2VsZWN0b3IgKyBcIiAud2lkZ2V0X2NvbGlzc2ltb19jbG9zZVwiKS5jbGljaygpO1xuICB9XG5cbiAgc2V0SGVhZGVyKCkge1xuICAgIHRoaXMuY29udGV4dC5xdWVyeVNlbGVjdG9yKHRoaXMuc2VsZWN0b3IgKyBcIiAuaGJrX2NvbnRlbnQgLmhlYWRlclwiKS5pbm5lckhUTUwgPSBcIlNlbGVjdGlvbm5lciB1biByZWxhaXMgPHNwYW4gY2xhc3M9J3RleHQtd2J1LXNlY29uZGFyeSc+KFwiICsgdGhpcy5zaGlwcGluZ01ldGhvZCArIFwiICk8L3NwYW4+XCI7XG4gIH1cbiAgYnVpbGRQb2ludEpzb24ocG9pbnQpIHtcbiAgICByZXR1cm4ge1xuICAgICAgbm9tOiBwb2ludC5ub20sXG4gICAgICBhZHJlc3NlMTogcG9pbnQuYWRyZXNzZTEsXG4gICAgICBhZHJlc3NlMjogcG9pbnQuYWRyZXNzZTIsXG4gICAgICBjb2RlUG9zdGFsOiBwb2ludC5jb2RlUG9zdGFsLFxuICAgICAgaWRlbnRpZmlhbnQ6IHBvaW50LmlkZW50aWZpYW50LFxuICAgICAgbG9jYWxpdGU6IHBvaW50LmxvY2FsaXRlLFxuICAgIH07XG4gIH1cblxuICBidWlsZFBvaW50SHRtbChwb2ludCkge1xuICAgIGNvbnN0IGltcG9ydGFudFBvaW50ID0gdGhpcy5idWlsZFBvaW50SnNvbihwb2ludCk7XG4gICAgbGV0IHN0cmluZ1BvaW50ID0gXCJcIjtcbiAgICBpZiAoaW1wb3J0YW50UG9pbnQubm9tKSBzdHJpbmdQb2ludCArPSBpbXBvcnRhbnRQb2ludC5ub20gKyBcIjxicj5cIjtcbiAgICBpZiAoaW1wb3J0YW50UG9pbnQuYWRyZXNzZTEpIHN0cmluZ1BvaW50ICs9IGltcG9ydGFudFBvaW50LmFkcmVzc2UxICsgXCI8YnI+XCI7XG4gICAgaWYgKGltcG9ydGFudFBvaW50LmFkcmVzc2UyKSBzdHJpbmdQb2ludCArPSBpbXBvcnRhbnRQb2ludC5hZHJlc3NlMiArIFwiPGJyPlwiO1xuICAgIGlmIChpbXBvcnRhbnRQb2ludC5jb2RlUG9zdGFsKSBzdHJpbmdQb2ludCArPSBpbXBvcnRhbnRQb2ludC5jb2RlUG9zdGFsICsgXCI8YnI+XCI7XG4gICAgLy9cbiAgICByZXR1cm4gc3RyaW5nUG9pbnQ7XG4gIH1cblxuICBvcGVuTW9kYWwoKSB7XG4gICAgdGhpcy5Qb1BpbiA9IHRoaXMuY29udGV4dC5xdWVyeVNlbGVjdG9yKHRoaXMuc2VsZWN0b3IpO1xuICAgIHRoaXMuUG9QaW4uY2xhc3NMaXN0LmFkZChcIm9wZW5cIik7XG4gICAgdGhpcy5hZGRJY29uQ2xvc2UoKTtcbiAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKFwiYm9keVwiKS5jbGFzc0xpc3QuYWRkKFwibW9kYWwtb3BlblwiKTtcbiAgICB0aGlzLmJ1aWxkV2lkZ2V0KCk7XG4gICAgdGhpcy5hY3Rpb25DbG9zZUJ5Q29saXNzaW1vKCk7XG4gICAgdGhpcy5zZXRIZWFkZXIoKTtcbiAgfVxuXG4gIGJ1aWxkV2lkZ2V0KCkge1xuICAgIGNvbnNvbGUubG9nKFwidGhpcy5hZGRyZXNzIDogXCIsIHRoaXMuYWRkcmVzcyk7XG4gICAgLy8gVVJMIGR1IHNlcnZldXIgY29saXNzaW1vIC0tPlxuICAgIHZhciB1cmxfc2VydmV1ciA9IFwiaHR0cHM6Ly93cy5jb2xpc3NpbW8uZnJcIjtcbiAgICB3aW5kb3cualF1ZXJ5KHRoaXMuc2VsZWN0b3IgKyBcIiAud2lkZ2V0LXBpY2t1cFwiKS5mcmFtZUNvbGlzc2ltb09wZW4oe1xuICAgICAgVVJMQ29saXNzaW1vOiB1cmxfc2VydmV1cixcbiAgICAgIGNhbGxCYWNrRnJhbWU6IFwiU2VsZWN0Q29saXNzaW1vUmVsYWlzXCIsXG4gICAgICBjZUNvdW50cnk6IHRoaXMuYWRkcmVzcy5jb3VudHJ5X2NvZGUsXG4gICAgICBjZUFkZHJlc3M6IHRoaXMuYWRkcmVzcy5hZGRyZXNzX2xpbmUxLFxuICAgICAgY2VaaXBDb2RlOiB0aGlzLmFkZHJlc3MucG9zdGFsX2NvZGUsXG4gICAgICBjZVRvd246IHRoaXMuYWRkcmVzcy5sb2NhbGl0eSxcbiAgICAgIHRva2VuOiB0aGlzLnRva2VuLFxuICAgIH0pO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFdpZGdldDtcbiIsIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpblxuZXhwb3J0IHt9OyIsIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpblxuZXhwb3J0IHt9OyIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdHZhciBjYWNoZWRNb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdO1xuXHRpZiAoY2FjaGVkTW9kdWxlICE9PSB1bmRlZmluZWQpIHtcblx0XHRyZXR1cm4gY2FjaGVkTW9kdWxlLmV4cG9ydHM7XG5cdH1cblx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcblx0dmFyIG1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0gPSB7XG5cdFx0Ly8gbm8gbW9kdWxlLmlkIG5lZWRlZFxuXHRcdC8vIG5vIG1vZHVsZS5sb2FkZWQgbmVlZGVkXG5cdFx0ZXhwb3J0czoge31cblx0fTtcblxuXHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cblx0X193ZWJwYWNrX21vZHVsZXNfX1ttb2R1bGVJZF0obW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cblx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcblx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufVxuXG4iLCIvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9ucyBmb3IgaGFybW9ueSBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSAoZXhwb3J0cywgZGVmaW5pdGlvbikgPT4ge1xuXHRmb3IodmFyIGtleSBpbiBkZWZpbml0aW9uKSB7XG5cdFx0aWYoX193ZWJwYWNrX3JlcXVpcmVfXy5vKGRlZmluaXRpb24sIGtleSkgJiYgIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBrZXkpKSB7XG5cdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywga2V5LCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZGVmaW5pdGlvbltrZXldIH0pO1xuXHRcdH1cblx0fVxufTsiLCJfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSAob2JqLCBwcm9wKSA9PiAoT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iaiwgcHJvcCkpIiwiLy8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuX193ZWJwYWNrX3JlcXVpcmVfXy5yID0gKGV4cG9ydHMpID0+IHtcblx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG5cdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG5cdH1cblx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbn07IiwiaW1wb3J0IFwiLi4vc2Nzcy9oYmtjb2xpc3NpbW9jaHJvbm8uc2Nzc1wiO1xuaW1wb3J0IFwiLi4vc2Nzcy9lZGl0LnNjc3NcIjtcbmltcG9ydCB3aWRnZXQgZnJvbSBcIi4vd2lkZ2V0LmpzXCI7XG5cbihmdW5jdGlvbiAoRHJ1cGFsKSB7XG4gIC8qKlxuICAgKiBQZXJtZXQgZCdvdXZyaXIgbGUgcG9wdXAgcG91ciBsYSBzZWxlY3Rpb24gZHUgcGlja3VwLlxuICAgKiBDJ2VzdCBkcnVwYWwgcXVpIGRldGVybWluZSBzJ2lsIGZhdXQgbCdvdXZyaXIgb3UgcGFzLlxuICAgKiBMZSBwb3B1cCBwZXV0IG5lIHBhcyBzJ291dnJpciBzJ2lsIHlhIGRlamEgdW5lIGFkcmVzc2Ugc2VsZWN0aW9ubsOpLlxuICAgKlxuICAgKiBAcGFyYW0geyp9IGFqYXhcbiAgICogQHBhcmFtIHsqfSByZXNwb25zZVxuICAgKiBAcGFyYW0geyp9IHN0YXR1c1xuICAgKi9cbiAgRHJ1cGFsLkFqYXhDb21tYW5kcy5wcm90b3R5cGUuQ29tbWFuZENvbGlzc2ltb1BpY2tVcCA9IGZ1bmN0aW9uIChEcnVwYWxBamF4LCBkYXRhcywgc3RhdHVzKSB7XG4gICAgY29uc29sZS5sb2coXCIgRHJ1cGFsQWpheCA6IFwiLCBEcnVwYWxBamF4LCBcIlxcbiBDb250ZW50IDogXCIsIGRhdGFzLCBcIlxcbiBTdGF0dXMgOiBcIiwgc3RhdHVzKTtcbiAgICBjb25zdCB0b2tlbiA9IGRhdGFzLnRva2VuO1xuICAgIGNvbnN0IGFkZHJlc3MgPSBkYXRhcy5hcmd1bWVudHMuYWRkcmVzcztcbiAgICBjb25zdCBzaGlwcGluZ01ldGhvZCA9IGRhdGFzLmFyZ3VtZW50cy5zaGlwcGluZ19tZXRob2Q7XG4gICAgLy8gU2kgbCdhZHJlc3NlIG4nZXN0IHBhcyBkZWZpbml0IG9uIG4nb3V2cmUgcGFzIGxlIHBvcC11cC5cbiAgICBpZiAoYWRkcmVzcy5jb3VudHJ5X2NvZGUpIHtcbiAgICAgIHdpbmRvdy5Db21tYW5kQ29saXNzaW1vUGlja1VwID0gbmV3IHdpZGdldChkb2N1bWVudCwgdG9rZW4sIHNoaXBwaW5nTWV0aG9kLCBhZGRyZXNzKTtcbiAgICAgIHdpbmRvdy5Db21tYW5kQ29saXNzaW1vUGlja1VwLm9wZW5Nb2RhbCgpO1xuICAgIH1cbiAgfTtcbiAgLy9cbiAgRHJ1cGFsLmJlaGF2aW9ycy5oYmtjb2xpc3NpbW9jaHJvbm9IYmtjb2xpc3NpbW9jaHJvbm8gPSB7XG4gICAgYXR0YWNoKGNvbnRleHQsIHNldHRpbmdzKSB7XG4gICAgICBjb25zb2xlLmxvZyhcIkl0IHdvcmtzIVwiKTtcbiAgICB9LFxuICB9O1xuICAvL1xufSkoRHJ1cGFsKTtcbiJdLCJuYW1lcyI6WyJ3aW5kb3ciLCJTZWxlY3RDb2xpc3NpbW9SZWxhaXMiLCJwb2ludCIsIkNvbW1hbmRDb2xpc3NpbW9QaWNrVXAiLCJoYmsiLCJjbG9zZU1vZGFsIiwiZWRpdCIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsInZhbHVlIiwiSlNPTiIsInN0cmluZ2lmeSIsImJ1aWxkUG9pbnRKc29uIiwiY29uc29sZSIsImxvZyIsImVkaXRIdG1sIiwiaW5uZXJIVE1MIiwiYnVpbGRQb2ludEh0bWwiLCJXaWRnZXQiLCJjb25zdHJ1Y3RvciIsImNvbnRleHQiLCJ0b2tlbiIsInNoaXBwaW5nTWV0aG9kIiwiYWRkcmVzcyIsInNlbGVjdG9yIiwiUG9QaW4iLCJnZW5lcmF0ZUljb25DbG9zZSIsImljb25TdmciLCJjcmVhdGVFbGVtZW50TlMiLCJpY29uUGF0aCIsInNldEF0dHJpYnV0ZSIsImNsYXNzTGlzdCIsImFkZCIsImFwcGVuZENoaWxkIiwiYWRkSWNvbkNsb3NlIiwiY292ZXJ0RGsiLCJjcmVhdGVFbGVtZW50IiwiYWRkRXZlbnRMaXN0ZW5lciIsImFjdGlvbkNsb3NlQnlDb2xpc3NpbW8iLCJyZW1vdmUiLCJjbGljayIsInNldEhlYWRlciIsIm5vbSIsImFkcmVzc2UxIiwiYWRyZXNzZTIiLCJjb2RlUG9zdGFsIiwiaWRlbnRpZmlhbnQiLCJsb2NhbGl0ZSIsImltcG9ydGFudFBvaW50Iiwic3RyaW5nUG9pbnQiLCJvcGVuTW9kYWwiLCJidWlsZFdpZGdldCIsInVybF9zZXJ2ZXVyIiwialF1ZXJ5IiwiZnJhbWVDb2xpc3NpbW9PcGVuIiwiVVJMQ29saXNzaW1vIiwiY2FsbEJhY2tGcmFtZSIsImNlQ291bnRyeSIsImNvdW50cnlfY29kZSIsImNlQWRkcmVzcyIsImFkZHJlc3NfbGluZTEiLCJjZVppcENvZGUiLCJwb3N0YWxfY29kZSIsImNlVG93biIsImxvY2FsaXR5Iiwid2lkZ2V0IiwiRHJ1cGFsIiwiQWpheENvbW1hbmRzIiwicHJvdG90eXBlIiwiRHJ1cGFsQWpheCIsImRhdGFzIiwic3RhdHVzIiwiYXJndW1lbnRzIiwic2hpcHBpbmdfbWV0aG9kIiwiYmVoYXZpb3JzIiwiaGJrY29saXNzaW1vY2hyb25vSGJrY29saXNzaW1vY2hyb25vIiwiYXR0YWNoIiwic2V0dGluZ3MiXSwic291cmNlUm9vdCI6IiJ9