document.addEventListener("DOMContentLoaded", function () {
  new gridjs.Grid({
    server: {
      url: "/devis/api",
      then: (data) =>
        data.map((devis) => [
          devis.customer,
          devis.totalPrice.toLocaleString("fr-FR", {
            style: "currency",
            currency: "EUR",
          }),
          devis.totalDuePrice.toLocaleString("fr-FR", {
            style: "currency",
            currency: "EUR",
          }),
          devis.paymentStatus,
          devis.createdAt ? new Date(devis.createdAt).toLocaleDateString() : "",
          gridjs.html(`
                <div class="w-full mx-auto flex justify-center items-center gap-2">
                    <button
                    class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
                >
                    <a href='/devis/${devis.id}/edit' class="text-decoration-none">Modifier</a>
                </button>
                <button
                class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
                >
                    <a href='/devis/${devis.id}/show' class='text-decoration-none'>Voir le Devis</a>
                </button>
                <form
                action="/devis/${devis.id}"
                method="POST"
                onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer cette formule ?");'
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
    columns: [
      {
        name: 'Client',
        
      },
      "Prix Total",
      "Total Dû",
      "Statut",
      "Créé Le",
      {
        name: 'Actions',
        width: 'fit-content', 
      },
    ],
    search: true,
    pagination: true,
    sort: true,
  }).render(document.getElementById("devis-table"));
});
