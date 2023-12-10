/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["**/**.html.twig"],
  theme: {
    extend: {
      colors: {
        "regal-blue": "#243c5a",
        "the-grey": "#b7bbc8",
        "the-grey-2": "#d4d4d4",
        "light-dark": "#353535",
        "devis-bg": "#adadad",
        "landing-bg": "#FDB833",
      },
      spacing: {
        "90-vh": "90vh",
        "80-vh": "80vh",
        "95%": "95%",
        "48%": "48%",
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
        "80vh": "80vh",
      },
    },
  },
  plugins: [],
};
