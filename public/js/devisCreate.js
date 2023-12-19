import { Grid, html } from "https://unpkg.com/gridjs?module";

let devisGrid;

document.addEventListener('DOMContentLoaded', function() {
    initDevisGrid();
    addEventListeners();
    handleCustomerSelectChange();
    const customSubmitButton = document.getElementById('customSubmitButton');
    if (customSubmitButton) {
        customSubmitButton.addEventListener('click', function(event) {
            event.preventDefault(); // Empêcher la soumission standard du formulaire
            const fakeSubmitEvent = new Event('submit', { cancelable: true, bubbles: true });
            handleDevisFormSubmit(fakeSubmitEvent); // Appeler votre fonction de soumission de formulaire
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
        data: []
    });
    devisGrid.render(document.getElementById("wrapper"));
}

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

function handleCustomerSelectChange() {
    const customerSelect = document.getElementById('devis_customer');
    const clientInfoDiv = document.getElementById('client-info');

    if (customerSelect) {
        customerSelect.addEventListener('change', function() {
            const customerId = this.value;
            console.log("Customer ID sélectionné:", customerId);
            fetchCustomerInfo(customerId, clientInfoDiv);
        });
    }
}
console.log("wouf");

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

let productIndex = 0;
let formulaIndex = 0;

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
        let itemName = selectElement.options[selectElement.selectedIndex].text;
        let quantity = parseInt(quantityElement.value);
        let pricePerUnitString = selectElement.options[selectElement.selectedIndex].getAttribute('data-price');
        let pricePerUnit = parseFloat(pricePerUnitString);
        let totalPrice = (quantity * pricePerUnit) / 100; // Calcul du prix total

        console.log("Prix par unité", pricePerUnit);
        console.log("Quantité", quantity);
        console.log("Prix total", totalPrice);

        const dataExists = devisGrid.config.data.find(row => row.includes(itemId));

        if (!dataExists) {
            console.log('Adding new item to grid:', {itemName, quantity, itemId, totalPrice, pricePerUnit, type});

            let index = type === 'product' ? productIndex : formulaIndex;
            const newRow = [itemName, quantity, itemId, totalPrice, pricePerUnit, type, index];
            devisGrid.updateConfig({
                data: devisGrid.config.data.concat([newRow])
            }).forceRender();

            addHiddenFieldsForGridItem(itemId, quantity, type, index, pricePerUnit);

            if (type === 'product') {
                productIndex++;
            } else if (type === 'formula') {
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


function addHiddenFieldsForGridItem(itemId, quantity, type, index,pricePerUnit) {
    const hiddenFieldsContainer = document.getElementById('hiddenFieldsContainer');
    const productId = `${type}-${index}-product`;
    const quantityId = `${type}-${index}-quantity`;
    const priceId = `${type}-${index}-price`;
    if (type === 'product') {
        addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][product]`, itemId, itemId,productId);
        addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][quantity]`, quantity,quantity,quantityId);
        // Dans addHiddenFieldsForGridItem ou une fonction similaire
        addHiddenInput(hiddenFieldsContainer, `devis[devisProducts][${index}][price]`, pricePerUnit.toFixed(2),pricePerUnit.toFixed(2),priceId);

    } else if (type === 'formula') {
        addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][formula]`, itemId, itemId,productId);
        addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][quantity]`, quantity,quantity,quantityId);
        // Dans addHiddenFieldsForGridItem ou une fonction similaire
        addHiddenInput(hiddenFieldsContainer, `devis[devisFormulas][${index}][price]`, pricePerUnit.toFixed(2),pricePerUnit.toFixed(2),priceId);

    }
}
console.log("done");


window.updateDevisItemQuantity = function (inputElement) {
    const rowId = inputElement.getAttribute('data-id');
    console.log("ID de la ligne:", rowId);
    const newQuantity = parseInt(inputElement.value);
    console.log("Nouvelle quantité:", newQuantity);


    // Trouvez la ligne dans la grille où l'ID correspond
    const rowIndex = devisGrid.config.data.findIndex(row => row[2] === rowId);
    console.log("Index de la ligne dans la grille:", rowIndex);

    if (rowIndex > -1) {
        const rowData = devisGrid.config.data[rowIndex];
        console.log("Données de la ligne:", rowData);

        // Mise à jour de la quantité dans la grille
        rowData[1] = newQuantity;

        // Type de l'élément (produit ou formule) et son index spécifique
        const itemType = rowData[5];
        const itemIndex = rowData[6];
        const quantityId = `${itemType}-${itemIndex}-quantity`;
        console.log("ID construit pour la quantité:", quantityId);

        // Sélectionner le bon champ caché pour la quantité
        const hiddenQuantityInput = document.getElementById(quantityId);
        console.log("Champ caché sélectionné pour la quantité:", hiddenQuantityInput);

        if (hiddenQuantityInput) {
            hiddenQuantityInput.value = newQuantity;
            console.log("Mise à jour effectuée pour la quantité.");
        } else {
            console.log("Aucun champ caché trouvé avec l'ID:", quantityId);
        }

        // Recalcul du prix total, si nécessaire
        const pricePerUnit = parseFloat(rowData[4]);
        const newTotalPrice = (newQuantity * pricePerUnit) / 100;
        rowData[3] = newTotalPrice.toFixed(2);
        console.log("Nouveau prix total:", newTotalPrice.toFixed(2));

        devisGrid.updateConfig({ data: devisGrid.config.data }).forceRender();
        updateTotalPrice();
    } else {
        console.log("Aucune ligne trouvée dans la grille avec l'ID:", rowId);
    }
};

window.removeDevisItemFromGrid = function (itemId) {
    // Mise à jour de la grille pour supprimer l'élément
    devisGrid.updateConfig({
        data: devisGrid.config.data.filter(row => row[2] !== itemId)
    }).forceRender();

    // Suppression des éléments correspondants du conteneur de champs cachés
    const hiddenFieldsContainer = document.getElementById('hiddenFieldsContainer');
    const inputs = hiddenFieldsContainer.querySelectorAll('input');

    inputs.forEach(input => {
        // Vérifier si l'input correspond à l'itemId et supprimer les éléments associés
        if (input.value === itemId) {
            // Supprimer l'input de produit/formule
            hiddenFieldsContainer.removeChild(input);

            // Supprimer l'input de la quantité associée
            const quantityInputName = input.name.replace('[product]', '[quantity]').replace('[formula]', '[quantity]');
            const quantityInput = hiddenFieldsContainer.querySelector(`input[name="${quantityInputName}"]`);
            if (quantityInput) {
                hiddenFieldsContainer.removeChild(quantityInput);
            }

            // Supprimer l'input du prix associé
            const priceInputName = input.name.replace('[product]', '[price]').replace('[formula]', '[price]');
            const priceInput = hiddenFieldsContainer.querySelector(`input[name="${priceInputName}"]`);
            if (priceInput) {
                hiddenFieldsContainer.removeChild(priceInput);
            }
        }
    });

    // Mise à jour du prix total
    updateTotalPrice();
};



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

    function calculateTotalDuePrice() {
        let totalPrice = parseFloat(document.getElementById('devis_totalPrice').value) || 0;
        let taxRate = parseFloat(document.getElementById('devis_taxe').value) || 0;

        let totalDuePrice = totalPrice * (1 + (taxRate / 100));

        const totalDuePriceInput = document.getElementById('devis_totalDuePrice');
        if (totalDuePriceInput) {
            totalDuePriceInput.value = totalDuePrice.toFixed(2);
        }
    }





