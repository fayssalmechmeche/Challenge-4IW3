document.addEventListener('DOMContentLoaded', function () {
    new gridjs.Grid({
        server: {
            url: '/devis/api',
            then: data => data.map(devis => [
                devis.customer,
                devis.totalPrice.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }),
                devis.totalDuePrice.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }),
                devis.paymentStatus,
                devis.createdAt ? new Date(devis.createdAt).toLocaleDateString() : '',
                gridjs.html(`
                    <a href='/devis/${devis.id}/show' class='btn btn-primary btn-sm'>Voir</a>
                    <a href='/devis/${devis.id}/edit' class='btn btn-secondary btn-sm'>Modifier</a>
                    <form action='/devis/${devis.id}' method='POST' onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer ce devis ?");'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='${csrfToken}'>
                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                    </form>
                `)
            ])
        },
        columns: [
            'Client',
            'Prix Total',
            'Total Dû',
            'Statut de Paiement',
            'Créé Le',
            'Actions'
        ],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('devis-table'));
});
