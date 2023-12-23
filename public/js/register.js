
form = document.getElementById('registration_form');
btn = document.getElementById('btn-steps');

btn.addEventListener('click', async function () {
    console.log('click');

    var name = document.getElementById('registration_form_name');
    var lastName = document.getElementById('registration_form_lastName');
    var email = document.getElementById('registration_form_email');
    var password = document.getElementById('registration_form_plainPassword');
    var nameError = document.getElementById('userName');
    var lastNameError = document.getElementById('userLastName');
    var emailError = document.getElementById('userEmail');
    var passwordError = document.getElementById('userPassword');



    function validateInput(input, errorDiv, errorMessage) {
        if (input.value === '') {
            input.classList.replace('border-black', 'border-red-500');
            errorDiv.textContent = errorMessage;
            return false;
        } else {
            input.classList.replace('border-red-500', 'border-black');
            errorDiv.textContent = '';
            return true;
        }
    }

    let isValid = true;

    isValid = validateInput(name, nameError, 'Le prénom est requis') && isValid;
    isValid = validateInput(lastName, lastNameError, 'Le nom est requis') && isValid;
    isValid = validateInput(password, passwordError, 'Le mot de passe est requis') && isValid;

    if (email.value === '') {
        email.classList.replace('border-black', 'border-red-500');
        emailError.textContent = "L'email est requis";
        isValid = false;
    } else if (await checkEmail(email.value)) {
        email.classList.replace('border-black', 'border-red-500');
        emailError.textContent = 'Cet e-mail existe déjà.';
        isValid = false;
    } else {
        email.classList.replace('border-red-500', 'border-black');
        emailError.textContent = '';
    }
    if (!isValid) {
        return
    }

    console.log('email', await checkEmail(email.value))



    divSecond = document.getElementById('second-step');
    divSecond.classList.remove('hidden');
    divSecond.classList.add('flex');
    divSecond.classList.add('flex-col');
    btnBack = document.createElement('button');
    btnBack.setAttribute('type', 'button');
    btnBack.setAttribute('id', 'btn-steps-back');
    btnBack.setAttribute('onClick', 'back();');
    btnBack.classList.add('bg-[#3347FD]', 'rounded-3xl', 'mx-auto', 'py-2', 'px-4', 'text-white', 'text-xl', 'font-sans', 'font-light');
    btnBack.innerHTML = 'Retour en arrière';
    form.insertBefore(btnBack, form.firstChild);

    document.getElementById('first-step').classList.add('hidden');

    h1 = document.getElementById('count-steps');
    h1.innerHTML = 'Etape 2/2';

    btnBack.addEventListener('click', function () {
        divSecond = document.getElementById('second-step');
        divSecond.classList.add('hidden');
        divSecond.classList.remove('flex');
        divSecond.classList.remove('flex-col');

        document.getElementById('first-step').classList.remove('hidden');

        h1 = document.getElementById('count-steps');
        h1.innerHTML = 'Etape 1/2';
        btnBack.parentNode.removeChild(btnBack);
    });
});

async function checkEmail(email) {
    const url = '/check/email';

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `email=${encodeURIComponent(email)}`
        });

        if (response.status === 200) {
            const data = await response.json();
            return !data.success; // renvoie true si l'utilisateur existe, sinon false
        } else {
            throw new Error('Erreur lors de la requête.');
        }
    } catch (error) {
        console.error('Erreur lors de la vérification de l\'e-mail:', error);
        return false; // En cas d'erreur, considérez que l'utilisateur existe déjà
    }

}
