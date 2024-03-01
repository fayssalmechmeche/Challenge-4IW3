document.addEventListener("DOMContentLoaded", function () {
  const invoices = document.querySelectorAll("#invoiceData input");
  const data = [];

  for (let i = 0; i < invoices.length; i += 6) {
    data.push([
      invoices[i].value, // idDevis
      invoices[i + 1].value, // InvoiceNumber
      invoices[i + 2].value + " €", // TotalDuePrice
      invoices[i + 3].value, // PaymentStatus
      invoices[i + 4].value, // CreatedAt
      invoices[i + 5].value, // PaymentDueTime
      gridjs.html(`<div class="w-full mx-auto flex justify-center items-center flex-wrap gap-2">
      <a class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2" href='/invoice/${invoices[i].value}'>Consulter</a>
       <form method="post" action="{{ path('app_invoice_delete', {'id': invoice.id}) }}" onsubmit="return confirm('Are you sure you want to delete this item?');">
      <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ invoice.id) }}">
      <button class="text-white font-medium bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
      >Supprimer</button>
      </div>
  </form>`),
    ]);
  }

  const container = document.getElementById("invoice-table");
  container.innerHTML = "";

  const grid = new gridjs.Grid({
    search: true,
    pagination: {
      limit: 12,
      enabled: true,
    },
    sort: true,
    columns: [
      "idDevis",
      "InvoiceNumber",
      "TotalDue Price",
      "PaymentStatus",
      "CreatedAt",
      "PaymentDueTime",
      "Actions",
    ],
    data: data,
    className: {
      th: "bg-white dark:bg-dark-bg text-black dark:text-white dark:border-dark-bg hover:bg-gray-200 dark:hover:bg-dark-card active:bg-gray-300 dark:active:bg-dark-card focus:bg-gray-300 dark:focus:bg-dark-card",
      td: "text-black bg-white dark:text-white dark:bg-dark-card dark:border-dark-section",
      paginationSummary: "text-black dark:text-white",
      sort: "bg-yellow-400",
      filter: "dark:bg-dark-card dark:text-white",
      footer: "dark:bg-dark-card dark:text-white dark:border-dark-bg",
    },
  });

  grid.render(container);

  const waitForGridToRender = () => {
    return new Promise((resolve) => {
      const checkExist = setInterval(() => {
        const wrapper = document.querySelector(
          "#invoice-table .gridjs-wrapper"
        );
        if (wrapper) {
          clearInterval(checkExist);
          resolve();
        }
      }, 100); // vérifier toutes les 100 millisecondes
    });
  };

  waitForGridToRender().then(() => {
    // Le tableau est maintenant rendu, appliquez vos modifications ici
    document
      .querySelector("#invoice-table .gridjs-wrapper")
      .classList.add("dark:border-t-0");
    document
      .querySelector("#invoice-table .gridjs-search-input")
      .classList.add(
        "bg-white",
        "dark:border-dark-bg",
        "dark:bg-dark-bg",
        "text-black",
        "dark:text-white"
      );
  });
});

function toggleModal(modalId, modalContentId) {
  addClassToElement();
  let modal = document.getElementById(modalId);
  let modalContent = document.getElementById(modalContentId);
  let sectionToHide = document.getElementById("sectionToHide");
  let sectionToHide2 = document.getElementById("sectionToHide2");

  if (modal.classList.contains("opacity-0")) {
    // Ouvrir le modal
    modal.classList.remove("hidden");
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
    }, 10);

    modalContent.classList.remove("hidden");
    setTimeout(() => {
      modalContent.classList.remove("top-up");
      modalContent.classList.add("top-1/2");
    }, 10);
  } else {
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

    setTimeout(() => {
      modal.classList.add("hidden");

      // Réinitialiser les sections ici
      if (sectionToHide) {
        sectionToHide.classList.add("hidden");
      }
      if (sectionToHide2) {
        sectionToHide2.classList.remove("hidden");
      }
    }, 500);

    modalContent.classList.remove("top-1/2");
    modalContent.classList.add("top-up");

    setTimeout(() => {
      modalContent.classList.add("hidden");
    }, 500);
  }
}

function openInvoiceCreateModal() {
  const modalBody = document.querySelector("#invoiceCreateModalContent");
  toggleModal("invoiceCreateModal", "invoiceCreateModalContent");
}

function addClassToElement() {
  //Ce script est propre à ce formulaire : il rajoute une class à la div id product qui ne peut être gérée dynamiquement depuis le form builder. #}
  let formulaDiv = document.getElementById("invoice");
  if (formulaDiv) {
    formulaDiv.classList.add("flex", "flex-wrap", "gap-y-5", "gap-x-10");
  }
}

function modalSwap() {
  var modalSwapButton = document.querySelector(".modalswap");
  var sectionToHide = document.getElementById("sectionToHide");
  var sectionToHideAgain = document.getElementById("sectionToHide2");
  if (modalSwapButton && sectionToHide) {
    modalSwapButton.addEventListener("click", function () {
      sectionToHide.classList.remove("hidden");
      sectionToHideAgain.classList.add("hidden");
    });
  }
}

function setupReturnButton() {
  var returnButtons = document.querySelectorAll(".btn-retour");
  returnButtons.forEach(function (returnButton) {
    returnButton.addEventListener("click", function () {
      var sectionToHide = document.getElementById("sectionToHide");
      var sectionToHide2 = document.getElementById("sectionToHide2");
      if (sectionToHide) sectionToHide.classList.add("hidden");
      if (sectionToHide2) sectionToHide2.classList.remove("hidden");
    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  modalSwap();
  setupReturnButton();
});

document.querySelector(".valid-invoice").addEventListener("click", function () {
  const invoiceDataElement = document.querySelector("#invoiceData");
  const devisId = invoiceDataElement.getAttribute("data-devis-id");
  var inputDate = document.getElementById("dateValidite");

  // Écouter l'événement 'change' pour détecter quand l'utilisateur sélectionne une date
  inputDate.addEventListener("change", function () {
    // Récupérer la valeur de l'input
    var selectedDate = inputDate.value;

    // Afficher la valeur dans la console ou l'utiliser selon vos besoins
    console.log(selectedDate);
  });
  var dateBeforeFetch = inputDate.value;
  console.log("dateBeforeFetch");

  // Préparation de l'objet JSON à envoyer
  const invoiceData = {
    dateValidite: dateBeforeFetch,
  };

  fetch("invoice/new", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify(invoiceData), // Conversion de l'objet JavaScript en chaîne JSON
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Success:", data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
});
