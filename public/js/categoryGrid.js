document.addEventListener('DOMContentLoaded', function () {
    new gridjs.Grid({
        server: {
            url: '/category/api',
            then: data => data.map(category => [
                category.name,
                gridjs.html(`
                    <a href='/category/${category.id}/edit' class='btn btn-primary btn-sm'>Modifier</a>
                    <form action='/category/${category.id}' method='POST' onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer ce produit ?");'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='${csrfToken}'>
                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                    </form>
                `),
            ])
        },
        columns: [
            'Nom',
            'Actions'
        ],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('category-table'));
});
