function initializeFormFields() {
  const switchClientType = document.getElementById("switchClientType");
  if (!switchClientType) return; // Sortir si l'élément switchClientType n'est pas trouvé

  const customerName = document.getElementById("customer_name");
  const customerLastName = document.getElementById("customer_lastName");
  const customerNameSociety = document.getElementById("customer_nameSociety");

  if (!customerName || !customerLastName || !customerNameSociety) {
    return;
  }

  const customerNameLabel = customerName.previousElementSibling;
  const customerLastNameLabel = customerLastName.previousElementSibling;
  const customerNameSocietyLabel = customerNameSociety.previousElementSibling;

  function updateFieldsVisibility() {
    if (switchClientType.checked) {
      // Masquer les éléments pour un client individuel
      customerNameSociety.style.display = "";
      customerNameSocietyLabel.style.display = "";
      // Masquer les champs et leurs labels
      customerName.style.display = "none";
      customerNameLabel.style.display = "none";
      customerLastName.style.display = "none";
      customerLastNameLabel.style.display = "none";
      // Masquer les div contenant les champs (si applicable)
      customerName.closest("div").style.display = "none";
      customerLastName.closest("div").style.display = "none";
      // Réinitialiser les valeurs des champs
      customerName.value = "";
      customerLastName.value = "";
    } else {
      // Afficher les éléments pour un client individuel
      customerNameSociety.style.display = "none";
      customerNameSocietyLabel.style.display = "none";
      // Afficher les champs et leurs labels
      customerName.style.display = "";
      customerNameLabel.style.display = "";
      customerLastName.style.display = "";
      customerLastNameLabel.style.display = "";
      // Afficher les div contenant les champs (si applicable)
      customerName.closest("div").style.display = "";
      customerLastName.closest("div").style.display = "";
      // Réinitialiser la valeur du champ société
      customerNameSociety.value = "";
    }
  }

  // Ajouter l'écouteur d'événement et initialiser
  switchClientType.addEventListener("change", updateFieldsVisibility);
  updateFieldsVisibility(); // Initialiser l'état des champs
}

document.addEventListener("DOMContentLoaded", initializeFormFields);

function openCustomerModal(customerId) {
  fetch(`/customer/api/${customerId}`)
    .then((response) => response.json())
    .then((data) => {
      const modalBody = document.querySelector(
        "#customerDetailsModalContainer"
      );
      let customerDetails;

      customerDetails = `<button class="absolute top-3 right-3 text-white font-medium bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg px-2 py-1" onclick="toggleModal('customerDetailsModal', 'customerDetailsModalContainer')">
        Fermer
      </button>`;

      if (data.nameSociety) {
        customerDetails += `<div class="flex flex-col justify-start items-start mt-3"> <p class="font-semibold">Société:
        </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.nameSociety}</p> </div>`;
      } else {
        customerDetails += `<div class="flex flex-col justify-start items-start mt-3"> <p class="font-semibold">Nom:
        </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.name}</p> <p>Prénom: ${data.lastName}</p> </div>`;
      }

      customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Ville:
      </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.city}</p> </div>`;
      customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Adresse:
      </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.streetNumber} ${data.streetName}, ${data.postalCode}</p> </div>`;

      customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Devis en attente:
      </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.devisCounts.pending}</p> </div>`;
      customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Devis payés:
      </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.devisCounts.paid}</p> </div>`;
      customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Devis partiellement payés:
      </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.devisCounts.partial}</p> </div>`;
      customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Devis remboursés:
      </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.devisCounts.refunded}</p> </div>`;
      customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Total de devis:
      </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.totalDevisCount}</p> </div>`;

      modalBody.innerHTML = customerDetails;

      toggleModal("customerDetailsModal", "customerDetailsModalContainer");
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération des données:", error)
    );
}

function openCustomerEditModal(customerId) {
  fetch(`/customer/${customerId}/edit`)
    .then((response) => response.text())
    .then((html) => {
      const modalBody = document.querySelector("#customerEditModalContent");
      modalBody.innerHTML = html;
      initializeFormFields();
      toggleModal("customerEditModal", "customerEditModalContent");
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openCustomerCreateModal() {
  fetch(`/customer/new`)
    .then((response) => response.text())
    .then((html) => {
      // Assurez-vous que cet ID correspond à l'ID de votre modal dans le template Twig
      const modalBody = document.querySelector("#newModalModalContentId");
      modalBody.innerHTML = html;
      initializeFormFields();

      toggleModal("newModalId", "newModalModalContentId");
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
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
  //Ce script est propre à ce formulaire : il rajoute une class à la div id customer qui ne peut être gérée dynamiquement depuis le form builder. #}
  let customerDiv = document.getElementById("customer");
  console.log(customerDiv);
  if (customerDiv) {
    customerDiv.classList.add("flex", "flex-wrap", "gap-y-5", "gap-x-10");
  }
}
