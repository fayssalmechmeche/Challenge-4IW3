document.addEventListener("DOMContentLoaded", function () {
  new gridjs.Grid({
    server: {
      url: "/product/api",
      then: (data) =>
        data.map((product) => [
          product.name,
          (product.price / 100).toLocaleString("fr-FR", {
            style: "currency",
            currency: "EUR",
          }),
          product.productCategory,
          gridjs.html(`
                <div class="w-auto mx-auto flex justify-center items-center gap-1 flex-wrap">
          <button
            onclick="openProductEditModal(${product.id})"
            class="text-white mx-2 font-medium bg-orange-400 hover:bg-orange-600 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
          >
            Modifier
          </button>
          <button
            class="text-white mx-2 font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
            onclick="openProductModal(${product.id})"
          >
            Consulter
          </button>
          <form
            action="/product/${product.id}"
            method="POST"
            onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer ce client ?");'
          >
            <input type="hidden" name="_method" value="DELETE" />
            <input type="hidden" name="_token" value="${csrfToken}" />
            <button
              type="submit"
              class="text-white font-medium mx-2 bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
            >
              Supprimer
            </button>
          </form>
        </div>



                    <a href='/product/${product.id}/edit' class='btn btn-primary btn-sm'>Modifier</a>
                    <form action='/product/${product.id}' method='POST' onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer ce produit ?");'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='${csrfToken}'>
                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                    </form>
                `),
        ]),
    },
    columns: ["Nom", "Prix", "Catégorie", "Actions"],
    search: true,
    pagination: true,
    sort: true,
  }).render(document.getElementById("product-table"));
});
