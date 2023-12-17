import { Grid, html } from "https://unpkg.com/gridjs?module";

let devisGrid;
let productIndex;
let formulaIndex;

document.addEventListener('DOMContentLoaded', () => {
    // Sélectionner tous les éléments qui commencent par 'devis_devisProducts_' et 'devis_devisFormulas_'
    const productElements = document.querySelectorAll('[id^="devis_devisProducts_"]');
    const formulaElements = document.querySelectorAll('[id^="devis_devisFormulas_"]');

    // Supprimer tous les éléments sélectionnés pour les produits
    productElements.forEach(element => element.remove());

    // Supprimer tous les éléments sélectionnés pour les formules
    formulaElements.forEach(element => element.remove());
});

function addEventListeners() {
    document.getElementById('addProductButton').addEventListener('click', () => addDevisItem('product'));
    document.getElementById('addFormulaButton').addEventListener('click', () => addDevisItem('formula'));
    document.getElementById('devisForm').addEventListener('submit', handleDevisFormSubmit);

    const taxeInput = document.getElementById('devis_taxe');
    if (taxeInput) {
        taxeInput.addEventListener('change', function() {
            calculateTotalDuePrice();
        });
    }
    calculateTotalDuePrice();
}

export function init(existingDevisItems, initialCustomerId) {
    console.log(existingDevisItems);
    productIndex = existingDevisItems.products ? existingDevisItems.products.length : 0;
    formulaIndex = existingDevisItems.formulas ? existingDevisItems.formulas.length : 0;

    document.addEventListener('DOMContentLoaded', function () {
        initDevisGrid(existingDevisItems);
        addEventListeners();
        handleCustomerSelectChange();

        if (initialCustomerId) {
            fetchCustomerInfo(initialCustomerId, document.getElementById('client-info'));
        }

        const customSubmitButton = document.getElementById('customSubmitButton');
        if (customSubmitButton) {
            customSubmitButton.addEventListener('click', function (event) {
                event.preventDefault();
                const fakeSubmitEvent = new Event('submit', {cancelable: true, bubbles: true});
                handleDevisFormSubmit(fakeSubmitEvent);
            });
        }
    });
}

function addDevisItem(type) {
    console.log('addDevisItem called with type:', type);
    let selectElement;
    if (type === 'product') {
        selectElement = document.getElementById('productSelect');
    } else if (type === 'formula') {
        selectElement = document.getElementById('formulaSelect');
    }

    let quantityElement = document.getElementById(type + 'Quantity');
    console.log("Element de quantité", quantityElement);
    console.log("Element de sélection", selectElement);

    if (selectElement && quantityElement) {
        let itemId = selectElement.value;
        console.log("ID de l'élément", itemId);
        let itemName = selectElement.options[selectElement.selectedIndex].text;
        let quantity = parseInt(quantityElement.value);
        let pricePerUnitString = selectElement.options[selectElement.selectedIndex].getAttribute('data-price');
        let pricePerUnit = parseFloat(pricePerUnitString);
        let totalPrice = quantity * pricePerUnit; // Calcul du prix total

        const dataExists = devisGrid.config.data.some(row => row[0] === itemName || row[2] === itemId);

        console.log("Prix par unité", pricePerUnit);
        console.log("Quantité", quantity);
        console.log("Prix total", totalPrice);



        if (!dataExists) {
            console.log('Adding new item to grid:', {itemName, quantity, itemId, totalPrice, pricePerUnit});
            const newRow = [itemName, quantity, itemId, totalPrice, pricePerUnit]; // Utilisation du prix total
            console.log("Nouvelle ligne ajoutée :", newRow);
            devisGrid.updateConfig({
                data: devisGrid.config.data.concat([newRow])
            }).forceRender();

            // Utilisez l'index approprié en fonction du type
            if(type === 'product') {
                addHiddenFieldsForGridItem(itemId, quantity, type, productIndex);
                productIndex++;
            } else if(type === 'formula') {
                addHiddenFieldsForGridItem(itemId, quantity, type, formulaIndex);
                formulaIndex++;
            }

            selectElement.selectedIndex = 0;
            quantityElement.value = '';
        } else {
            alert("Cet élément a déjà été ajouté.");
        }
    } else {
        alert("Veuillez sélectionner un élément et saisir une quantité.");
    }
    updateTotalPrice();
}

function addHiddenFieldsForGridItem(itemId, quantity, type, index) {
    const hiddenFieldsContainer = document.getElementById('hiddenFieldsContainer');
    if (type === 'product') {
        addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][product]`, itemId, itemId);
        addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][quantity]`, quantity);
    } else if (type === 'formula') {
        addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][formula]`, itemId, itemId);
        addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][quantity]`, quantity);
    }
}

window.updateDevisItemQuantity = function (inputElement) {
    const rowId = inputElement.getAttribute('data-id');
    const newQuantity = parseInt(inputElement.value);
    const rowData = devisGrid.config.data.find(row => row[2] === rowId);

    if (rowData) {
        // Mise à jour de la quantité dans la grille
        rowData[1] = newQuantity;

        // Mise à jour de la quantité dans les champs cachés
        const hiddenQuantityInput = document.querySelector(`input[name="devis[devisProducts][${rowId}][quantity]"]`);
        if (hiddenQuantityInput) {
            hiddenQuantityInput.value = newQuantity;
        }

        // Recalcul du prix total
        const pricePerUnit = parseFloat(rowData[4]);
        const newTotalPrice = newQuantity * pricePerUnit;
        rowData[3] = newTotalPrice; // Mise à jour du prix total

        devisGrid.forceRender();
        updateTotalPrice();
    }
};

