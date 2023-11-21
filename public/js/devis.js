document.addEventListener('DOMContentLoaded', function() {
    const collectionHolder = document.getElementById('devisProducts');

    // Ajouter le bouton de suppression aux produits existants
    collectionHolder.querySelectorAll('.devisProductItem').forEach(function(item) {
        addRemoveButton(item);
    });

    // Gérer l'ajout d'un nouveau produit
    document.getElementById('add_product_button').addEventListener('click', function(e) {
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
    newFormDiv.classList.add('devisProductItem');
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
        e.target.closest('.devisProductItem').remove();
    });
    divElement.appendChild(removeButton);
}
