document.addEventListener("DOMContentLoaded", function () {
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
      url: "/admin/user/api",
      then: (data) =>
        data.map((user) => [
          user.name,
          user.lastName,
          user.roles,
          user.status ? "Validé" : "Invalidé",
          gridjs.html(`
          <div class="flex">
            <button onclick="openUserShowModal(${user.id})">👁‍🗨</button>
            <button onclick="openUserEditModal(${user.id})">📝</button>
            <button onclick="">❌</button>
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
