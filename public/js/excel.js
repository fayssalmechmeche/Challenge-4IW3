

function generateDevisXLSX() { // MAKE REQUEST
    fetch(`/excel/generate/xlsx`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest', // AJAX REQUEST
        }
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status
                }`);
        }

        return response.blob();
    }).then(blob => { // Create a temporary link element to trigger the download
        download(blob, 'devis', 'xlsx');
    }).catch(error => {
        console.error('Fetch error:', error); // Handle errors
    });
}


function generateDevisPDF() { // MAKE REQUEST
    fetch(`/excel/generate/pdf`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest', // AJAX REQUEST
        }
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status
                }`);
        }

        return response.blob();
    }).then(blob => { // Create a temporary link element to trigger the download
        download(blob, 'devis', 'pdf');
    }).catch(error => {
        console.error('Fetch error:', error); // Handle errors
    });
}



function generateDevisProductXLSX() { // MAKE REQUEST
    fetch(`/excel/generate/product/xlsx`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest', // AJAX REQUEST
        }
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status
                }`);
        }

        return response.blob();
    }).then(blob => { // Create a temporary link element to trigger the download
        download(blob, 'devis_product', 'xlsx');
    }).catch(error => {
        console.error('Fetch error:', error); // Handle errors
    });
}

function generateDevisProductPDF() { // MAKE REQUEST
    fetch(`/excel/generate/product/pdf`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest', // AJAX REQUEST
        }
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status
                }`);
        }

        return response.blob();
    }).then(blob => { // Create a temporary link element to trigger the download
        download(blob, 'devis_product', 'pdf');
    }).catch(error => {
        console.error('Fetch error:', error); // Handle errors
    });
}

function generateDevisFormulaXLSX() { // MAKE REQUEST
    fetch(`/excel/generate/formula/xlsx`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest', // AJAX REQUEST
        }
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status
                }`);
        }

        return response.blob();
    }).then(blob => { // Create a temporary link element to trigger the download

        download(blob, 'devis_formula', 'xlsx');
    }).catch(error => {
        console.error('Fetch error:', error); // Handle errors
    });
}

function generateDevisFormulaPDF() { // MAKE REQUEST
    fetch(`/excel/generate/formula/pdf`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest', // AJAX REQUEST
        }
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status
                }`);
        }

        return response.blob();
    }).then(blob => { // Create a temporary link element to trigger the download

        download(blob, 'devis_formula', 'pdf');
    }).catch(error => {
        console.error('Fetch error:', error); // Handle errors
    });
}

function download(blob, filename, extension) {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    const now = new Date();
    const options = {
        // Format the date and time FR
        timeZone: 'Europe/Paris',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    };

    const formattedDateTime = now.toLocaleString('fr-FR', options).replace(/[\/: ]/g, ''); // Remove slashes, colons, and spaces
    a.download = `${filename}_${formattedDateTime}.${extension}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
