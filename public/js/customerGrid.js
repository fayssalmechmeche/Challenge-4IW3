document.addEventListener('DOMContentLoaded', function () {
    new gridjs.Grid({
        server: {
            url: '/customer/api',
            then: data => data.map(customer => [
                customer.name,
                gridjs.html(`
                    <a href='/customer/${customer.id}/edit' class='btn btn-primary btn-sm'>Modifier</a>
                    <form action='/customer/${customer.id}' method='POST' onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer ce client ?");'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='${csrfToken}'>
                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                    </form>
                `)
            ])
        },
        columns: [
            'Nom',
            'Actions'
        ],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('product-table'));
});
