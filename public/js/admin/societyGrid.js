document.addEventListener("DOMContentLoaded", function () {
  new gridjs.Grid({
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
          <div class="flex">
            <a href="/admin/society/show/${society.id}">👁‍🗨</a>
            <button onclick="openSocietyEditModal(${society.id})">📝</button>
            <a href="/admin/society/delete/${society.id}/${society.token}">❌</a>
          </div>`),
        ]),
    },
    search: true,
    pagination: {
      limit: 5,
    },
    sort: true,
    language: {
      search: {
        placeholder: 'Rechercher...'
      },
      noRecordsFound: 'Aucun résultat',
      loading: 'Chargement...',
      error: 'Une erreur est survenue',
      pagination: {
        previous: 'Précédent',
        next: 'Suivant',
        showing: 'Affichage',
        results: () => 'Résultats',
        of: 'de',
        to: 'à'
      }
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
