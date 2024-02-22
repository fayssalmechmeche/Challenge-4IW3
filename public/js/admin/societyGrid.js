let gridSociety = null;
document.addEventListener("DOMContentLoaded", function () {
  gridSociety = new gridjs.Grid({
    columns: [
      {
        name: "Nom de société",
        formatter: (cell) => gridjs.html(`<b>${cell}</b>`),
      },
      ,
      "Adresse",
      "Téléphone",
      "Email",
      "Actions",
    ],
    server: {
      url: "/admin/society/api",
      then: (data) =>
        data.map((society) => [
          society.name,
          society.address,
          society.phone,
          society.email,
          gridjs.html(`
          <div class="w-full mx-auto flex justify-center items-center gap-2">
      <button
        class="text-white w-40 font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-1 py-2"
      >
        <a class="text-decoration-none" href="/admin/society/show/${society.id}"
          >Voir la Société</a
        >
      </button>
      <button
        class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
        onclick="openSocietyEditModal(${society.id})"
      >
        Modifier
      </button>
      <button
        type="submit"
        class="text-white font-medium bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
        onclick="deleteSociety(${society.id},'${society.token}')"
        )
      >
        Supprimer
      </button>
    </div>`),
        ]),
    },
    search: true,
    pagination: {
      limit: 5,
    },
    sort: true,
    className: {
      th: "bg-white dark:bg-dark-bg text-black dark:text-white dark:border-dark-bg hover:bg-gray-200 dark:hover:bg-dark-card active:bg-gray-300 dark:active:bg-dark-card focus:bg-gray-300 dark:focus:bg-dark-card",
      td: "text-black bg-white dark:text-white dark:bg-dark-card dark:border-dark-section",
      paginationSummary: "text-black dark:text-white",
      sort: "bg-yellow-400 ",
      filter: "dark:bg-dark-card dark:text-white",
      footer: "dark:bg-dark-card dark:text-white dark:border-dark-bg",
    },
    language: {
      search: {
        placeholder: "Rechercher...",
      },
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
    // style: {
    //   td: {
    //     "max-width": "1000px",
    //   },
    //   table: {
    //     "font-size": "18px",
    //     "text-align": "center",
    //     "font-weight": "bold",
    //   },
    // },
  }).render(document.getElementById("tabSocietyGridJs"));
});

function loadGridSociety() {
  gridSociety
    .updateConfig({
      // search: true,
    })
    .forceRender();
}
const waitForGridToRender = () => {
  return new Promise((resolve) => {
    const checkExist = setInterval(() => {
      const wrapper = document.querySelector("#tabSocietyGridJs .gridjs-wrapper");
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
    .querySelector("#tabSocietyGridJs .gridjs-search-input")
    .classList.add(
      "bg-white",
      "dark:border-dark-bg",
      "dark:bg-dark-bg",
      "text-black",
      "dark:text-white"
    );
});
