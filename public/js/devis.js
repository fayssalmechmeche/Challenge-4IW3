document.addEventListener('DOMContentLoaded', function() {
    handleCollectionItems('devisProducts', 'add_product_button', 'devisProductItem', updateProductPriceDisplay);
    handleCollectionItems('devisFormulas', 'add_formula_button', 'devisFormulaItem', updateFormulaPriceDisplay);
});

function handleCollectionItems(collectionId, addButtonId, itemClass, updatePriceFunction) {
    const collectionHolder = document.getElementById(collectionId);

    collectionHolder.querySelectorAll('.' + itemClass).forEach(function(item) {
        addRemoveButton(item, itemClass);
        updatePriceFunction(item);
    });

    document.getElementById(addButtonId).addEventListener('click', function(e) {
        e.preventDefault();
        let newFormDiv = addFormToCollection(collectionHolder, itemClass);
        addRemoveButton(newFormDiv, itemClass);
        updatePriceFunction(newFormDiv);
    });
}

function addFormToCollection(collectionHolder, itemClass) {
    const prototype = collectionHolder.dataset.prototype;
    const index = collectionHolder.dataset.index;
    let newForm = prototype.replace(/__name__/g, index);
    collectionHolder.dataset.index = parseInt(index) + 1;

    let newFormDiv = document.createElement('div');
    newFormDiv.classList.add(itemClass);
    newFormDiv.innerHTML = newForm;
    collectionHolder.appendChild(newFormDiv);

    return newFormDiv;
}

function addRemoveButton(divElement, itemClass) {
    let removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.textContent = 'Supprimer';
    removeButton.classList.add('remove_button');
    removeButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.target.closest('.' + itemClass).remove();
    });
    divElement.appendChild(removeButton);
}


// Fonction mise à jour pour le prix des produits
function updateProductPriceDisplay(divElement) {
    let selectElement = divElement.querySelector('.productSelect');
    if (selectElement) {
        selectElement.addEventListener('change', function() {
            let productId = selectElement.value;
            fetch('/devis/product/' + productId + '/price')
                .then(response => response.json())
                .then(data => {
                    divElement.querySelector('.productPriceDisplay').innerText = data.price;
                });
        });
    }
}

// Fonction mise à jour pour le prix des formules
function updateFormulaPriceDisplay(divElement) {
    let selectElement = divElement.querySelector('.formulaSelect');
    if (selectElement) {
        selectElement.addEventListener('change', function () {
            let formulaId = selectElement.value;
            fetch('/devis/formula/' + formulaId + '/price')
                .then(response => response.json())
                .then(data => {
                    divElement.querySelector('.formulaPriceDisplay').innerText = data.price;
                });
        });
    }
}
