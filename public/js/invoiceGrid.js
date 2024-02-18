document.addEventListener('DOMContentLoaded', function () {
    // Initialisation de Grid.js
    new gridjs.Grid({
        server: {
            url: '/invoice/api',
            then: data => data.map(invoice => [
                invoice.customer,
                invoice.devisNumber,
                invoice.createdAt ? new Date(invoice.createdAt).toLocaleDateString() : '',
                gridjs.html(`
    <button class="text-white font-medium bg-nav-btn hover:bg-blue-800 transition-all duration-300 ease-out rounded-lg mb-3 px-3 py-2 modalswap" data-devis-id="${invoice.id}" data-devis-number="${invoice.devisNumber}" data-deposit-status="${invoice.depositStatus}">Test</button>
`)
            ])
        },
        columns: ['Client', 'Numéro du devis', 'Créé Le', 'Actions'],
        search: true,
        pagination: true,
        sort: true,
    }).render(document.getElementById('devis-table'));

    document.getElementById('devis-table').addEventListener('click', function(event) {
        var target = event.target;
        while (target != null && !target.classList.contains('modalswap')) {
            target = target.parentNode;
        }

        if (target && target.classList.contains('modalswap')) {
            const devisId = target.getAttribute('data-devis-id');
            const devisNumber = target.getAttribute('data-devis-number');
            const depositStatus = target.getAttribute('data-deposit-status'); // Récupère le statut de l'acompte

            document.getElementById('selected-devis-id').value = devisId;
            document.getElementById('sectionToHide2').classList.add('hidden');
            document.getElementById('sectionToHide').classList.remove('hidden');

            updateDevisSelectionDisplay(devisNumber);

            // Mise à jour des URLs pour tous les liens "Créer la facture"
            document.querySelectorAll('.create-invoice-btn').forEach(link => {
                link.href = `/invoice/new?devisId=${devisId}`; // Assurez-vous que le chemin est correct
            });

            const depositInvoiceSection = document.getElementById('depositInvoiceSection');
            console.log(depositInvoiceSection);
            if (depositStatus === 'NON_EXISTANT' || depositStatus === 'GENERE') {
                // Applique un effet d'assombrissement et réduit l'opacité
                depositInvoiceSection.style.filter = 'brightness(50%)';
                depositInvoiceSection.style.opacity = '0.5';
                console.log("Effet d'assombrissement et opacité réduite appliqués");
                // Trouver le lien dans la section et le désactiver
                const link = depositInvoiceSection.querySelector('a');
                link.classList.add('cursor-not-allowed');
                link.addEventListener('click', function(event) {
                    event.preventDefault(); // Empêche la navigation
                });
            } else {
                // Réinitialise les styles pour la luminosité et l'opacité normales
                depositInvoiceSection.style.filter = '';
                depositInvoiceSection.style.opacity = '1';
                // Réactiver le lien
                const link = depositInvoiceSection.querySelector('a');
                link.classList.remove('cursor-not-allowed');
            }
        }
    });
});



    function updateDevisSelectionDisplay(devisNumber) {
    document.getElementById('devis-selection-display').textContent = `Devis sélectionné : Devis numéro ${devisNumber} du client X`;
}
console.log("sheeesdfsdfsdfh");
