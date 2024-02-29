function openTestModal() {
  const htmlContent = `
    <div>
      <button class="absolute top-3 right-3 text-white font-medium bg-red-500 hover:bg-red-700 transition-all duration-300 ease-out rounded-lg px-2 py-1" onclick="toggleModal('testModalId', 'testModalContentId')">Fermer</button>
    
      <!-- Contenu de notre modal ici -->
      <h2 class="text-3xl mb-5 dark:text-white">Exemple de modal</h2>
    
      <div class="flex flex-wrap gap-y-5 gap-x-10">
        <div class="flex flex-col px-1 my-1">
          <p class="font-medium dark:text-white">Nom du produit</p>
          <p id="formulaName" class="rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form
          flex justify-start items-center">Produit Exemple</p>
        </div>
        <div class="flex flex-col px-1 my-1">
          <p class="font-medium dark:text-white">Prix du produit</p>
          <p id="formulaPrice" class="rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form
          flex justify-start items-center">19,99 €</p>
        </div>
      </div>
    
      <div class="flex mt-7 justify-start items-center">
        <button class="text-white font-medium bg-orange-400 hover:bg-orange-600 transition-all duration-300 ease-out rounded-lg px-3 py-2 mr-3">Modifier</button>
        <!-- Ici, vous pouvez insérer un formulaire de suppression ou autre contenu statique pour remplacer le Twig include -->
      </div>
    </div>
    `;

  const modalContent = document.getElementById("testModalContentId");
  modalContent.innerHTML = htmlContent;
  toggleModal("testModalId", "testModalContentId");
}

function toggleModal(modalId, modalContentId) {
  let modal = document.getElementById(modalId);
  let modalContent = document.getElementById(modalContentId);

  if (modal.classList.contains("opacity-0")) {
    //OVERLAY
    // Ouvrir le modal
    modal.classList.remove("hidden");

    // Nécessaire pour permettre au navigateur de reconnaître que l'élément est maintenant visible
    // avant d'appliquer la transition d'opacité
    setTimeout(() => {
      modal.classList.remove(
        "opacity-0",
        "transition-opacity",
        "duration-500",
        "ease-in"
      );
      modal.classList.add(
        "opacity-100",
        "transition-opacity",
        "duration-500",
        "ease-out"
      );
    }, 10); // Un délai très court est généralement suffisant

    //MODAL CONTENT

    // Ouvrir le modal
    modalContent.classList.remove("hidden");

    // Permettre un bref délai pour que la classe 'hidden' soit complètement enlevée
    setTimeout(() => {
      modalContent.classList.remove("top-up");
      modalContent.classList.add("top-1/2");
    }, 10); // Un délai très court est généralement suffisant
  } else {
    //OVERLAY
    // Fermer le modal avec une transition d'opacité
    modal.classList.remove(
      "opacity-100",
      "transition-opacity",
      "duration-500",
      "ease-out"
    );
    modal.classList.add(
      "opacity-0",
      "transition-opacity",
      "duration-500",
      "ease-in"
    );

    // Ajouter `hidden` après que la transition soit terminée
    setTimeout(() => {
      modal.classList.add("hidden");
    }, 500); // La durée correspond à la durée de la transition

    //MODAL CONTENT
    // Animer la fermeture
    modalContent.classList.remove("top-1/2");
    modalContent.classList.add("top-up");

    // Ajouter 'hidden' après que la transition soit terminée
    setTimeout(() => {
      modalContent.classList.add("hidden");
    }, 500); // Assurez-vous que ce délai correspond à la durée de la transition
  }
}

function toggleSwitch() {
  var checkbox = document.getElementById("switchClientType");
  var switchBackground = document.getElementById("switchBackground");
  var switchDot = document.getElementById("switchDot");

  checkbox.checked = !checkbox.checked;
  checkbox.dispatchEvent(new Event("change"));

  if (checkbox.checked) {
    switchBackground.classList.remove("bg-gray-200");
    switchBackground.classList.add("bg-nav-btn");
    switchDot.classList.add("translate-x-full");
  } else {
    switchBackground.classList.remove("bg-nav-btn");
    switchBackground.classList.add("bg-gray-200");
    switchDot.classList.remove("translate-x-full");
  }
}



