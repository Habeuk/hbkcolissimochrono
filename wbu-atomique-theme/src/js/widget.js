window.SelectColissimoRelais = (point) => {
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
      localite: point.localite,
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
      token: this.token,
    });
  }
}

export default Widget;
