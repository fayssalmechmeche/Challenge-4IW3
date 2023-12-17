document.addEventListener('DOMContentLoaded', function () {
    new gridjs.Grid({
        server: {
            url: '/formula/api',
            then: data => data.map(formula => [
                formula.name,
                (formula.price / 100).toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }),
                gridjs.html(`
                    <button onclick='openFormulaEditModal(${formula.id})' class='btn btn-primary btn-sm'>Modifier</button>
                    <button class='btn btn-info btn-sm' onclick='openFormulaModal(${formula.id})'>Consulter</button>
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
            'Prix',
            'Actions'
        ],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('formula-table'));
});
