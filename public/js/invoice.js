function toggleModal(modalId, modalContentId) {
    addClassToElement();
    let modal = document.getElementById(modalId);
    let modalContent = document.getElementById(modalContentId);
    let sectionToHide = document.getElementById('sectionToHide');
    let sectionToHide2 = document.getElementById('sectionToHide2');

    if (modal.classList.contains("opacity-0")) {
        // Ouvrir le modal
        modal.classList.remove("hidden");
        setTimeout(() => {
            modal.classList.remove("opacity-0", "transition-opacity", "duration-500", "ease-in");
            modal.classList.add("opacity-100", "transition-opacity", "duration-500", "ease-out");
        }, 10);

        modalContent.classList.remove("hidden");
        setTimeout(() => {
            modalContent.classList.remove("top-up");
            modalContent.classList.add("top-1/2");
        }, 10);
    } else {
        // Fermer le modal avec une transition d'opacité
        modal.classList.remove("opacity-100", "transition-opacity", "duration-500", "ease-out");
        modal.classList.add("opacity-0", "transition-opacity", "duration-500", "ease-in");

        setTimeout(() => {
            modal.classList.add("hidden");

            // Réinitialiser les sections ici
            if (sectionToHide) {
                sectionToHide.classList.add("hidden");
            }
            if (sectionToHide2) {
                sectionToHide2.classList.remove("hidden");
            }
        }, 500);

        modalContent.classList.remove("top-1/2");
        modalContent.classList.add("top-up");

        setTimeout(() => {
            modalContent.classList.add("hidden");
        }, 500);
    }
}


function openInvoiceCreateModal() {
    const modalBody = document.querySelector("#invoiceCreateModalContent");
    toggleModal("invoiceCreateModal", "invoiceCreateModalContent");

}

function addClassToElement() {
    //Ce script est propre à ce formulaire : il rajoute une class à la div id product qui ne peut être gérée dynamiquement depuis le form builder. #}
    let formulaDiv = document.getElementById("invoice");
    if (formulaDiv) {
        formulaDiv.classList.add("flex", "flex-wrap", "gap-y-5", "gap-x-10");
    }
}

function modalSwap() {
    var modalSwapButton = document.querySelector('.modalswap');
    var sectionToHide = document.getElementById('sectionToHide');
    var sectionToHideAgain = document.getElementById('sectionToHide2');
    if (modalSwapButton && sectionToHide) {
        modalSwapButton.addEventListener('click', function () {
            sectionToHide.classList.remove("hidden");
            sectionToHideAgain.classList.add("hidden");
        });
    }
}

function setupReturnButton() {
    var returnButtons = document.querySelectorAll('.btn-retour');
    returnButtons.forEach(function(returnButton) {
        returnButton.addEventListener('click', function() {
            var sectionToHide = document.getElementById('sectionToHide');
            var sectionToHide2 = document.getElementById('sectionToHide2');
            if (sectionToHide) sectionToHide.classList.add("hidden");
            if (sectionToHide2) sectionToHide2.classList.remove("hidden");
        });
    });
}



document.addEventListener('DOMContentLoaded', function() {
    modalSwap();
    setupReturnButton();
});

document.querySelector('.valid-invoice').addEventListener('click', function() {
    const invoiceDataElement = document.querySelector('#invoiceData');
    const devisId = invoiceDataElement.getAttribute('data-devis-id');
    const invoiceNumber = invoiceDataElement.getAttribute('data-invoice-number');

    // Préparation de l'objet JSON à envoyer
    const invoiceData = {
        devisId: devisId,
        invoiceNumber: invoiceNumber,
        paymentStatus: 'PENDING',
        taxe: invoiceDataElement.getAttribute('data-taxe'), // Valeur fictive pour la taxe
        totalPrice: invoiceDataElement.getAttribute('data-total-price'), // Valeur fictive pour le prix total
        totalDuePrice: invoiceDataElement.getAttribute('data-total-due-price'), // Valeur fictive pour le total dû
        remise: '0', // Valeur fictive pour la remise
        paymentDueTime: '2024-02-20T08:00:00', // Date fictive d'échéance de paiement
    };
    console.log(invoiceData,typeof(invoiceData.paymentStatus));
    fetch('/invoice/new/ajax', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(invoiceData)// Conversion de l'objet JavaScript en chaîne JSON
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Success:', data);
        })
        .catch((error) => {
            console.error('Error:', error);
        });
});


console.log("empty men");



