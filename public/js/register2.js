var btn = document.getElementById("nextStep");
btn.addEventListener("click", checkInputs);

async function checkInputs() {
  console.log("click");
  var name = document.getElementById("registration_form_name");
  var lastName = document.getElementById("registration_form_lastName");
  var email = document.getElementById("registration_form_email");
  var password = document.getElementById(
    "registration_form_plainPassword_first"
  );
  var passwordConfirm = document.getElementById(
    "registration_form_plainPassword_second"
  );

  var nameError = document.getElementById("userName");
  var lastNameError = document.getElementById("userLastName");
  var emailError = document.getElementById("userEmail");
  var passwordError = document.getElementById("userPassword");
  var passwordConfirmError = document.getElementById("userPasswordConfirm");

  function validateInput(input, errorDiv, errorMessage) {
    if (input.value.length < 2) {
      input.classList.replace("border-black", "border-red-500");
      errorDiv.textContent = errorMessage;
      return false;
    } else if (
      (input.id == "registration_form_plainPassword_first" ||
        input.id == "registration_form_plainPassword_second") &&
      input.value.length < 6 //Verifie que le mdp < que 6 caractères
    ) {
      input.classList.replace("border-black", "border-red-500");
      errorDiv.textContent = errorMessage;
      return false;
    } else {
      input.classList.replace("border-red-500", "border-black");
      errorDiv.textContent = "";
      return true;
    }
  }

  let isValid = true;

  isValid =
    validateInput(
      name,
      nameError,
      "Le prénom doit contenir au moins 2 caractères"
    ) && isValid;
  console.log("name", isValid);
  isValid =
    validateInput(
      lastName,
      lastNameError,
      "Le nom doit contenir au moins 2 caractères"
    ) && isValid;
  console.log("lastname", isValid);
  isValid =
    validateInput(
      password,
      passwordError,
      "Le mot de passe doit contenir au moins 6 caractères"
    ) && isValid;
  console.log("pwd", isValid);
  isValid =
    validateInput(
      passwordConfirm,
      passwordConfirmError,
      "La confirmation mot de passe est requis"
    ) && isValid;
  console.log("confPdw", isValid);

  if (email.value === "") {
    email.classList.replace("border-black", "border-red-500");
    emailError.textContent = "L'email est requis";
    isValid = false;
  } else if (await checkEmail(email.value)) {
    email.classList.replace("border-black", "border-red-500");
    emailError.textContent = "Cet e-mail existe déjà.";
    isValid = false;
  } else {
    email.classList.replace("border-red-500", "border-black");
    emailError.textContent = "";
  }
  console.log("passb", isValid);
  if (isValid) {
    console.log("pass", isValid);
    nextPage();
  }

  console.log("email", await checkEmail(email.value));
}

async function checkEmail(email) {
  const url = "/check/email";

  try {
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: `email=${encodeURIComponent(email)}`,
    });

    if (response.status === 200) {
      const data = await response.json();
      return !data.success; // renvoie true si l'utilisateur existe, sinon false
    } else {
      throw new Error("Erreur lors de la requête.");
    }
  } catch (error) {
    console.error("Erreur lors de la vérification de l'e-mail:", error);
    return false; // En cas d'erreur, considérez que l'utilisateur existe déjà
  }
}

function nextPage() {
  const timeAnim = 380;
  const timing = {
    duration: timeAnim,
    easing: "cubic-bezier(0,1,0.5,1)",
  };

  const main = document.getElementById("main");
  const inputsForm = document
    .getElementById("registration_form")
    .getElementsByTagName("input");
  const sectionForm = document.getElementById("sectionForm");
  const sectionCube = document.getElementById("sectionCube");

  if (main.children[0].id == "sectionForm") {
    if (document.documentElement.clientWidth < 640) {
      sectionForm.animate([{ transform: "rotateY(360deg)" }], timing);
    } else {
      sectionCube.animate([{ transform: "translateX(-140%)" }], timing);
      sectionForm.animate([{ transform: "translateX(71.5%)" }], timing);
    }
    document.getElementById("step1").classList.add("hidden");
    document.getElementById("step2").classList.remove("hidden");
  } else {
    if (document.documentElement.clientWidth < 640) {
      sectionForm.animate([{ transform: "rotateY(360deg)" }], timing);
    } else {
      sectionCube.animate([{ transform: "translateX(140%)" }], timing);
      sectionForm.animate([{ transform: "translateX(-71.5%)" }], timing);
    }
    document.getElementById("step1").classList.remove("hidden");
    document.getElementById("step2").classList.add("hidden");
  }

  //Stock les valeurs des input avant de passer à l'etape 2 (changement DOM)
  var valuesForm = [];
  for (const input of inputsForm) {
    valuesForm.push(input.value);
  }

  setTimeout(() => {
    //Change de place les composants dans le DOM
    tmp = main.children[0].outerHTML;
    main.children[0].outerHTML = main.children[1].outerHTML;
    main.children[1].outerHTML = tmp;

    const newForm = document
      .getElementById("registration_form")
      .getElementsByTagName("input");

    //Rempli le nouveau formualaire après le changement dans le DOM
    for (let i = 0; i < valuesForm.length; i++) {
      if (newForm[i].type != "file") {
        newForm[i].value = valuesForm[i];
      }
    }

    btn = document.getElementById("nextStep");
    btn.addEventListener("click", checkInputs);
  }, timeAnim - 15);
}
