let fixedNavbar = true;
document.addEventListener("DOMContentLoaded", () => {
  toggleNavbar();
  hoverNavbar();
  navClick();
});

function toggleNavbar() {
  let navbar = document.querySelector(".vertical-nav");
  let navbarToggle = document.querySelector(".navbar-toggle");
  let navbarHiddenSpace = document.querySelector(".navbar-hidden-space");
  let logoLight = document.querySelector(".nav-logo-light");
  let logoDark = document.querySelector(".nav-logo-dark");
  let siteContent = document.querySelector(".site-content");
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
      navbarHiddenSpace.classList.remove("w-open-nav");
      navbarHiddenSpace.classList.add(
        "w-closed-nav",
        "duration-300",
        "ease-out"
      );
      // Content
      siteContent.classList.remove("w-content-space-open");
      siteContent.classList.add(
        "w-content-space-closed",
        "duration-300",
        "ease-in"
      );
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
      navbarHiddenSpace.classList.remove("w-closed-nav");
      navbarHiddenSpace.classList.add("w-open-nav", "duration-300", "ease-in");
      // Content
      siteContent.classList.remove("w-content-space-closed");
      siteContent.classList.add(
        "w-content-space-open",
        "duration-300",
        "ease-out"
      );
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
  let navbarHiddenSpace = document.querySelector(".navbar-hidden-space");
  let logoLight = document.querySelector(".nav-logo-light");
  let logoDark = document.querySelector(".nav-logo-dark");
  let siteContent = document.querySelector(".site-content");
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
    navbarHiddenSpace.classList.remove("w-closed-nav");
    navbarHiddenSpace.classList.add("w-open-nav", "duration-300", "ease-in");
    // Content
    siteContent.classList.remove("w-content-space-closed");
    siteContent.classList.add(
      "w-content-space-open",
      "duration-300",
      "ease-out"
    );
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
    navbarHiddenSpace.classList.remove("w-open-nav");
    navbarHiddenSpace.classList.add("w-closed-nav", "duration-300", "ease-out");
    // Content
    siteContent.classList.remove("w-content-space-open");
    siteContent.classList.add(
      "w-content-space-closed",
      "duration-300",
      "ease-in"
    );
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
