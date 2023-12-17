function initializeFormFields() {
    const switchClientType = document.getElementById('switchClientType');
    const customerName = document.getElementById('customer_name');
    const customerLastName = document.getElementById('customer_lastName');
    const customerNameSociety = document.getElementById('customer_nameSociety');
    const formContainer = document.getElementById('customerFormContainer');

    if (!switchClientType || !customerName || !customerLastName || !customerNameSociety || !formContainer) return;

    const isSociety = formContainer.getAttribute('data-is-society') === 'true';
    const customerNameLabel = customerName.previousElementSibling;
    const customerLastNameLabel = customerLastName.previousElementSibling;
    const customerNameSocietyLabel = customerNameSociety.previousElementSibling;

    function updateFieldsVisibility() {
        if (switchClientType.checked) {
            customerNameSociety.style.display = '';
            customerNameSocietyLabel.style.display = '';
            customerName.style.display = 'none';
            customerNameLabel.style.display = 'none';
            customerLastName.style.display = 'none';
            customerLastNameLabel.style.display = 'none';
            customerName.value = '';
            customerLastName.value = '';
        } else {
            customerNameSociety.style.display = 'none';
            customerNameSocietyLabel.style.display = 'none';
            customerName.style.display = '';
            customerNameLabel.style.display = '';
            customerLastName.style.display = '';
            customerLastNameLabel.style.display = '';
            customerNameSociety.value = '';
        }
    }

    switchClientType.checked = isSociety;
    updateFieldsVisibility();
    switchClientType.addEventListener('change', updateFieldsVisibility);
}

document.addEventListener('DOMContentLoaded', initializeFormFields);

function openCustomerModal(customerId) {
    fetch(`/customer/api/${customerId}`)
        .then(response => response.json())
        .then(data => {
            const modalBody = document.querySelector('#customerDetailsModal .modal-body');
            let customerDetails;

            if (data.nameSociety) {
                customerDetails = `<p>Société: ${data.nameSociety}</p>`;
            } else {
                customerDetails = `<p>Nom: ${data.name}</p> <p>Prénom: ${data.lastName}</p>`;
            }

            customerDetails += `<p>Ville: ${data.city}</p>`;
            customerDetails += `<p>Adresse: ${data.streetNumber} ${data.streetName}, ${data.postalCode}</p>`;
            customerDetails += `<p>Email: ${data.email}</p>`;
            customerDetails += `<p>Téléphone: ${data.phone}</p>`;
            customerDetails += `<p>Devis en attente: ${data.devisCounts.pending}</p>`;
            customerDetails += `<p>Devis payés: ${data.devisCounts.paid}</p>`;
            customerDetails += `<p>Devis partiellement payés: ${data.devisCounts.partial}</p>`;
            customerDetails += `<p>Devis remboursés: ${data.devisCounts.refunded}</p>`;
            customerDetails += `<p>Total de devis: ${data.totalDevisCount}</p>`;

            modalBody.innerHTML = customerDetails;
            $('#customerDetailsModal').modal('show');
        })
        .catch(error => console.error('Erreur lors de la récupération des données:', error));
}

function openCustomerEditModal(customerId) {
    fetch(`/customer/${customerId}/edit`)
        .then(response => response.text())
        .then(html => {
            const modalBody = document.querySelector('#customerEditModal .modal-body');
            modalBody.innerHTML = html;
            initializeFormFields();
            $('#customerEditModal').modal('show');
        })
        .catch(error => console.error('Erreur lors de la récupération du formulaire:', error));
}

function openCustomerCreateModal() {
    fetch(`/customer/new`)
        .then(response => response.text())
        .then(html => {
            const modalBody = document.querySelector('#customerCreateModal .modal-body');
            modalBody.innerHTML = html;
            initializeFormFields();
            $('#customerCreateModal').modal('show');
        })
        .catch(error => console.error('Erreur lors de la récupération du formulaire:', error));
}
