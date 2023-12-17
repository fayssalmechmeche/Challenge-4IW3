import { Grid, html } from "https://unpkg.com/gridjs?module";

let devisGrid;

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('devisForm')) {
        initDevisGrid();
        addEventListeners();
        handleCustomerSelectChange();
    } else {
        loadDevisData();
    }

    const customSubmitButton = document.getElementById('customSubmitButton');
    if (customSubmitButton) {
        customSubmitButton.addEventListener('click', function(event) {
            event.preventDefault();
            if (document.getElementById('devisForm')) {
                const fakeSubmitEvent = new Event('submit', { cancelable: true, bubbles: true });
                handleDevisFormSubmit(fakeSubmitEvent);
            }
        });
    }
});

function initDevisGrid() {
    devisGrid = new Grid({
        columns: [
            'Nom',
            {
                name: 'Quantité',
                formatter: (cell, row) => html(`<input type="number" min="1" value="${cell}" data-id="${row.cells[2].data}" class="quantity-input" onchange="updateDevisItemQuantity(this)">`)
            },
            {
                name: 'Supprimer',
                formatter: (_, row) => html(`<button type="button" onclick="removeDevisItemFromGrid('${row.cells[2].data}')">Supprimer</button>`)
            }
        ],
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

        console.log(products); // Pour débogage
        console.log(formulas); // Pour débogage

        initDevisGridWithData(products, formulas);
    }
}

function initDevisGridWithData(products, formulas) {
    const productRows = products.map(product => [product.name, product.quantity, product.id, 'product']);
    const formulaRows = formulas.map(formula => [formula.name, formula.quantity, formula.id, 'formula']);

    const gridData = productRows.concat(formulaRows);

    devisGrid.updateConfig({
        data: gridData
    }).forceRender();
}

function addEventListeners() {
    document.getElementById('addProductButton').addEventListener('click', () => addDevisItem('product'));
    document.getElementById('addFormulaButton').addEventListener('click', () => addDevisItem('formula'));
    document.getElementById('devisForm').addEventListener('submit', handleDevisFormSubmit);

    if (addProductButton) {
        addProductButton.addEventListener('click', () => addDevisItem('product'));
    }

    if (addFormulaButton) {
        addFormulaButton.addEventListener('click', () => addDevisItem('formula'));
    }

    if (devisForm) {
        devisForm.addEventListener('submit', handleDevisFormSubmit);
    }
}

function handleCustomerSelectChange() {
    const customerSelect = document.getElementById('devis_customer');
    const clientInfoDiv = document.getElementById('client-info');

    if (customerSelect) {
        customerSelect.addEventListener('change', function() {
            const customerId = this.value;
            fetchCustomerInfo(customerId, clientInfoDiv);
        });
    }
}

function fetchCustomerInfo(customerId, clientInfoDiv) {
    if (customerId) {
        fetch(`/customer/api/${customerId}`)
            .then(response => response.json())
            .then(data => displayCustomerInfo(data, clientInfoDiv))
            .catch(error => console.error('Erreur lors de la récupération des informations du client:', error));
    } else {
        clientInfoDiv.style.display = 'none';
    }
}

function displayCustomerInfo(data, clientInfoDiv) {
    clientInfoDiv.style.display = 'block';
    let clientInfoHtml = data.nameSociety ? `<p>Société : ${data.nameSociety}</p>` : `<p>Nom : ${data.name} ${data.lastName}</p>`;
    clientInfoHtml += `<p>Adresse : ${data.streetNumber} ${data.streetName}, ${data.postalCode} ${data.city}</p>`;
    clientInfoDiv.innerHTML = clientInfoHtml;
}

function addDevisItem(type) {
    let selectElement = document.getElementById(type + 'Select');
    let quantityElement = document.getElementById(type + 'Quantity');

    if (selectElement && quantityElement) {
        let itemId = selectElement.value;
        let itemName = selectElement.options[selectElement.selectedIndex].text;
        let quantity = quantityElement.value;

        if (itemId && quantity) {
            const dataExists = devisGrid.config.data.find(row => row.includes(itemId));
            if (!dataExists) {
                devisGrid.updateConfig({
                    data: devisGrid.config.data.concat([[itemName, quantity, itemId, type]])
                }).forceRender();
                selectElement.selectedIndex = 0;
                quantityElement.value = '';
            } else {
                alert("Cet élément a déjà été ajouté.");
            }
        } else {
            alert("Veuillez sélectionner un élément et saisir une quantité.");
        }
    }
}


window.updateDevisItemQuantity = function(inputElement) {
    const itemId = inputElement.getAttribute('data-id');
    const newQuantity = inputElement.value;

    const rowData = devisGrid.config.data.find(row => row[2] === itemId);
    if (rowData) {
        rowData[1] = newQuantity;
        devisGrid.forceRender();
    }
};

window.removeDevisItemFromGrid = function(itemId) {
    devisGrid.updateConfig({
        data: devisGrid.config.data.filter(row => row[2] !== itemId)
    }).forceRender();
};

function handleCollectionItems(collectionId, addButtonId, itemClass, updatePriceFunction) {
    const collectionHolder = document.getElementById(collectionId);
    if (!collectionHolder) {
        console.error("Element non trouvé:", collectionId);
        return;
    }
    collectionHolder.querySelectorAll('.' + itemClass).forEach(function(item) {
        addRemoveButton(item, itemClass);
        updatePriceFunction(item);
    });

    const addButton = document.getElementById(addButtonId);
    if (addButton) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            let newFormDiv = addFormToCollection(collectionHolder, itemClass);
            addRemoveButton(newFormDiv, itemClass);
            updatePriceFunction(newFormDiv);
        });
    } else {
        console.error("Bouton non trouvé:", addButtonId);
    }
}

function handleDevisFormSubmit(event) {
    event.preventDefault();
    const hiddenFieldsContainer = document.getElementById('hiddenFieldsContainer');
    hiddenFieldsContainer.innerHTML = ''; // Nettoyer les champs cachés existants

    devisGrid.config.data.forEach((row, index) => {
        if(row[3] === 'product') {
            // Pour les produits
            addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][product]`, row[2]); // ID du produit
            addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][quantity]`, row[1]); // Quantité
        } else if(row[3] === 'formula') {
            // Pour les formules
            addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][formula]`, row[2]); // ID de la formule
            addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][quantity]`, row[1]); // Quantité
        }
    });

    const devisForm = document.getElementById('devisForm');
    if (devisForm) {
        devisForm.submit();
    }
}

function addHiddenInput(container, name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    container.appendChild(input);
}


// Fonctions de mise à jour pour le prix des produits et des formules (comme précédemment)
