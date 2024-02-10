let gridUser = null
document.addEventListener("DOMContentLoaded", function () {
    gridUser = new gridjs.Grid({
        columns: [
            "Prénom",
            "Nom",
            "Rôles",
            "Status",
            "Actions",
        ],
        server: {
            url: "/user/api",
            then: (data) =>
                data.map((user) => {
                    return [
                        user.name,
                        user.lastName,
                        user.roles.includes('ROLE_ACCOUNTANT') ? 'Comptable' : user.roles.includes('ROLE_SOCIETY') ? 'Entreprise' : 'Utilisateur',
                        user.status ? "Validé" : "Invalidé",
                        gridjs.html(`
            <div class="flex">
              <button class="pr-3" onclick="openUserShowModal(${user.id})">👁‍🗨</button>
              <button class="pr-3" onclick="openUserEditModal(${user.id})">📝</button>
              <button onclick="deleteUser(${user.id},'${user.token}')" >❌</button>
            </div>`),
                    ];
                }),
        },
        // href="/admin/user/delete/${user.id}/${user.token}"

        // search: true,
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

function loadGridUser() {
    gridUser.updateConfig({
        // search: true,
        pagination: {
            limit: 5,
        },
    }).forceRender();
}