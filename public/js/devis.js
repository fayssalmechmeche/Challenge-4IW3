import {
    Grid,
    html
} from "https://unpkg.com/gridjs?module";

document.addEventListener('DOMContentLoaded', function() {
    handleCollectionItems('devisProducts', 'add_product_button', 'devisProductItem', updateProductPriceDisplay);
    handleCollectionItems('devisFormulas', 'add_formula_button', 'devisFormulaItem', updateFormulaPriceDisplay);
    // Grid JS

    
    const grid = new Grid({
      columns: ["Produit", "Qté", "Prix unitaire",'Total HT'],
      data: [
        ["Formule 1", "50", "25 €",'1250 €'],
        ["Formule 2", "100", "20 €",'2000 €'],
        ["Formule 3", "150", "15 €",'2250 €'],
      ],
      style: {
        table: {
          border: '1px solid #ccc'
        },
        th: {
          'background-color': '#d4d4d4',
          color: '#000',
          'border-bottom': '1px solid #ccc',
          'text-align': 'center'
        },
        td: {
          'text-align': 'center'
        }
      }
    });
    
    
    grid.render(document.getElementById("wrapper"));





});

function handleCollectionItems(collectionId, addButtonId, itemClass, updatePriceFunction) {
    const collectionHolder = document.getElementById(collectionId);

    // Vérifie si l'élément collectionHolder existe
    if (!collectionHolder) {
        console.error("Element non trouvé:", collectionId);
        return; // Stoppe l'exécution de la fonction si l'élément n'existe pas
    }

    // Maintenant, on peut utiliser querySelectorAll en toute sécurité
    collectionHolder.querySelectorAll('.' + itemClass).forEach(function(item) {
        addRemoveButton(item, itemClass);
        updatePriceFunction(item);
    });

    // Ajout d'un bouton pour gérer les nouveaux éléments
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
