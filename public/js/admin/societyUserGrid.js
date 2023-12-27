document.addEventListener("DOMContentLoaded", function () {

  let id = document.getElementById('tabUserGridJs').getAttribute('data-id');
  console.log(id)
  new gridjs.Grid({
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
          user.roles.includes('ROLE_ACCOUNTANT') ? 'Comptable' : (
            user.roles.includes('ROLE_USER') ? 'Utilisateur' : (
              user.roles.includes('ROLE_SOCIETY') ? 'Entreprise' : user.roles.join(', ')
            )
          ),
          user.status ? "Validé" : "Invalidé",
          gridjs.html(`
          <div class="flex">
            <button class="pr-2" onclick="openUserShowModal(${user.id})">👁‍🗨</button>
            <button class="pr-2" onclick="openUserEditModal(${user.id})">📝</button>
            <a href="/admin/user/delete/${user.id}/${user.token}">❌</a>
          </div>`),
        ]),
    },
    search: true,
    pagination: {
      limit: 5,
    },
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
    sort: true,
  }).render(document.getElementById("tabUserGridJs"));
});
