document.addEventListener("DOMContentLoaded", function () {
  new gridjs.Grid({
    columns: [
      {
        name: "Raison Sociale",
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
            <button onclick="openSocietyShowModal(${society.id})">👁‍🗨</button>
            <button onclick="openSocietyEditModal(${society.id})">📝</button>
            <button href="/admin/society/delete/${society.id}/${society.token}">❌</button>
          </div>`),
        ]),
    },
    search: true,
    pagination: {
      limit: 5,
    },
    sort: true,
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
