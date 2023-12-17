// devisShow.js
import { Grid, html } from "https://unpkg.com/gridjs?module";

let devisGrid;

document.addEventListener('DOMContentLoaded', function() {
    initializeGrid();
    loadDevisData();
});

function initializeGrid() {
    devisGrid = new Grid({
        columns: ['Nom', 'Quantité'],
        data: []
    });
    devisGrid.render(document.getElementById("wrapper"));
}

function loadDevisData() {
    const devisProductsData = document.getElementById('devisProductsData');
    const devisFormulasData = document.getElementById('devisFormulasData');

    if (devisProductsData && devisFormulasData) {
        const products = JSON.parse(devisProductsData.getAttribute('data-products'));
        const formulas = JSON.parse(devisFormulasData.getAttribute('data-formulas'));
        initDevisGridWithData(products, formulas);
    }
}

function initDevisGridWithData(products, formulas) {
    const productRows = products.map(product => [product.name, product.quantity, 'product']);
    const formulaRows = formulas.map(formula => [formula.name, formula.quantity, 'formula']);
    const gridData = productRows.concat(formulaRows);

    devisGrid.updateConfig({ data: gridData }).forceRender();
}

// Ajoutez ici d'autres fonctions spécifiques à la page d'affichage du devis, si nécessaire
