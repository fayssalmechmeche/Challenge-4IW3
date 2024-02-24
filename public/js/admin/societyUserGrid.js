let gridUser = null
document.addEventListener("DOMContentLoaded", function () {
  let id = document.getElementById("tabUserGridJs").getAttribute("data-id");
  gridUser = new gridjs.Grid({
    columns: [
      {
        name: "Prénom",
        formatter: (cell) => gridjs.html(`<b>${cell}</b>`),
      },
      "Nom",
      "Rôles",
      "Status",
      "Actions",
    ],
    server: {
      url: "/admin/user/api/" + id,
      then: (data) =>
        data.map((user) => [
          user.name,
          user.lastName,
          user.roles.includes("ROLE_ACCOUNTANT")
            ? "Comptable"
            : user.roles.includes("ROLE_USER")
              ? "Utilisateur"
              : user.roles.includes("ROLE_SOCIETY")
                ? "Entreprise"
                : user.roles.join(", "),
          user.status ? "Validé" : "Invalidé",
          gridjs.html(`
          <div class="w-full mx-auto flex justify-center items-center gap-2">
          <button
          class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
          onclick="openUserShowModal(${user.id})"
          >
          Voir l'Utilisateur
          </button>
          <button
          class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
          onclick="openUserEditModal(${user.id})"
          >
          Modifier
          </button>
          <button
          type="submit"
           class="text-white font-medium bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
           onclick="deleteUser(${user.id},'${user.token}')"
            >
            Supprimer
          </button>
          </div>`),
        ]),
    },
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
      limit: 5,
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
    sort: true,
  }).render(document.getElementById("tabUserGridJs"));
});

function loadGridUser() {
  gridUser.updateConfig({
    search: true,
    pagination: {
      limit: 5,
    },
  }).forceRender();
}


const waitForGridToRender = () => {
  return new Promise((resolve) => {
    const checkExist = setInterval(() => {
      const wrapper = document.querySelector("#tabUserGridJs .gridjs-wrapper");
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
    .querySelector("#tabUserGridJs .gridjs-search-input")
    .classList.add(
      "bg-white",
      "dark:border-dark-bg",
      "dark:bg-dark-bg",
      "text-black",
      "dark:text-white"
    );
});