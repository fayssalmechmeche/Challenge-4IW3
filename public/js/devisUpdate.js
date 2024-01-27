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
        let totalPrice = (quantity * pricePerUnit) / 100;

        const dataExists = devisGrid.config.data.some(row => row[0] === itemName || row[2] === itemId);

        console.log("Prix par unité", pricePerUnit);
        console.log("Quantité", quantity);
        console.log("Prix total", totalPrice);

        if (!dataExists) {
            let index = type === 'product' ? productIndex : formulaIndex;


            console.log('Adding new item to grid:', {itemName, quantity, itemId, totalPrice, pricePerUnit,type, index});
            const newRow = [itemName, quantity, itemId, totalPrice, pricePerUnit,type,index];
            console.log("Nouvelle ligne ajoutée :", newRow);
            devisGrid.updateConfig({
                data: devisGrid.config.data.concat([newRow])
            }).forceRender();

            addHiddenFieldsForGridItem(itemId, quantity, type, index, pricePerUnit);

            if (type === 'product') {
                productIndex++;
            } else if (type === 'formula') {
                formulaIndex++;
            }
            console.log("Données actuelles de la grille :", devisGrid.config.data);
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

function addHiddenFieldsForGridItem(itemId, quantity, type, index, pricePerUnit) {
    const hiddenFieldsContainer = document.getElementById('hiddenFieldsContainer');
    const productId = `${type}-${index}-product`;
    const quantityId = `${type}-${index}-quantity`;
    const priceId = `${type}-${index}-price`;

    // Vérifiez que pricePerUnit est un nombre et, sinon, définissez-le sur 0
    pricePerUnit = (typeof pricePerUnit === 'number') ? pricePerUnit : 0;

    if (type === 'product') {
        addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][product]`, itemId,itemId, productId);
        addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][quantity]`, quantity,quantity, quantityId);
        addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][price]`, pricePerUnit.toFixed(2),pricePerUnit.toFixed(2), priceId);
    } else if (type === 'formula') {
        addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][formula]`, itemId,itemId, productId);
        addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][quantity]`, quantity,quantity, quantityId);
        addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][price]`, pricePerUnit.toFixed(2),pricePerUnit.toFixed(2), priceId);
    }
}


