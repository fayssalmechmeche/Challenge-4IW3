function openProductCreateModal() {
  fetch(`/product/new`)
    .then((response) => response.text())
    .then((html) => {
      // Assurez-vous que cet ID correspond à l'ID de votre modal dans le template Twig
      const modalBody = document.querySelector("#newProductModalContentId");
      modalBody.innerHTML = html;

      toggleModal("newProductModalId", "newProductModalContentId");
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
  fetch(`/product/${productId}`)
    .then((response) => response.text()) // Recevoir du HTML au lieu de JSON
    .then((html) => {
      const modalContent = document.getElementById(
        "produitDetailsModalContentId"
      );
      modalContent.innerHTML = html;

      toggleModal("produitDetailsModal", "produitDetailsModalContentId");
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération des données:", error)
    );
}

function editFromShow(productId) {
  toggleModal("produitDetailsModal", "produitDetailsModalContentId");
  openProductEditModal(productId);
 }

//achraf

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

function toggleSwitch() {
  var checkbox = document.getElementById("switchClientType");
  var switchBackground = document.getElementById("switchBackground");
  var switchDot = document.getElementById("switchDot");

  checkbox.checked = !checkbox.checked;
  checkbox.dispatchEvent(new Event("change"));

  if (checkbox.checked) {
    switchBackground.classList.remove("bg-gray-200");
    switchBackground.classList.add("bg-nav-btn");
    switchDot.classList.add("translate-x-full");
  } else {
    switchBackground.classList.remove("bg-nav-btn");
    switchBackground.classList.add("bg-gray-200");
    switchDot.classList.remove("translate-x-full");
  }
}

function addClassToElement() {
  //Ce script est propre à ce formulaire : il rajoute une class à la div id product qui ne peut être gérée dynamiquement depuis le form builder. #}
  let formulaDiv = document.getElementById("product");
  if (formulaDiv) {
    formulaDiv.classList.add("flex", "flex-wrap", "gap-y-5", "gap-x-10");
  }
}


