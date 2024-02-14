document.addEventListener('DOMContentLoaded', function () {
    // Récupère l'ID du devis depuis l'attribut de données
    const devisElement = document.getElementById('wrapper-invoice');
    const devisId = devisElement.getAttribute('data-devis-id');

    new gridjs.Grid({
        server: {
            url: `/invoice/devisDetail?devisId=${devisId}`,
            then: data => [...data.devisProducts, ...data.devisFormulas].map(item => [
                item.name,
                item.quantity,
                `${(item.price / 100) * item.quantity } €`,
            ])
        },
        columns: [
            {name: 'Nom', width: '40%'},
            {name: 'Quantité', width: '30%'},
            {name: 'Prix', width: '30%'},
        ],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('wrapper-invoice'));
});
