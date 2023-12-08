document.addEventListener('DOMContentLoaded', function() {
    handleCollectionItems('devisProducts', 'add_product_button', 'devisProductItem');
    handleCollectionItems('devisFormulas', 'add_formula_button', 'devisFormulaItem');
});

function handleCollectionItems(collectionId, addButtonId, itemClass) {
    const collectionHolder = document.getElementById(collectionId);

    collectionHolder.querySelectorAll('.' + itemClass).forEach(function(item) {
        addRemoveButton(item, itemClass);
    });

    document.getElementById(addButtonId).addEventListener('click', function(e) {
        e.preventDefault();
        let newFormDiv = addFormToCollection(collectionHolder, itemClass);
        addRemoveButton(newFormDiv, itemClass);
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
