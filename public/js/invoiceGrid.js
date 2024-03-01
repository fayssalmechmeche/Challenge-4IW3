document.addEventListener("DOMContentLoaded", function () {
  // Initialisation de Grid.js
  new gridjs.Grid({
    server: {
      url: "/invoice/api",
      then: (data) =>
        data.map((invoice) => [
          invoice.customer,
          invoice.devisNumber,
          invoice.createdAt
            ? new Date(invoice.createdAt).toLocaleDateString()
            : "",
          gridjs.html(`
    <button class="text-white font-medium bg-nav-btn hover:bg-blue-800 transition-all duration-300 ease-out rounded-lg mb-3 px-3 py-2 modalswap" data-devis-id="${invoice.id}" data-devis-number="${invoice.devisNumber}" data-deposit-status="${invoice.depositStatus}">Test</button>
`),
        ]),
    },
    columns: ["Client", "Numéro du devis", "Créé Le", "Actions"],
    search: true,
    pagination: true,
    sort: true,
  }).render(document.getElementById("devis-table"));

  document
    .getElementById("devis-table")
    .addEventListener("click", function (event) {
        resetInvoiceSections();
      const depositInvoiceSection = document.getElementById(
        "depositInvoiceSection"
      );
      const allInvoiceSection = document.getElementById("allInvoiceSection");

      var target = event.target;
      while (target != null && !target.classList.contains("modalswap")) {
        target = target.parentNode;
      }

      if (target && target.classList.contains("modalswap")) {
        const devisId = target.getAttribute("data-devis-id");
        const devisNumber = target.getAttribute("data-devis-number");
        let stringDevisNumber = String(devisNumber);
        const depositStatus = target.getAttribute("data-deposit-status"); // Récupère le statut de l'acompte

        fetch("api/invoices")
          .then((response) => response.json())
          .then((data) => {
            // Traitement des données, par exemple en filtrant les factures qui correspondent au devisNumber
            const matchingInvoices = data.filter(
              (invoice) => invoice.devisNumber === stringDevisNumber
            );
            console.log("matchings", matchingInvoices);
            console.log("data", data);
            console.log("devisNumber", devisNumber);
            if (matchingInvoices.length > 0) {
              // Itérer sur les factures correspondantes pour vérifier leur type
              matchingInvoices.forEach((invoice) => {
                if (invoice.invoiceType === "STANDARD") {
                  allInvoiceSection.style.filter = "brightness(50%)";
                  allInvoiceSection.style.opacity = "0.5";
                  let links = allInvoiceSection.querySelectorAll("a");
                  links.forEach((link) => {
                    link.removeAttribute("href");
                  });
                } else if (invoice.invoiceType === "DEPOSIT") {
                  depositInvoiceSection.style.filter = "brightness(50%)";
                  depositInvoiceSection.style.opacity = "0.5";
                  let links = depositInvoiceSection.querySelectorAll("a");
                  links.forEach((link) => {
                    link.removeAttribute("href");
                  });
                } else {
                  // Si le type de facture est autre que STANDARD ou DEPOSIT
                  console.log(
                    `La facture ${invoice.invoiceNumber} a un type inattendu: ${invoice.invoiceType}.`
                  );
                }
              });
            } else {
              // Logique si aucune facture correspondante n'est trouvée
              console.log("Aucune facture correspondante trouvée");
            }
          })
          .catch((error) =>
            console.error("Erreur lors de la récupération des factures:", error)
          );

        document.getElementById("selected-devis-id").value = devisId;
        document.getElementById("sectionToHide2").classList.add("hidden");
        document.getElementById("sectionToHide").classList.remove("hidden");

        updateDevisSelectionDisplay(devisNumber);

        // Mise à jour des URLs pour tous les liens "Créer la facture"
        document
          .querySelectorAll(".create-invoice-btn")
          .forEach((link, index) => {
            if (index === 0) {
              link.href = `/invoice/new?devisId=${devisId}&deposit=true`;
            } else if (index === 1) {
              link.href = `/invoice/new?devisId=${devisId}&deposit=false`;
            }
          });

        console.log(depositInvoiceSection);
        if (depositStatus === "NON_EXISTANT" || depositStatus === "GENERE") {
          // Applique un effet d'assombrissement et réduit l'opacité
          depositInvoiceSection.style.filter = "brightness(50%)";
          depositInvoiceSection.style.opacity = "0.5";
          console.log("Effet d'assombrissement et opacité réduite appliqués");
          // Trouver le lien dans la section et le désactiver
          const link = depositInvoiceSection.querySelector("a");
          link.classList.add("cursor-not-allowed");
          link.addEventListener("click", function (event) {
            event.preventDefault(); // Empêche la navigation
          });
        } else {
          // Réinitialise les styles pour la luminosité et l'opacité normales
          depositInvoiceSection.style.filter = "";
          depositInvoiceSection.style.opacity = "1";
          // Réactiver le lien
          const link = depositInvoiceSection.querySelector("a");
          link.classList.remove("cursor-not-allowed");
        }
      }
    });
});

function updateDevisSelectionDisplay(devisNumber) {
  document.getElementById(
    "devis-selection-display"
  ).textContent = `Devis sélectionné : Devis numéro ${devisNumber} du client X`;
}


function resetInvoiceSections() {
    // Réinitialise les styles pour allInvoiceSection et depositInvoiceSection
    [allInvoiceSection, depositInvoiceSection].forEach(section => {
        section.style.filter = '';
        section.style.opacity = '1';
        // Supprime le gestionnaire d'événements sur les liens pour réactiver la navigation
        const links = section.querySelectorAll('a');
        links.forEach(link => {
            link.classList.remove('cursor-not-allowed');
        });
    });
}

fetch("api/invoices")
  .then((response) => response.json())
  .then((data) => console.log(data))
  .catch((error) => console.error("Error:", error));
console.log("wad");

function preventNavigation(event) {
  event.preventDefault();
}

console.log("seaaatttt");
