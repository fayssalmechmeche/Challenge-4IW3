document.addEventListener('DOMContentLoaded', function() {
    const collectionHolder = document.querySelector('.productFormulas');

    collectionHolder.querySelectorAll('.productFormulaItem').forEach(function(item) {
        addRemoveButton(item);
    });

    document.querySelector('.add_product_button').addEventListener('click', function(e) {
        e.preventDefault();
        let newFormDiv = addProductForm(collectionHolder);
        addRemoveButton(newFormDiv);
    });
});

function addProductForm(collectionHolder) {
    const prototype = collectionHolder.dataset.prototype;
    const index = collectionHolder.dataset.index;
    let newForm = prototype.replace(/__name__/g, index);
    collectionHolder.dataset.index = parseInt(index) + 1;

    let newFormDiv = document.createElement('div');
    newFormDiv.classList.add('productFormulaItem');
    newFormDiv.innerHTML = newForm;
    collectionHolder.appendChild(newFormDiv);

    return newFormDiv;
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
