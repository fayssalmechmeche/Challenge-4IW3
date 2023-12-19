// devisShow.js
import { Grid, html } from "https://unpkg.com/gridjs?module";

let devisGrid;

document.addEventListener('DOMContentLoaded', function() {
    initializeGrid();
    loadDevisData();
});

function initializeGrid() {
    devisGrid = new Grid({
        columns: ['Nom', 'Quantité','Prix'],
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
        console.log(products, formulas);
    }

}

function initDevisGridWithData(products, formulas) {
    const productRows = products.map(product => [
        product.name,
        product.quantity,
        (product.price * product.quantity / 100).toFixed(2) + ' €',
        'product'
    ]);
    const formulaRows = formulas.map(formula => [
        formula.name,
        formula.quantity,
        (formula.price * formula.quantity / 100).toFixed(2) + ' €',
        'formula'
    ]);
    const gridData = productRows.concat(formulaRows);
    console.log(gridData);

    devisGrid.updateConfig({ data: gridData }).forceRender();
}