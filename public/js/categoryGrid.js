document.addEventListener("DOMContentLoaded", function () {
  new gridjs.Grid({
    server: {
      url: "/category/api",
      then: (data) =>
        data.map((category) => [
          category.name,
          gridjs.html(`
          <div class="w-full mx-auto flex justify-center items-center flex-wrap gap-2">
          <button
          onclick="openCategoryEditModal(${category.id})"
          class="text-white font-medium bg-orange-400 hover:bg-orange-600 transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
      >
          Modifier
      </button>
      <button
      class="text-white font-medium bg-button-blue hover:bg-button-blue-hover transition-all duration-300 ease-out rounded-lg m-1 px-3 py-2"
      onclick="openCategoryModal(${category.id})"
  >
      Consulter
  </button>
  <form
      action="/category/${category.id}"
      method="POST"
      onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer cette catégorie ?");'
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
    columns: ["Nom", "Actions"],
    search: true,
    className: {
      th: "bg-white dark:bg-dark-bg text-black dark:text-white dark:border-dark-bg hover:bg-gray-200 dark:hover:bg-dark-card active:bg-gray-300 dark:active:bg-dark-card focus:bg-gray-300 dark:focus:bg-dark-card",
      td: "text-black bg-white dark:text-white dark:bg-dark-card dark:border-dark-section",
      paginationSummary: "text-black dark:text-white",
      sort: "bg-yellow-400 ",
      filter: "dark:bg-dark-card dark:text-white",
      footer: "dark:bg-dark-card dark:text-white dark:border-dark-bg",
    },
    pagination: true,
    sort: true,
  }).render(document.getElementById("category-table"));

  const waitForGridToRender = () => {
    return new Promise((resolve) => {
      const checkExist = setInterval(() => {
        const wrapper = document.querySelector(".gridjs-wrapper");
        if (wrapper) {
          clearInterval(checkExist);
          resolve();
        }
      }, 100); // vérifier toutes les 100 millisecondes
    });
  };

  waitForGridToRender().then(() => {
    document.querySelector(".gridjs-wrapper").classList.add("dark:border-t-0");
    document
      .querySelector(".gridjs-search-input")
      .classList.add(
        "bg-white",
        "dark:border-dark-bg",
        "dark:bg-dark-bg",
        "text-black",
        "dark:text-white"
      );
  });
});