document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('dashboard-grid');
    // Nettoyer le conteneur
    container.innerHTML = '';

    const grid = new gridjs.Grid({
        search: true,
        pagination: {
            limit: 12,
            enabled: true,
        },
        sort: true,
        columns: ['Client', 'Numéro de devis', 'Etat', 'Consultation'],
        data: [
            ['Jean', "2023-09-0034", 'Payé', 'Consulter'],
            ['Marie', "2023-09-0034", 'En cours', 'Consulter'],
            ['Pierre', "2023-09-0034", 'En cours', 'Consulter'],
            ['Julie', "2023-09-0034", 'En retard', 'Consulter'],
            ['Kcorp', "2023-08-0090","Payé", "Consulter"],
            ['Safran', "2023-09-0134", 'En cours', 'Consulter'],
            ['Société générale', "2023-11-1134", 'En cours', 'Consulter'],
            ['Galitt', "2023-11-0004", 'Payé', 'Consulter'],
            ['PotatoFun', "2023-19-0009", 'En retard', 'Consulter'],
            ['KetchupFun', "2023-12-0009", 'En retard', 'Consulter'],
            ['Aled', "2023-12-0039", 'Payé', 'Consulter'],
            ['Maurice', "2023-12-1031", 'Payé', 'Consulter'],
            ['Lucie', "2023-12-1287", 'Payé', 'Consulter'],
        ]
    });

    grid.render(container);
});
