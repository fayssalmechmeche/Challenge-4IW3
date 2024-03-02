document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('design-guide-grid');
  // Nettoyer le conteneur
  container.innerHTML = '';

  const grid = new gridjs.Grid({
      search: true,
      className: {
          th: "bg-white dark:bg-dark-bg text-black dark:text-white dark:border-dark-bg hover:bg-gray-200 dark:hover:bg-dark-card active:bg-gray-300 dark:active:bg-dark-card focus:bg-gray-300 dark:focus:bg-dark-card",
          td: "text-black bg-white dark:text-white dark:bg-dark-card dark:border-dark-section",
          paginationSummary: "text-black dark:text-white",
          sort: "bg-yellow-400 ",
          filter: "dark:bg-dark-card dark:text-white",
          footer: "dark:bg-dark-card dark:text-white dark:border-dark-bg",
        },
      pagination: {
          limit: 12,
          enabled: true,
      },
      sort: true,
      columns: ['Client', 'Numéro de devis', 'Etat', 'Consultation'],
      data: [
          ['Jean', "2023-09-0034", 'Payé', 'Consulter'],
          ['Marie', "2023-09-0034", 'En cours', 'Consulter'],
          ['Pierre', "2023-09-0034", 'En cours', 'Consulter'],
          ['Julie', "2023-09-0034", 'En retard', 'Consulter'],
          ['Kcorp', "2023-08-0090","Payé", "Consulter"],
          ['Safran', "2023-09-0134", 'En cours', 'Consulter'],
          ['Société générale', "2023-11-1134", 'En cours', 'Consulter'],
          ['Galitt', "2023-11-0004", 'Payé', 'Consulter'],
          ['PotatoFun', "2023-19-0009", 'En retard', 'Consulter'],
          ['KetchupFun', "2023-12-0009", 'En retard', 'Consulter'],
          ['Aled', "2023-12-0039", 'Payé', 'Consulter'],
          ['Maurice', "2023-12-1031", 'Payé', 'Consulter'],
          ['Lucie', "2023-12-1287", 'Payé', 'Consulter'],
      ]
  });

  grid.render(container);

  const waitForGridToRender = () => {
      return new Promise((resolve) => {
        const checkExist = setInterval(() => {
          const wrapper = document.querySelector("#design-guide-grid .gridjs-wrapper");
          if (wrapper) {
            clearInterval(checkExist);
            resolve();
          }
        }, 100); // vérifier toutes les 100 millisecondes
      });
    };
  
    waitForGridToRender().then(() => {
      // Le tableau est maintenant rendu, appliquez vos modifications ici
      document.querySelector("#design-guide-grid .gridjs-wrapper").classList.add("dark:border-t-0");
      document
        .querySelector("#design-guide-grid .gridjs-search-input")
        .classList.add(
          "bg-white",
          "dark:border-dark-bg",
          "dark:bg-dark-bg",
          "text-black",
          "dark:text-white"
        );
    });
});