window.removeDevisItemFromGrid = function (itemId) {
    console.log('removeDevisItemFromGrid called with itemId:', itemId);
    const oldData = devisGrid.config.data;
    console.log('Old data:', oldData);

    // Suppression des éléments correspondants du conteneur de champs cachés
    const hiddenFieldsContainer = document.getElementById('hiddenFieldsContainer');
    const inputs = hiddenFieldsContainer.querySelectorAll('input');

    inputs.forEach(input => {
        // Supposons que le 'itemId' est contenu dans la valeur de l'input
        if (input.value === itemId) {
            hiddenFieldsContainer.removeChild(input);
            // Supprimer également l'input de la quantité associé
            const quantityInput = hiddenFieldsContainer.querySelector(`input[name="${input.name.replace('[product]', '[quantity]').replace('[formula]', '[quantity]')}"]`);
            if (quantityInput) {
                hiddenFieldsContainer.removeChild(quantityInput);
            }
        }
    });
    const itemIdNumber = Number(itemId);
    const newData = oldData.filter(row => row[2] !== itemId && row[2] !== itemIdNumber);
    console.log('New data after removal:', newData);

    devisGrid.updateConfig({
        data: newData
    }).forceRender();
    removeHiddenInputsForItem(itemId);

    // Mise à jour du prix total
    updateTotalPrice();
};

function removeHiddenInputsForItem(itemId) {
    const hiddenFieldsContainer = document.getElementById('hiddenFieldsContainer');
    const inputs = hiddenFieldsContainer.querySelectorAll(`input[data-id="${itemId}"]`);

    inputs.forEach(input => {
        hiddenFieldsContainer.removeChild(input);
    });
}

function handleCollectionItems(collectionId, addButtonId, itemClass, updatePriceFunction) {
    const collectionHolder = document.getElementById(collectionId);
    if (!collectionHolder) {
        console.error("Element non trouvé:", collectionId);
        return;
    }
    collectionHolder.querySelectorAll('.' + itemClass).forEach(function (item) {
        addRemoveButton(item, itemClass);
        updatePriceFunction(item);
    });

    const addButton = document.getElementById(addButtonId);
    if (addButton) {
        addButton.addEventListener('click', function (e) {
            e.preventDefault();
            let newFormDiv = addFormToCollection(collectionHolder, itemClass);
            addRemoveButton(newFormDiv, itemClass);
            updatePriceFunction(newFormDiv);
        });
    } else {
        console.error("Bouton non trouvé:", addButtonId);
    }
}

function addHiddenInput(container, name, value, dataId = null) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    if (dataId !== null) {
        input.setAttribute('data-id', dataId);
    }
    container.appendChild(input);
}


function updateTotalPrice() {
    let total = 0;
    devisGrid.config.data.forEach(row => {
        total += parseFloat(row[3]);
    });

    const totalPriceInput = document.getElementById('devis_totalPrice');
    const totalDuePriceInput = document.getElementById('devis_totalDuePrice');
    const taxeInput = document.getElementById('devis_taxe');

    if (totalPriceInput) {
        totalPriceInput.value = total.toFixed(2);
    }

    // Calcul du total TTC
    if (totalDuePriceInput && taxeInput) {
        let taxRate = parseFloat(taxeInput.value) || 0;
        let totalDuePrice = total * (1 + taxRate / 100);
        totalDuePriceInput.value = totalDuePrice.toFixed(2);
    }
    calculateTotalDuePrice();
}

function handleDevisFormSubmit(event) {
    event.preventDefault();
    const hiddenJsonInput = document.createElement('input');
    hiddenJsonInput.type = 'hidden';
    hiddenJsonInput.name = 'devisProductsJson';
    // Ajoutez le champ caché au formulaire
    const devisForm = document.getElementById('devisForm');
    devisForm.appendChild(hiddenJsonInput);
    console.log(devisForm);
    // Soumettez le formulaire
    devisForm.submit();
}

function initDevisGrid(existingDevisItems) {
    console.log("initDevisGrid called");
    let initialData = [];
    console.log(existingDevisItems);

    // Traitement des produits et formules existants
    if (existingDevisItems.products) {
        initialData.push(...existingDevisItems.products.map(item => formatRow(item, 'product')));
    }
    if (existingDevisItems.formulas) {
        initialData.push(...existingDevisItems.formulas.map(item => formatRow(item, 'formula')));
    }

    // Initialisation de Grid.js avec les données existantes
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
            },
            'Prix Total'
        ],
        data: initialData
    });
    existingDevisItems.products.forEach(item => {
        addHiddenFieldsForGridItem(item.id, item.quantity, 'product', productIndex);
        productIndex++;
    });

    existingDevisItems.formulas.forEach(item => {
        addHiddenFieldsForGridItem(item.id, item.quantity, 'formula', formulaIndex);
        formulaIndex++;
    });
    devisGrid.render(document.getElementById("wrapper"));
}

function formatRow(item, type) {
    return [
        item.name,
        item.quantity,
        item.id, // Le bouton de suppression sera géré par le formatter de la colonne
        (item.quantity * item.price).toFixed(2)
    ];
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
    clientInfoHtml += `<p>Email : ${data.email}</p>`;
    clientInfoHtml += `<p>Téléphone : ${data.phone}</p>`;

    clientInfoDiv.innerHTML = clientInfoHtml;
}

function calculateTotalDuePrice() {
    let totalPrice = parseFloat(document.getElementById('devis_totalPrice').value) || 0;
    let taxRate = parseFloat(document.getElementById('devis_taxe').value) || 0;

    let totalDuePrice = totalPrice * (1 + (taxRate / 100));

    const totalDuePriceInput = document.getElementById('devis_totalDuePrice');
    if (totalDuePriceInput) {
        totalDuePriceInput.value = totalDuePrice.toFixed(2);
    }
}