window.updateDevisItemQuantity = function (inputElement) {
    console.log("updateDevisItemQuantity called with inputElement:", inputElement);
    const rowIdStr = inputElement.getAttribute('data-id');
    console.log("Type et valeur de rowIdStr:", typeof rowIdStr);

    const newQuantity = parseInt(inputElement.value);
    console.log("Nouvelle quantité:", newQuantity);

    const rowIndex = devisGrid.config.data.findIndex(row => row[2].toString() === rowIdStr);
    console.log("Index de la ligne dans la grille:", rowIndex);

    if (rowIndex > -1) {
        const rowData = devisGrid.config.data[rowIndex];
        console.log("Données de la ligne:", rowData);

        // Mettre à jour la quantité dans la grille
        rowData[1] = newQuantity;

        const itemType = rowData[5];
        let hiddenIndex;

        // Trouver l'index directement à partir du nom des inputs cachés
        if (itemType === 'product') {
            const productInput = document.querySelector(`#hiddenFieldsContainer input[name^="devis[devisProducts]"][data-id="${rowIdStr}"]`);
            if (productInput) {
                const match = productInput.name.match(/\[devisProducts\]\[(\d+)\]/);
                if (match && match[1]) {
                    hiddenIndex = parseInt(match[1], 10);
                }
            }
        } else if (itemType === 'formula') {
            const formulaInput = document.querySelector(`#hiddenFieldsContainer input[name^="devis[devisFormulas]"][data-id="${rowIdStr}"]`);
            if (formulaInput) {
                const match = formulaInput.name.match(/\[devisFormulas\]\[(\d+)\]/);
                if (match && match[1]) {
                    hiddenIndex = parseInt(match[1], 10);
                }
            }
        }

        if (hiddenIndex !== undefined) {
            // Utiliser hiddenIndex pour identifier le champ caché de la quantité
            const quantityId = `${itemType}-${hiddenIndex}-quantity`;
            const hiddenQuantityInput = document.getElementById(quantityId);

            if (hiddenQuantityInput) {
                hiddenQuantityInput.value = newQuantity;
                console.log("Mise à jour effectuée pour la quantité dans le champ caché.");
            } else {
                console.log("Aucun champ caché trouvé avec l'ID:", quantityId);
            }

            // Recalcul du prix total
            const pricePerUnit = parseFloat(rowData[4]);
            const newTotalPrice = (newQuantity * pricePerUnit) / 100;
            rowData[3] = newTotalPrice.toFixed(2);
            console.log("Nouveau prix total:", newTotalPrice.toFixed(2));
        } else {
            console.log("Aucun index correspondant trouvé pour le type d'élément:", itemType);
        }

        // Mise à jour de la grille avec les nouvelles données
        devisGrid.updateConfig({ data: devisGrid.config.data }).forceRender();
        updateTotalPrice();
    } else {
        console.log("Aucune ligne trouvée dans la grille avec l'ID:", rowIdStr);
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
        if (input.value === itemId) {
            // Supprimez l'élément du produit/formule
            hiddenFieldsContainer.removeChild(input);

            // Supprimer l'input de la quantité associé
            const quantityInput = hiddenFieldsContainer.querySelector(`input[name="${input.name.replace('[product]', '[quantity]').replace('[formula]', '[quantity]')}"]`);
            if (quantityInput) {
                hiddenFieldsContainer.removeChild(quantityInput);
            }

            // Supprimer également l'input du prix associé
            const priceInput = hiddenFieldsContainer.querySelector(`input[name="${input.name.replace('[product]', '[price]').replace('[formula]', '[price]')}"]`);
            if (priceInput) {
                hiddenFieldsContainer.removeChild(priceInput);
            }
        }
    });

    // Filtrer les données pour enlever l'élément supprimé
    const itemIdNumber = Number(itemId);
    const newData = oldData.filter(row => row[2] !== itemId && row[2] !== itemIdNumber);
    console.log('New data after removal:', newData);

    devisGrid.updateConfig({
        data: newData
    }).forceRender();

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

function addHiddenInput(container, name, value, dataId = null,uniqueId) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    input.id = uniqueId;
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
    console.log("ici", devisForm);
    // Soumettez le formulaire
    // devisForm.submit();
}

function initDevisGrid(existingDevisItems) {
    console.log("initDevisGrid called");
    let initialData = [];
    console.log("existing devis item" ,existingDevisItems.products);

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
                formatter: (cell, row) => html(`<input class='rounded-xl w-24 h-10 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form' type="number" min="1" value="${cell}" data-id="${row.cells[2].data}" class="quantity-input" onchange="updateDevisItemQuantity(this)">`)
            },
            {
                name: 'Supprimer',
                formatter: (_, row) => html(`<button 
                class="text-white font-medium bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2" type="button" onclick="removeDevisItemFromGrid('${row.cells[2].data}')">Supprimer</button>`)
            },
            {
                name: 'Prix',
                formatter: (_, row) => {
                    console.log("Valeur de la cellule de prix :", row.cells[3].data);
                    let price = row.cells[3] ? parseFloat(row.cells[3].data) : 0;
                    return price ? `${price.toFixed(2)} €` : 'N/A';
                }
            }
        ],
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
    let index = type === 'product' ? productIndex++ : formulaIndex++;
    console.log("voici l'item : ",item);
    return [
        item.name,
        item.quantity,
        item.id, // Le bouton de suppression sera géré par le formatter de la colonne
        (item.quantity * item.price).toFixed(2) /100,
        item.price,
        type,
        index
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
