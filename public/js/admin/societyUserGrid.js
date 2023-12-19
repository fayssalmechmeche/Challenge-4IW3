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
            user.roles.includes('ROLE_USER') ? 'Utilisateur' : user.roles.join(', ')
          ),
          user.status ? "Validé" : "Invalidé",
          gridjs.html(`
          <div class="flex">
            <button onclick="openUserShowModal(${user.id})">👁‍🗨</button>
            <button onclick="openUserEditModal(${user.id})">📝</button>
            <a href="/admin/user/delete/${user.id}/${user.token}">❌</a>
          </div>`),
        ]),
    },
    search: true,
    pagination: {
      limit: 5,
    },
    sort: true,
  }).render(document.getElementById("tabUserGridJs"));
});
