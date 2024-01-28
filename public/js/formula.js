let currentGridInstance = null;
let productGridInstance = null;
let hiddenFieldIndex = 0;
let priceInput;

document.addEventListener("DOMContentLoaded", function () {
  initializeFormElements(document.body);
});

function initializeFormElements(context) {
  context.querySelectorAll(".productFormulaItem").forEach(function (item) {
    addRemoveButton(item);
  });
}

function addRemoveButton(divElement) {
  let removeButton = document.createElement("button");
  removeButton.type = "button";
  removeButton.textContent = "Supprimer le produit";
  removeButton.classList.add("remove_product_button");
  removeButton.addEventListener("click", function (e) {
    e.preventDefault();
    e.target.closest(".productFormulaItem").remove();
  });
  divElement.appendChild(removeButton);
}

function openFormulaModal(formulaId) {
  fetch(`/formula/api/${formulaId}`)
    .then((response) => response.text()) // Recevoir du HTML au lieu de JSON
    .then((html) => {
      const modalContent = document.getElementById(
        "formulaDetailsModalContentId"
      );
      modalContent.innerHTML = html;

      

      toggleModal("formulaDetailsModal", "formulaDetailsModalContentId");
      initializeFormulaView();
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération des données:", error)
    );
}

function initializeFormulaView() {
  const dataElement = document.getElementById("dataToShow");
  let data = null;
  if (dataElement) {
    const dataJSON = dataElement.textContent;
    try {
      data = JSON.parse(dataJSON);
    } catch (error) {
      console.error("Erreur lors du parsing des données JSON:", error);
    }
  }

  if (currentGridInstance) {
    currentGridInstance.destroy();
  }

  currentGridInstance = new gridjs.Grid({
    columns: ["Nom", "Quantité"],
    data: data.map((product) => [product.name, product.quantity]),
    search: true,
    pagination: true,
    sort: true,
    language: {
      search: {
        placeholder: "🔍 Rechercher...",
      },
    },
  }).render(document.getElementById("productGrid"));
}

function initializeCheckbox() {
  const adjustPriceCheckbox = document.querySelector(".adjust-price-checkbox");
  if (adjustPriceCheckbox) {
    priceInput = document.querySelector('input[name="formula[price]"]');
    togglePriceInput(adjustPriceCheckbox.checked);

    adjustPriceCheckbox.addEventListener("change", function () {
      togglePriceInput(this.checked);
    });
  } else {
    // Si la checkbox n'est pas trouvée, vous pourriez afficher un avertissement ou gérer cette situation autrement.
    console.warn("Checkbox 'Ajuster le prix' introuvable.");
  }
}

function openFormulaCreateModal() {
  fetch(`/formula/new`)
    .then((response) => response.text())
    .then((html) => {
      const modalBody = document.querySelector("#newModalModalContentId");
      console.log(modalBody);
      modalBody.innerHTML = html;

      initializeFormElements(modalBody);
      initializeCheckbox(); // Initialiser la checkbox ici
      if (!productGridInstance) {
        initializeProductGridCreate();
      }
      toggleModal("newModalId", "newModalModalContentId");

      // $('#formulaCreateModal').on('shown.bs.modal', function() {
      //     if (!productGridInstance) {
      //         initializeProductGridCreate();
      //     }
      // });

      // $('#formulaCreateModal').modal('show');
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function initializeProductGridCreate() {
  productGridInstance = new gridjs.Grid({
    columns: [
      "Produit",
      {
        name: "Quantité",
        formatter: (cell, row) => {
          return gridjs.html(
            `<input type="number" min="1" value="${cell}" data-product-id="${row.cells[3].data}" class="quantity-input" onchange="updateQuantity(this)">`
          );
        },
      },
      {
        name: "Prix",
        formatter: (cell, row) => {
          // Convertir la valeur en nombre si nécessaire
          let price = parseFloat(row.cells[2].data);
          if (!isNaN(price)) {
            return `${price.toFixed(2)} €`;
          } else {
            return "N/A"; // Ou une autre valeur par défaut si le prix n'est pas disponible
          }
        },
      }, // Ajout de la virgule ici
      {
        name: "Actions",
        formatter: (cell, row) => {
          return gridjs.html(
            `<button onclick="removeProductFromGrid('${row.cells[3].data}')">Supprimer</button>`
          );
        },
      },
    ],
    data: [],
    search: false,
    pagination: false,
    sort: false,
    language: {
      search: {
        placeholder: "🔍 Rechercher...",
      },
      pagination: {
        previous: "Précédent",
        next: "Suivant",
        showing: "Affichage de",
        results: () => "Produits",
      },
      noRecordsFound: "Veuillez sélectionner des produits",
    },
  });

  productGridInstance.render(document.getElementById("productGrid"));
  document
    .getElementById("addProductButton")
    .addEventListener("click", addProductToGridCreate);
}

function addProductToGridCreate() {
  const selectedProductElement = document.querySelector(
    'select[name="formula[selectedProduct]"]'
  );
  const quantityElement = document.getElementById("productQuantity");

  if (selectedProductElement && quantityElement) {
    const selectedProductId = selectedProductElement.value;
    const selectedProductName =
      selectedProductElement.options[selectedProductElement.selectedIndex].text;
    const quantity = parseInt(quantityElement.value); // Assurez-vous que la quantité est un nombre

    const priceString =
      selectedProductElement.options[
        selectedProductElement.selectedIndex
      ].getAttribute("data-price");
    let productPrice = parseFloat(priceString);
    if (!isNaN(productPrice)) {
      productPrice = productPrice / 100;
    } else {
      productPrice = 0;
    }

    const totalPrice = productPrice * quantity;

    if (selectedProductId && quantity) {
      if (!productExistsInGrid(selectedProductId)) {
        productGridInstance
          .updateConfig({
            data: productGridInstance.config.data.concat([
              [
                selectedProductName,
                quantity,
                totalPrice.toFixed(2),
                selectedProductId,
              ],
            ]),
          })
          .forceRender();

        addHiddenInput(selectedProductId, quantity);
        updateTotalPrice();
      } else {
        alert("Ce produit a déjà été ajouté.");
      }

      selectedProductElement.selectedIndex = 0;
      quantityElement.value = "";
    } else {
      alert("Veuillez sélectionner un produit et saisir une quantité.");
    }
  }
}

function togglePriceInput(isChecked) {
  if (isChecked) {
    // L'utilisateur ajuste le prix manuellement
    priceInput.readOnly = false;
    priceInput.style.pointerEvents = "auto"; // Permet les événements de souris
    priceInput.style.backgroundColor = "#fff"; // Fond blanc (modifiable selon votre design)
    priceInput.style.color = "#000"; // Texte noir (modifiable selon votre design)
  } else {
    // Le prix est calculé automatiquement
    priceInput.readOnly = true;
    priceInput.style.pointerEvents = "none"; // Désactive les événements de souris
    priceInput.style.backgroundColor = "#e9ecef"; // Grisé pour montrer qu'il est désactivé
    priceInput.style.color = "#6c757d"; // Couleur de texte grisé
    updateTotalPrice();
  }
}

function productExistsInGrid(productId) {
  return productGridInstance.config.data.some((row) => row.includes(productId));
}

function addHiddenInput(productId, quantity) {
  const container = document.getElementById("productDataContainer");
  if (container) {
    const productInput = document.createElement("input");
    productInput.type = "hidden";
    productInput.name = `formula[productFormulas][${productId}][product]`;
    productInput.value = productId;
    productInput.setAttribute("data-product-id", productId);
    container.appendChild(productInput);

    const quantityInput = document.createElement("input");
    quantityInput.type = "hidden";
    quantityInput.name = `formula[productFormulas][${productId}][quantity]`;
    quantityInput.value = quantity;
    quantityInput.setAttribute("data-product-id", productId);
    container.appendChild(quantityInput);
  }
}

function removeProductFromGrid(productId) {
  // Mettre à jour la configuration de la grille pour retirer la ligne
  productGridInstance
    .updateConfig({
      data: productGridInstance.config.data.filter(
        (row) => row[3] !== productId
      ),
    })
    .forceRender();

  // Retirer les inputs cachés correspondants
  const container = document.getElementById("productDataContainer");
  if (container) {
    const inputsToRemove = container.querySelectorAll(
      `[data-product-id="${productId}"]`
    );
    if (inputsToRemove.length > 0) {
      inputsToRemove.forEach((input) => container.removeChild(input));
    } else {
      console.error(
        `Aucun champ caché trouvé pour le produit avec l'ID ${productId}`
      );
    }
  }

  // Mettre à jour le prix total de la formule
  updateTotalPrice();
}

function updateQuantity(inputElement) {
  const productId = inputElement.getAttribute("data-product-id");
  const newQuantity = inputElement.value;

  const rowData = productGridInstance.config.data.find(
    (row) => row[2] === productId
  );
  if (rowData) {
    rowData[1] = newQuantity;
    productGridInstance.forceRender();
    updateTotalPrice();
  }

  const quantityInput = document.querySelector(
    `input[name="formula[productFormulas][${productId}][quantity]"]`
  );
  if (quantityInput) {
    quantityInput.value = newQuantity;
    updateTotalPrice();
  } else {
    console.error(
      `Aucun champ caché trouvé pour le produit avec l'ID ${productId}`
    );
  }
}

function updateTotalPrice() {
  // Ajouter une vérification pour s'assurer que productGridInstance existe et n'est pas null
  if (!productGridInstance || !productGridInstance.config) {
    console.warn("La grille des produits n'est pas initialisée.");
    return;
  }

  const adjustPriceCheckbox = document.querySelector(".adjust-price-checkbox");
  const priceField = document.getElementById("formula_price");

  // Vérifier si la checkbox "Ajuster le prix" est cochée
  if (adjustPriceCheckbox && !adjustPriceCheckbox.checked) {
    let totalPrice = 0;
    productGridInstance.config.data.forEach((row) => {
      let price = parseFloat(row[2]);
      if (!isNaN(price)) {
        totalPrice += price;
      }
    });

    // Mettre à jour le champ de prix uniquement si la checkbox n'est pas cochée
    if (priceField) {
      priceField.value = totalPrice.toFixed(2);
    }
  }
  // Si la checkbox est cochée, ne rien faire
}

function openFormulaEditModal(formulaId) {
  fetch(`/formula/${formulaId}/edit`)
    .then((response) => response.text())
    .then((html) => {
      const modalBody = document.querySelector("#formulaEditModalContentId");
      modalBody.innerHTML = html;

      if (!productGridInstance) {
        initializeProductGridCreate(); // Initialise un Grid.js vide
      }

      toggleModal("formulaEditModal", "formulaEditModalContentId");
    })
    .catch((error) => {
      console.error("Error loading the edit form:", error);
      alert("There was a problem loading the edit form. Please try again.");
    });
}

function toggleModal(modalId, modalContentId) {
  addClassToElement();
  let modal = document.getElementById(modalId);
  let modalContent = document.getElementById(modalContentId);

  if (modal.classList.contains("opacity-0")) {
    //OVERLAY
    // Ouvrir le modal
    modal.classList.remove("hidden");

    // Nécessaire pour permettre au navigateur de reconnaître que l'élément est maintenant visible
    // avant d'appliquer la transition d'opacité
    setTimeout(() => {
      modal.classList.remove(
        "opacity-0",
        "transition-opacity",
        "duration-500",
        "ease-in"
      );
      modal.classList.add(
        "opacity-100",
        "transition-opacity",
        "duration-500",
        "ease-out"
      );
    }, 10); // Un délai très court est généralement suffisant

    //MODAL CONTENT

    // Ouvrir le modal
    modalContent.classList.remove("hidden");

    // Permettre un bref délai pour que la classe 'hidden' soit complètement enlevée
    setTimeout(() => {
      modalContent.classList.remove("top-up");
      modalContent.classList.add("top-1/2");
    }, 10); // Un délai très court est généralement suffisant
  } else {
    //OVERLAY
    // Fermer le modal avec une transition d'opacité
    modal.classList.remove(
      "opacity-100",
      "transition-opacity",
      "duration-500",
      "ease-out"
    );
    modal.classList.add(
      "opacity-0",
      "transition-opacity",
      "duration-500",
      "ease-in"
    );

    // Ajouter `hidden` après que la transition soit terminée
    setTimeout(() => {
      modal.classList.add("hidden");
    }, 500); // La durée correspond à la durée de la transition

    //MODAL CONTENT
    // Animer la fermeture
    modalContent.classList.remove("top-1/2");
    modalContent.classList.add("top-up");

    // Ajouter 'hidden' après que la transition soit terminée
    setTimeout(() => {
      modalContent.classList.add("hidden");
    }, 500); // Assurez-vous que ce délai correspond à la durée de la transition
  }
}

function addClassToElement() {
  //Ce script est propre à ce formulaire : il rajoute une class à la div id formula qui ne peut être gérée dynamiquement depuis le form builder. #}
  let formulaDiv = document.getElementById("formula");
  if (formulaDiv) {
    formulaDiv.classList.add("flex", "flex-wrap", "gap-y-5", "gap-x-10");
  }
}
