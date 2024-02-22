/** @type {import('tailwindcss').Config} */
module.exports = {
  mode: "jit",
  content: [
    "./templates/**/*.twig", // Cible tous les fichiers Twig dans `templates` et ses sous-dossiers
    "./assets/**/*.js", // Cible tous les fichiers JS dans `assets` et ses sous-dossiers
    "./public/js/**/*.js", // Cible tous les fichiers JS dans `public/js` et ses sous-dossiers
  ],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "regal-blue": "#243c5a",
        "the-grey": "#b7bbc8",
        "the-grey-2": "#d4d4d4",
        "light-dark": "#353535",
        "devis-bg": "#adadad",
        "landing-bg": "#FDB833",
        "button-blue": "#0252C5",
        "button-yellow": "#FDB833",
        "button-blue-hover": "#023c8d",
        "nav-bg": "#FBFCFC",
        "nav-menu": "#000D31",
        "nav-btn": "#3347FD",
        "card-grey": "#8A92A6",
        "progress-bar-1": "#3A57E8",
        "progress-bar-2": "#08B1BA",
        caterer: "#EFF1F6",
        "grey-p": "#A1A0BD",
        "light-red": "#D14F4F",
        "bg-light-red": "#F5DCDC",
        "light-green": "#4FD18B",
        "bg-light-green": "#DCF5E8",
        "nav-bar-black": "#353535",
        //DARK MODE
        "dark-bg": "#0b1120",
        "dark-section": "#1c2c54",
        "dark-card": "#38446c",
      },
      spacing: {
        "90-vh": "90vh",
        "80-vh": "80vh",
        "95%": "95%",
        "2px": "2px",
        "15%": "15%",
        A4w: "794px",
        A4h: "1123px",
        "5px": "5px",
        "15px": "15px",
        "35%": "35%",
        "37%": "37%",
        "50px": "50px",
        "70%": "70%",
        "closed-nav": "48px",
        "open-nav": "257px",
        "modal-width": "1031px",
        "modal-width2": "70%",
        "modal-height": "90%",
        up: "-1000px",
        "550px": "550px",
        "500px": "500px",
        "1150px": "1150px",
        "90%": "90%",
      },
      width: {
        "content-space-open": "calc(100% - 257px)",
        "content-space-closed": "calc(100% - 48px)",
        "grid-dashboard-space": "calc(100% - 410px)",
        "ds-block": "1150px",
        "1/2.5": "45%",
      },
      borderRadius: {
        "10px": "10px",
        "btn-rounded": "47px",
        "155px": "155px",
      },
      fontSize: {
        "15px": "15px",
      },
      fontFamily: {
        roboto: ["Roboto", "sans-serif"],
      },
      minHeight: {
        "90vh": "90vh",
        "80vh": "80vh",
      },
      boxShadow: {
        form: "0px 4px 18px 0px rgba(158, 168, 189, 0.31)",
      },
      screens: {
        "3xl": "1618px",
        // => @media (min-width: 1280px) { ... }
      },
    },
  },
  plugins: [
    require('tailwindcss'),
    require('autoprefixer'),
    // Other PostCSS plugins as needed
  ]
};
