let currentGridInstance = null;
let productGridInstance = null;
let hiddenFieldIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    initializeFormElements(document.body);
});

function initializeFormElements(context) {
    context.querySelectorAll('.productFormulaItem').forEach(function(item) {
        addRemoveButton(item);
    });
}

function addRemoveButton(divElement) {
    let removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.textContent = 'Supprimer le produit';
    removeButton.classList.add('remove_product_button');
    removeButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.target.closest('.productFormulaItem').remove();
    });
    divElement.appendChild(removeButton);
}

function openFormulaModal(formulaId) {
    fetch(`/formula/api/${formulaId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('formulaName').textContent = data.name;
            document.getElementById('formulaPrice').textContent = data.price;

            if (currentGridInstance) {
                currentGridInstance.destroy();
            }

            currentGridInstance = new gridjs.Grid({
                columns: ['Nom', 'Quantité'],
                data: data.products.map(product => [product.name, product.quantity]),
                search: true,
                pagination: true,
                sort: true,
                language: {
                    'search': {
                        'placeholder': '🔍 Rechercher...'
                    }
                }
            });

            currentGridInstance.render(document.getElementById('productsTable'));
            $('#formulaDetailsModal').modal('show');
        })
        .catch(error => console.error('Erreur lors de la récupération des données:', error));
}

function openFormulaCreateModal() {
    fetch(`/formula/new`)
        .then(response => response.text())
        .then(html => {
            const modalBody = document.querySelector('#formulaCreateModal .modal-body');
            modalBody.innerHTML = html;
            initializeFormElements(modalBody);

            $('#formulaCreateModal').on('shown.bs.modal', function() {
                if (!productGridInstance) {
                    initializeProductGridCreate();
                }
            });

            $('#formulaCreateModal').modal('show');
        })
        .catch(error => console.error('Erreur lors de la récupération du formulaire:', error));
}

function initializeProductGridCreate() {
    productGridInstance = new gridjs.Grid({
        columns: [
            'Produit',
            {
                name: 'Quantité',
                formatter: (cell, row) => {
                    return gridjs.html(
                        `<input type="number" min="1" value="${cell}" data-product-id="${row.cells[2].data}" class="quantity-input" onchange="updateQuantity(this)">`
                    );
                }
            },
            {
                name: 'Supprimer',
                formatter: (_, row) => gridjs.html(`<button onclick="removeProductFromGrid('${row.cells[2].data}')">Supprimer</button>`)
            }
        ],
        data: [],
        search: false,
        pagination: false,
        sort: false,
        language: {
            'search': {
                'placeholder': '🔍 Rechercher...'
            },
            'pagination': {
                'previous': 'Précédent',
                'next': 'Suivant',
                'showing': 'Affichage de',
                'results': () => 'Produits'
            },
            'noRecordsFound': 'Veuillez sélectionner des produits'
        }
    });

    productGridInstance.render(document.getElementById('productGrid'));
    document.getElementById('addProductButton').addEventListener('click', addProductToGridCreate);
}

function addProductToGridCreate() {
    const selectedProductElement = document.querySelector('select[name="formula[selectedProduct]"]');
    const quantityElement = document.getElementById('productQuantity');

    if (selectedProductElement && quantityElement) {
        const selectedProductId = selectedProductElement.value;
        const selectedProductName = selectedProductElement.options[selectedProductElement.selectedIndex].text;
        const quantity = quantityElement.value;

        if (selectedProductId && quantity) {
            if (!productExistsInGrid(selectedProductId)) {
                productGridInstance.updateConfig({
                    data: productGridInstance.config.data.concat([[selectedProductName, quantity, selectedProductId]])
                }).forceRender();

                addHiddenInput(selectedProductId, quantity);
            } else {
                alert("Ce produit a déjà été ajouté.");
            }

            selectedProductElement.selectedIndex = 0;
            quantityElement.value = '';
        } else {
            alert("Veuillez sélectionner un produit et saisir une quantité.");
        }
    }
}

function productExistsInGrid(productId) {
    return productGridInstance.config.data.some(row => row.includes(productId));
}

function addHiddenInput(productId, quantity) {
    const container = document.getElementById('productDataContainer');
    if (container) {
        const productInput = document.createElement('input');
        productInput.type = 'hidden';
        productInput.name = `formula[productFormulas][${productId}][product]`;
        productInput.value = productId;
        productInput.setAttribute('data-product-id', productId);
        container.appendChild(productInput);

        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = `formula[productFormulas][${productId}][quantity]`;
        quantityInput.value = quantity;
        quantityInput.setAttribute('data-product-id', productId);
        container.appendChild(quantityInput);
    }
}

function removeProductFromGrid(productId) {
    productGridInstance.updateConfig({
        data: productGridInstance.config.data.filter(row => row[2] !== productId)
    }).forceRender();

    const container = document.getElementById('productDataContainer');
    if (container) {
        const inputsToRemove = container.querySelectorAll(`[data-product-id="${productId}"]`);
        if (inputsToRemove.length > 0) {
            inputsToRemove.forEach(input => container.removeChild(input));
        } else {
            console.error(`Aucun champ caché trouvé pour le produit avec l'ID ${productId}`);
        }
    }
}

function updateQuantity(inputElement) {
    const productId = inputElement.getAttribute('data-product-id');
    const newQuantity = inputElement.value;

    const rowData = productGridInstance.config.data.find(row => row[2] === productId);
    if (rowData) {
        rowData[1] = newQuantity;
        productGridInstance.forceRender();
    }

    const quantityInput = document.querySelector(`input[name="formula[productFormulas][${productId}][quantity]"]`);
    if (quantityInput) {
        quantityInput.value = newQuantity;
    } else {
        console.error(`Aucun champ caché trouvé pour le produit avec l'ID ${productId}`);
    }
}

function openFormulaEditModal(formulaId) {
    fetch(`/formula/${formulaId}/edit`)
        .then(response => response.text())
        .then(html => {
            const modalBody = document.querySelector('#openFormulaEditModal .modal-body');
            modalBody.innerHTML = html;

            if (!productGridInstance) {
                initializeProductGridCreate(); // Initialise un Grid.js vide
            }

            $('#openFormulaEditModal').modal('show');
        })
        .catch(error => {
            console.error('Error loading the edit form:', error);
            alert('There was a problem loading the edit form. Please try again.');
        });
}



