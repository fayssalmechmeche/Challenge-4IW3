/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
  safelist: ["bg-green-400", "top-1/2", "translate-x-full", 'bg-orange-400','hover:bg-orange-600'],
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
        "button-blue-hover": "#023c8d",
      },
      spacing: {
        "90-vh": "90vh",
        "95%": "95%",
        "2px": "2px",
        "15%": "15%",
        A4w: "794px",
        A4h: "1123px",
        "5px": "5px",
        "15px": "15px",
        "35%": "35%",
        "50px": "50px",
        "70%": "70%",
      },
      top: {
        "from-top": "-100%",
      },
      borderRadius: {
        "10px": "10px",
      },
      fontSize: {
        "15px": "15px",
      },
      fontFamily: {
        roboto: ["Roboto", "sans-serif"],
      },
      minHeight: {
        "90vh": "90vh",
      },
    },
  },
  plugins: [],
};
