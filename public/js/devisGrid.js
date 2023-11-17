document.addEventListener('DOMContentLoaded', function () {
    new gridjs.Grid({
        server: {
            url: '/devis/api',
            then: data => data.map(devis => [
                devis.id,
                devis.taxe,
                devis.totalPrice,
                devis.totalDuePrice,
                devis.paymentStatus,
                devis.createdAt ? new Date(devis.createdAt).toLocaleString() : '',
                devis.updatedAt ? new Date(devis.updatedAt).toLocaleString() : '',
                gridjs.html(`<a href='/devis/${devis.id}/show'>show</a> <a href='/devis/${devis.id}/edit'>edit</a>`)
            ])
        },
        columns: [
            'ID',
            'Taxe',
            'Total Price',
            'Total Due Price',
            'Payment Status',
            'Created At',
            'Updated At',
            'Actions'
        ],
        search: true,
        pagination: true,
        sort: true,
        // autres options...
    }).render(document.getElementById('devis-table'));
});
