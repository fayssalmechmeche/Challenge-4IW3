document.addEventListener("DOMContentLoaded", darkModeInit);

function darkModeInit() {
  // Récupère le thème actuel du stockage local ou définit le mode clair par défaut
  const currentTheme = localStorage.getItem("theme") || "light";
  

  // Applique le thème au chargement de la page
  if (currentTheme === "dark") {
    document.body.classList.add("dark");
    document.body.style.transition = "background-color 0.5s ease";
    document.body.classList.add("bg-dark-bg");
    document.getElementById("toggleDarkMode").style.display = "none";
    document.getElementById("toggleLightMode").style.display = "flex";
  } else {
    document.getElementById("toggleLightMode").style.display = "none";
    document.getElementById("toggleDarkMode").style.display = "flex";
  }

  // Fonction pour basculer le thème
  function toggleTheme(isDark) {
    if (isDark) {
      document.body.classList.add("dark");
      localStorage.setItem("theme", "dark");
    document.body.classList.add("bg-dark-bg");
      document.getElementById("toggleDarkMode").style.display = "none";
      document.getElementById("toggleLightMode").style.display = "flex";
    } else {
      document.body.classList.remove("dark");
      localStorage.setItem("theme", "light");
    document.body.classList.remove("bg-dark-bg");
      document.getElementById("toggleLightMode").style.display = "none";
      document.getElementById("toggleDarkMode").style.display = "flex";
    }
  }

  // Écouteurs d'événements pour les boutons de basculement
  document
    .getElementById("toggleLightMode")
    .addEventListener("click", () => toggleTheme(false));
  document
    .getElementById("toggleDarkMode")
    .addEventListener("click", () => toggleTheme(true));
}
