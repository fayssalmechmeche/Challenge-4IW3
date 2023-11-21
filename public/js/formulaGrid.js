document.addEventListener('DOMContentLoaded', function () {
    new gridjs.Grid({
        server: {
            url: '/formula/api',
            then: data => data.map(formula => [
                formula.name,
                gridjs.html(formula.formula ? `<img src='/images/formulas/${formula.image}' alt='Image' style='height: 50px;' />` : 'Aucune image'),
                formula.price.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }),
                gridjs.html(`
                    <a href='/formula/${formula.id}/edit' class='btn btn-primary btn-sm'>Modifier</a>
                    <form action='/formula/${formula.id}' method='POST' onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer cette formule ?");'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='${csrfToken}'>
                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                    </form>
                `)
            ])
        },
        columns: [
            'Nom',
            'Photo',
            'Prix',
            'Actions'
        ],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('formula-table'));
});
