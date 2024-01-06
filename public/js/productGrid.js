document.addEventListener('DOMContentLoaded', function () {
    new gridjs.Grid({
        server: {
            url: '/product/api',
            then: data => data.map(product => [
                product.name,
                (product.price / 100).toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }),
                product.category,
                gridjs.html(`
                    <a href='/product/${product.id}/edit' class='btn btn-primary btn-sm'>Modifier</a>
                    <form action='/product/${product.id}' method='POST' onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer ce produit ?");'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='${csrfToken}'>
                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                    </form>
                `)
            ])
        },
        columns: [
            'Nom',
            'Prix',
            'Catégorie',
            'Actions'
        ],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('product-table'));
});
