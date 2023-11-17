document.addEventListener('DOMContentLoaded', function () {
    new gridjs.Grid({
        server: {
            url: '/product/api',
            then: data => data.map(product => [
                product.name,
                product.price.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }),
                gridjs.html(product.image ? `<img src='/images/products/${product.image}' alt='Image' style='height: 50px;' />` : 'No Image'),
                product.productCategory,
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
            'Photo',
            'Catégorie',
            'Actions'
        ],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('product-table'));
});
