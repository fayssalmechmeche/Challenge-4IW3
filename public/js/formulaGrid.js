document.addEventListener("DOMContentLoaded", function () {
  new gridjs.Grid({
      server: {
          url: "/formula/api",
          then: (data) =>
              data.map((formula) => [
                  formula.name,
                  (formula.price / 100).toLocaleString("fr-FR", {
                      style: "currency",
                      currency: "EUR",
                  }),
                  gridjs.html(`
                      <div class="w-full mx-auto flex justify-center items-center flex-wrap gap-2">
                          <button
                              onclick="openFormulaEditModal(${formula.id})"
                              class="text-white font-medium bg-orange-400 hover:bg-orange-600 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
                          >
                              Modifier
                          </button>
                          <button
                              class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
                              onclick="openFormulaModal(${formula.id})"
                          >
                              Consulter
                          </button>
                          <form
                              action="/formula/${formula.id}"
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
      columns: ["Nom", "Prix", "Actions"],
      search: true,
      pagination: true,
      sort: true,
  }).render(document.getElementById("formula-table"));
});
