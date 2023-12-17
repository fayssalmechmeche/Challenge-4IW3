document.addEventListener("DOMContentLoaded", () => {
  let menu = document.getElementById("menu");
  let isAnimating = false; // Variable pour suivre l'état de l'animation

  document.getElementById("burger").addEventListener("click", function () {
    console.log("click");
    menu = document.getElementById("menu");
    if (isAnimating) return; // Empêcher l'animation si elle est déjà en cours
    isAnimating = true; // Définir l'animation comme en cours
    let bars = this.querySelectorAll('.burger-bar');
    console.log(bars);
    this.classList.toggle("opened");


    //X animation
    if (this.classList.contains('opened')) {
      // Lorsque le menu est ouvert
      bars[0].classList.add('rotate-45', 'translate-y-3.5', 'absolute', "duration-300", "ease-out", "bg-nav-btn");
      bars[1].classList.add('opacity-0');
      bars[2].classList.add('-rotate-45', '-translate-y-3.5', 'absolute', "duration-300", "ease-out", "bg-nav-btn");
    } else {
      // Lorsque le menu est fermé
      bars[0].classList.remove('rotate-45', 'translate-y-3.5', 'absolute', "bg-nav-btn");
      bars[1].classList.remove('opacity-0');
      bars[2].classList.remove('-rotate-45', '-translate-y-3.5', 'absolute', "bg-nav-btn");
    }




    if (menu.classList.contains("hidden")) {
      menu.classList.toggle("hidden");
      setTimeout(() => {
        menu.classList.remove("top-[-1000px]");
        menu.classList.add("top-28", "duration-300", "ease-out");
        isAnimating = false; // Réinitialiser l'état de l'animation après la fin de l'animation
      }, 10);
    } else {
      menu.classList.remove("top-28", "duration-300", "ease-out");
      menu.classList.add("top-[-1000px]", "duration-300", "ease");
      setTimeout(() => {
        menu.classList.toggle("hidden");
        isAnimating = false; // Réinitialiser l'état de l'animation après la fin de l'animation
      }, 300);
    }
  });
});

