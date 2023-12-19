function openProductCreateModal() {
  fetch(`/product/new`)
    .then((response) => response.text())
    .then((html) => {
      // Assurez-vous que cet ID correspond à l'ID de votre modal dans le template Twig
      const modalBody = document.querySelector("#newModalProduitContentId");
      modalBody.innerHTML = html;

      toggleModal("newModalProduitId", "newModalProduitContentId");
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openProductEditModal(productId) {
  fetch(`/product/${productId}/edit`)
    .then((response) => response.text())
    .then((html) => {
      const modalBody = document.querySelector("#productEditModalContent");
      modalBody.innerHTML = html;
      toggleModal("productEditModal", "productEditModalContent");
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openProductModal(productId) {
  fetch(`/product/api/${productId}`)
    .then((response) => response.json())
    .then((data) => {
      const modalBody = document.querySelector("#productDetailsModalContainer");
      let productDetails;

      productDetails = `<button class="absolute top-3 right-3 text-white font-medium bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg px-2 py-1" onclick="toggleModal('productDetailsModal', 'productDetailsModalContainer')">
      Fermer
    </button>`;
      productDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Nom du produit:
    </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.name}</p> </div>`;
      productDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Prix du produit en €:
    </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.price}</p> </div>`;
      productDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Catégorie de produit:
    </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.productCategory}</p> </div>`;
      modalBody.innerHTML = productDetails;

      toggleModal("productDetailsModal", "productDetailsModalContainer");
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération des données:", error)
    );
}

function toggleModal(modalId, modalContentId) {
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
      modalContent.classList.remove("top-[-1000px]");
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
    modalContent.classList.add("top-[-1000px]");

    // Ajouter 'hidden' après que la transition soit terminée
    setTimeout(() => {
      modalContent.classList.add("hidden");
    }, 500); // Assurez-vous que ce délai correspond à la durée de la transition
  }
}
