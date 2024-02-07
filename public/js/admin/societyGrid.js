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
        class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
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
        onclick="deleteSociety(${society.id},${society.token}"
        )
      >
        Supprimer
      </button>
    </div>`),
        ]),
    },
    // search: true,
    pagination: {
      limit: 5,
    },
    style: {
      table: {
        border: "none",
      },
      th: {
        "background-color": "#d4d4d4",
        color: "#000",
        "text-align": "center",
      },
      td: {
        "text-align": "center",
        
      },
    },
    sort: true,
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
      pagination: {
        limit: 5,
      },
    })
    .forceRender();
}
