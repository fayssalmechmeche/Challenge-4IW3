document.addEventListener('DOMContentLoaded', function () {
    new gridjs.Grid({
        server: {
            url: '/customer/api',
            then: data => data.map(customer => [
                customer.nameSociety || (customer.name + ' ' + customer.lastName),
                customer.nameSociety ? 'Société' : 'Client Particulier',
                gridjs.html(`
   <button onclick='openCustomerEditModal(${customer.id})' class='btn btn-primary btn-sm'>Modifier</button>
    <button class='btn btn-info btn-sm' onclick='openCustomerModal(${customer.id})'>Consulter</button>
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
            'Type',
            'Actions'
        ],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('product-table'));
});




