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
      customerNameSociety.style.display = "";
      customerNameSocietyLabel.style.display = "";
      customerName.style.display = "none";
      customerNameLabel.style.display = "none";
      customerLastName.style.display = "none";
      customerLastNameLabel.style.display = "none";
      customerName.value = "";
      customerLastName.value = "";
    } else {
      customerNameSociety.style.display = "none";
      customerNameSocietyLabel.style.display = "none";
      customerName.style.display = "";
      customerNameLabel.style.display = "";
      customerLastName.style.display = "";
      customerLastNameLabel.style.display = "";

      customerNameSociety.value = "";
    }
  }

  switchClientType.addEventListener("change", updateFieldsVisibility);
  updateFieldsVisibility(); // Initialiser l'état des champs
}

document.addEventListener("DOMContentLoaded", initializeFormFields);

function openCustomerModal(customerId) {
  fetch(`/customer/api/${customerId}`)
    .then((response) => response.json())
    .then((data) => {
      const modalBody = document.querySelector(
        "#customerDetailsModal .modal-body"
      );
      let customerDetails;

      if (data.nameSociety) {
        customerDetails = `<p>Société: ${data.nameSociety}</p>`;
      } else {
        customerDetails = `<p>Nom: ${data.name}</p> <p>Prénom: ${data.lastName}</p>`;
      }

      customerDetails += `<p>Ville: ${data.city}</p>`;
      customerDetails += `<p>Adresse: ${data.streetNumber} ${data.streetName}, ${data.postalCode}</p>`;

      customerDetails += `<p>Devis en attente: ${data.devisCounts.pending}</p>`;
      customerDetails += `<p>Devis payés: ${data.devisCounts.paid}</p>`;
      customerDetails += `<p>Devis partiellement payés: ${data.devisCounts.partial}</p>`;
      customerDetails += `<p>Devis remboursés: ${data.devisCounts.refunded}</p>`;
      customerDetails += `<p>Total de devis: ${data.totalDevisCount}</p>`;

      modalBody.innerHTML = customerDetails;

      $("#customerDetailsModal").modal("show");
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération des données:", error)
    );
}

function openCustomerEditModal(customerId) {
  fetch(`/customer/${customerId}/edit`)
    .then((response) => response.text())
    .then((html) => {
      const modalBody = document.querySelector(
        "#customerEditModal .modal-body"
      );
      modalBody.innerHTML = html;
      initializeFormFields();
      $("#customerEditModal").modal("show");
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

      // Cette ligne doit être mise à jour pour correspondre à la manière dont vous affichez votre modal dans Twig
      toggleModal("newModalId", "newModalModalContentId");
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

//achraf

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

function toggleSwitch() {
  var checkbox = document.getElementById("switchClientType");
  var switchBackground = document.getElementById("switchBackground");
  var switchDot = document.getElementById("switchDot");

  checkbox.checked = !checkbox.checked;
  checkbox.dispatchEvent(new Event("change"));

  if (checkbox.checked) {
    switchBackground.classList.remove("bg-gray-200");
    switchBackground.classList.add("bg-green-400");
    switchDot.classList.add("translate-x-full");
  } else {
    switchBackground.classList.remove("bg-green-400");
    switchBackground.classList.add("bg-gray-200");
    switchDot.classList.remove("translate-x-full");
  }
}
