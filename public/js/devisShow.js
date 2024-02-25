// devisShow.js
import { Grid, html } from "https://unpkg.com/gridjs?module";

document.addEventListener("DOMContentLoaded", function () {
  initializeGrid();
});

function initializeGrid() {
  const devisGrid = new Grid({
    columns: ["Nom", "Quantité", "Prix"],
    data: [],
    style: {
      table: {
        border: "none",
      },
      th: {
        "background-color": "#d4d4d4",
        color: "#000",
        "text-align": "center",
      },
      td: {
        "text-align": "center",
        
      },
    },
  });
  devisGrid.render(document.getElementById("wrapper"));

  setTimeout(() => {
    loadDevisData(devisGrid);
  }, 1000);
}

function loadDevisData(devisGrid) {
  const devisProductsData = document.getElementById("devisProductsData");
  const devisFormulasData = document.getElementById("devisFormulasData");

  if (devisProductsData && devisFormulasData) {
    const products = JSON.parse(
      devisProductsData.getAttribute("data-products")
    );
    const formulas = JSON.parse(
      devisFormulasData.getAttribute("data-formulas")
    );
    initDevisGridWithData(products, formulas, devisGrid);
    console.log("here ! :", products, formulas);
  }
}

function initDevisGridWithData(products, formulas, devisGrid) {
  console.log("products", products);
  const productRows = products.map((product) => [
    product.name,
    product.quantity,
    ((product.price * product.quantity) / 100).toFixed(2) + " €",
    "product",
  ]);
  console.log("product" ,productRows);
  const formulaRows = formulas.map((formula) => [
    formula.name,
    formula.quantity,
    ((formula.price * formula.quantity) / 100).toFixed(2) + " €",
    "formula",
  ]);
  console.log("formula" ,productRows);
  const gridData = productRows.concat(formulaRows);
  console.log(gridData);

  devisGrid.updateConfig({ data: gridData }).forceRender();
}
