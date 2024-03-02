document.addEventListener("DOMContentLoaded", function () {
  new gridjs.Grid({
    server: {
      url: "/api/product",
      then: (data) =>
        data.map((product) => [
          product.name,
          (product.price / 100).toLocaleString("fr-FR", {
            style: "currency",
            currency: "EUR",
          }),
          product.category,
        ]),
    },
    columns: ["Nom", "Prix", "Catégorie"],
    search: true,
    pagination: true,
    sort: true,
    className: {
      th: "bg-white dark:bg-dark-bg text-black dark:text-white dark:border-dark-bg hover:bg-gray-200 dark:hover:bg-dark-card active:bg-gray-300 dark:active:bg-dark-card focus:bg-gray-300 dark:focus:bg-dark-card",
      td: "text-black bg-white dark:text-white dark:bg-dark-card dark:border-dark-section",
      paginationSummary: "text-black dark:text-white",
      sort: "bg-yellow-400 ",
      filter: "dark:bg-dark-card dark:text-white",
      footer: "dark:bg-dark-card dark:text-white dark:border-dark-bg",
    },
    style: {
      td: {
        "max-width": "1000px",
      },
      table: {
        "text-align": "center",
      },
    },
    language: {
      search: {
        placeholder: "Rechercher...",
        placeholder: "Rechercher...",
      },
      noRecordsFound: "Aucun résultat",
      loading: "Chargement...",
      error: "Une erreur est survenue",
      noRecordsFound: "Aucun résultat",
      loading: "Chargement...",
      error: "Une erreur est survenue",
      pagination: {
        previous: "Précédent",
        next: "Suivant",
        showing: "Affichage",
        results: () => "Résultats",
        of: "de",
        to: "à",
      },
    },
  }).render(document.getElementById("product-table"));

  const waitForGridToRender = () => {
    return new Promise((resolve) => {
      const checkExist = setInterval(() => {
        const wrapper = document.querySelector(".gridjs-wrapper");
        if (wrapper) {
          clearInterval(checkExist);
          resolve();
        }
      }, 100); // vérifier toutes les 100 millisecondes
    });
  };

  waitForGridToRender().then(() => {
    // Le tableau est maintenant rendu, appliquez vos modifications ici
    document.querySelector(".gridjs-wrapper").classList.add("dark:border-t-0");
    document
      .querySelector(".gridjs-search-input")
      .classList.add(
        "bg-white",
        "dark:border-dark-bg",
        "dark:bg-dark-bg",
        "text-black",
        "dark:text-white"
      );
  });
});