let fixedNavbar = true;
document.addEventListener("DOMContentLoaded", () => {
  toggleNavbar();
  hoverNavbar();
  navClick();
});

function toggleNavbar() {
  let navbar = document.querySelector(".vertical-nav");
  let navbarToggle = document.querySelector(".navbar-toggle");
  let logoLight = document.querySelector(".nav-logo-light");
  let logoDark = document.querySelector(".nav-logo-dark");
  
  let navIcons = document.querySelectorAll(".nav-icon");
  let navTexts = document.querySelectorAll(".nav-text");

  navbarToggle.addEventListener("click", () => {
    fixedNavbar = !fixedNavbar;
    if (navbar.classList.contains("w-open-nav")) {
      // Navbar
      navbarToggle.classList.remove("rotate-180");
      logoLight.classList.add("hidden");
      logoDark.classList.remove("dark:block");
      navbar.classList.remove("w-open-nav");
      navbar.classList.add("w-closed-nav", "duration-300", "ease-out");

     
      // Icons
      navIcons.forEach((icon) => {
        icon.classList.remove("mx-4");
        icon.classList.add("mx-auto");
      });
      // Link text
      navTexts.forEach((text) => {
        text.classList.add("hidden");
      });
    } else {
      // Navbar
      navbarToggle.classList.add("rotate-180");
      logoLight.classList.remove("hidden");
      logoDark.classList.add("dark:block");
      navbar.classList.remove("w-closed-nav");
      navbar.classList.add("w-open-nav", "duration-300", "ease-in");
      // Content
   
      // Icons
      navIcons.forEach((icon) => {
        icon.classList.remove("mx-auto");
        icon.classList.add("mx-4");
      });
      // Link text
      navTexts.forEach((text) => {
        text.classList.remove("hidden");
      });
    }
  });
}

function hoverNavbar() {
  let navBody = document.querySelector(".nav-body");
  let navbar = document.querySelector(".vertical-nav");
  let navbarToggle = document.querySelector(".navbar-toggle");
  let logoLight = document.querySelector(".nav-logo-light");
  let logoDark = document.querySelector(".nav-logo-dark");
  let navIcons = document.querySelectorAll(".nav-icon");
  let navTexts = document.querySelectorAll(".nav-text");

  navBody.addEventListener("mouseenter", () => {
    if (fixedNavbar) return;
    // Navbar
    navbarToggle.classList.add("rotate-180");
    logoLight.classList.remove("hidden");
    logoDark.classList.add("dark:block");
    navbar.classList.remove("w-closed-nav");
    navbar.classList.add("w-open-nav", "duration-300", "ease-in");
    // Icons
    navIcons.forEach((icon) => {
      icon.classList.remove("mx-auto");
      icon.classList.add("mx-4");
    });
    // Link text
    navTexts.forEach((text) => {
      text.classList.remove("hidden");
    });
  });

  navBody.addEventListener("mouseleave", () => {
    if (fixedNavbar) return;
    // Navbar
    navbarToggle.classList.remove("rotate-180");
    logoLight.classList.add("hidden");
    logoDark.classList.remove("dark:block");
    navbar.classList.remove("w-open-nav");
    navbar.classList.add("w-closed-nav", "duration-300", "ease-out");

 
    // Icons
    navIcons.forEach((icon) => {
      icon.classList.remove("mx-4");
      icon.classList.add("mx-auto");
    });
    // Link text
    navTexts.forEach((text) => {
      text.classList.add("hidden");
    });
  });
}

function navClick() {
  let navLinks = document.querySelectorAll(".nav-link");

  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      // Enlever les classes des autres liens
      navLinks.forEach((otherLink) => {
        if (otherLink !== link) {
          otherLink.classList.remove("bg-nav-btn");
          if (otherLink.children[1]) {
            otherLink.children[1].classList.remove("text-white");
            otherLink.children[1].classList.add("text-gray-500");
            otherLink.children[0].classList.remove("text-white");
            otherLink.children[0].classList.add("text-gray-500");
          }
        }
      });

      // Ajouter les classes au lien cliqué
      link.classList.add("bg-nav-btn");
      if (link.children[1]) {
        link.children[0].classList.remove("text-gray-500");
        link.children[0].classList.add("text-white");
        link.children[1].classList.remove("text-gray-500");
        link.children[1].classList.add("text-white");
      }
    });
  });
}
