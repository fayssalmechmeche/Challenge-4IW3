function initializeFormFields(editMode = false) {
  if (editMode) {
    let typeSociter = document.getElementById("ClientTypeHidden");
    let switchType = document.getElementById("switchToClick");

    // Vérification si les éléments existent
    if (typeSociter && switchType) {
      if (typeSociter.value !== "") {
        console.log(typeSociter);
        switchType.click();
      }
    }
  }

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

// function openCustomerModal(customerId) {
//   fetch(`/customer/api/${customerId}`)
//     .then((response) => response.json())
//     .then((data) => {
//       const modalBody = document.querySelector(
//         "#customerDetailsModalContainer"
//       );
//       let customerDetails;

//       customerDetails = `<button class="absolute top-3 right-3 text-white font-medium bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg px-2 py-1" onclick="toggleModal('customerDetailsModal', 'customerDetailsModalContainer')">
//         Fermer
//       </button>`;

//       if (data.nameSociety) {
//         customerDetails += `<div class="flex flex-col justify-start items-start mt-3"> <p class="font-semibold">Société:
//         </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.nameSociety}</p> </div>`;
//       } else {
//         customerDetails += `<div class="flex flex-col justify-start items-start mt-3"> <p class="font-semibold">Nom:
//         </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.name}</p> <p>Prénom: ${data.lastName}</p> </div>`;
//       }

//       customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Ville:
//       </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.city}</p> </div>`;
//       customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Adresse:
//       </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.streetNumber} ${data.streetName}, ${data.postalCode}</p> </div>`;

//       customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Devis en attente:
//       </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.devisCounts.pending}</p> </div>`;
//       customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Devis payés:
//       </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.devisCounts.paid}</p> </div>`;
//       customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Devis partiellement payés:
//       </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.devisCounts.partial}</p> </div>`;
//       customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Devis remboursés:
//       </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.devisCounts.refunded}</p> </div>`;
//       customerDetails += `<div class="flex flex-col justify-start items-start"> <p class="font-semibold">Total de devis:
//       </p> <p class="'rounded-xl w-full h-10  p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent"> ${data.totalDevisCount}</p> </div>`;

//       modalBody.innerHTML = customerDetails;

//       toggleModal("customerDetailsModal", "customerDetailsModalContainer");
//     })
//     .catch((error) =>
//       console.error("Erreur lors de la récupération des données:", error)
//     );
// }

function openCustomerEditModal(customerId) {
  fetch(`/customer/${customerId}/edit`)
    .then((response) => response.text())
    .then((html) => {
      const modalBody = document.querySelector("#customerEditModalContent");
      modalBody.innerHTML = html;
      initializeFormFields(true);
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
      sheeesh();
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openCustomerModal(customerId) {
  fetch(`/customer/${customerId}`)
    .then((response) => response.text()) // Recevoir du HTML au lieu de JSON
    .then((html) => {
      const modalContent = document.getElementById(
        "customerDetailsModalContainer"
      );
      modalContent.innerHTML = html;
      toggleModal("customerDetailsModal", "customerDetailsModalContainer");
      sheeesh();
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération des données:", error)
    );
}

function editFromShow(customerId) {
  toggleModal("customerDetailsModal", "customerDetailsModalContainer");
  openCustomerEditModal(customerId);
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
  if (customerDiv) {
    customerDiv.classList.add("flex", "flex-wrap", "gap-y-5", "gap-x-10");
  }
}

document.addEventListener('DOMContentLoaded', function() {
  // Lire les messages flash de l'élément script
  const flashMessagesElement = document.getElementById('flash-messages');
  const flashMessages = flashMessagesElement ? JSON.parse(flashMessagesElement.textContent) : {};

  if (flashMessages) {
    Object.keys(flashMessages).forEach(type => {
      flashMessages[type].forEach(message => {
        // Utilisez Toastify ou une fonction personnalisée pour afficher le message
        Toastify({
          text: message,
          className: type, // 'success' ou 'error'
          // Vous pouvez personnaliser davantage Toastify ici
        }).showToast();
      });
    });
  }
});

document.addEventListener('DOMContentLoaded', function() {
  // Supposant que votre modale est directement dans le body ou dans un élément qui ne change pas
  document.getElementById('newModalId').addEventListener('click', function(event) {
    console.log('click détecté');s
    // Vérifie si l'élément cliqué (ou un de ses parents) est le bouton vérifier
    var target = event.target;
    while (target != this) {
      if (target.matches('.check')) { // Assurez-vous que le sélecteur correspond à votre bouton
        // Votre code pour traiter le click sur le bouton vérifier
        console.log('Bouton vérifier cliqué!');
        break; // Sort de la boucle une fois l'élément trouvé et traité
      }
      target = target.parentNode;
    }
  });
});

function sheeesh() {
  const form = document.querySelector('form.w-full');
  document.getElementById("save-button").addEventListener("click", function(event) {
      event.preventDefault(); // Empêche le comportement par défaut du bouton (envoi du formulaire)
      // Ajoutez ici votre logique JavaScript supplémentaire si nécessaire
    let isValid = true;
    let messages = [];

    // Récupération des valeurs du formulaire
    const name = document.getElementById('customer_name').value;
    const lastName = document.getElementById('customer_lastName').value;
    const streetName = document.getElementById('customer_streetName').value;
    const streetNumber = document.getElementById('customer_streetNumber').value;
    const customerCity = document.getElementById('customer_city').value;
    const customerPostalCode = document.getElementById('customer_postalCode').value;
    const nameSociety = document.getElementById('customer_nameSociety').value;
    const email = document.getElementById('customer_email').value;
    const phoneNumber = document.getElementById('customer_phoneNumber').value;

    // Validation du nom et du prénom ou du nom de la société
    if ((!name || !lastName) && !nameSociety) {
      messages.push('Vous devez remplir soit le Nom/Prénom soit le Nom de la Société.');
        highlightInput(document.getElementById('customer_name'));
        highlightInput(document.getElementById('customer_lastName'));
        highlightInput(document.getElementById('customer_nameSociety'));
      isValid = false;
    }

    // Validation de l'adresse email
    if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
      messages.push('Veuillez renseigner une adresse e-mail valide.');
        highlightInput(document.getElementById('customer_email'));
      isValid = false;
    }

    // Validation du numéro de téléphone
    if (!phoneNumber || !/^\d{10}$/.test(phoneNumber)) {
      messages.push('Le numéro de téléphone doit comporter 10 chiffres.');
        highlightInput(document.getElementById('customer_phoneNumber'));
      isValid = false;
    }

    if (!customerPostalCode || !/^\d{5}$/.test(customerPostalCode)) {
      messages.push('Le code postal doit comporter 5 chiffres.');
      highlightInput(document.getElementById('customer_postalCode'));
      isValid = false;
    }

    if ( !streetName) {
      messages.push('Veuillez remplir un nom de rue.');
        highlightInput(document.getElementById('customer_streetName'));
      isValid = false;
    }

    if ( !streetNumber) {
      messages.push('Veuillez remplir un numéro de rue.');
      highlightInput(document.getElementById('customer_streetNumber'));
      isValid = false;
    }

    if ( !customerCity) {
      messages.push('Veuillez remplir une ville.');
      highlightInput(document.getElementById('customer_city'));
      isValid = false;
    }

    // Affichage des messages d'erreur ou soumission du formulaire
    if (!isValid) {
      messages.forEach(message => {
        Toastify({
          text: message,
          duration: 6000,
          close: true,
          gravity: "top", // `top` or `bottom`
          position: "right", // `left`, `center` or `right`
          backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)",
          stopOnFocus: true, // Prevents dismissing of toast on hover
        }).showToast();
      });
    } else {
      console.log('Validation réussie, soumission du formulaire.');
      form.submit(); // Soumettre le formulaire si tout est valide
    }

  });
}

function highlightInput(inputElement) {
  inputElement.style.borderColor = 'red'; // Met l'input en rouge
}

console.log('script chargé');




