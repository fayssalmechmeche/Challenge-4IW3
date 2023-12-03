document.addEventListener("DOMContentLoaded", function () {
  new gridjs.Grid({
    server: {
      url: "/customer/api",
      then: (data) =>
        data.map((customer) => [
          customer.nameSociety || customer.name + " " + customer.lastName,
          customer.nameSociety ? "Société" : "Client Particulier",
          gridjs.html(`
          <div class="w-auto mx-auto flex justify-center items-center flex-wrap">
          <button
            onclick="openCustomerEditModal(${customer.id})"
            class="text-white font-medium bg-orange-400 hover:bg-orange-600 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
          >
            Modifier
          </button>
          <button
            class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
            onclick="openCustomerModal(${customer.id})"
          >
            Consulter
          </button>
          <form
            action="/customer/${customer.id}"
            method="POST"
            onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer ce client ?");'
          >
            <input type="hidden" name="_method" value="DELETE" />
            <input type="hidden" name="_token" value="${csrfToken}" />
            <button
              type="submit"
              class="text-white font-medium bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
            >
              Supprimer
            </button>
          </form>
        </div>
`),
        ]),
    },
    columns: ["Nom", "Type", { name: "Actions", width: "300px" }],
    search: true,
    pagination: true,
    sort: true,
    style: {
      td: {
        "max-width": "1000px",
      },
      table: {
        "font-size": "18px",
        "text-align": "center",
        "font-weight": "bold",
      },
    },
  }).render(document.getElementById("customer-table"));
});
