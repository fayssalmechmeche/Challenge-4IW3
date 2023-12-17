/** @type {import('tailwindcss').Config} */
module.exports = {
  mode: "jit",
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
        "button-blue": "#0252C5",
        "button-blue-hover": "#023c8d",
        "nav-bg": "#FBFCFC",
        "nav-menu": "#000D31",
        "nav-btn": "#3A57E8",
        "card-grey": "#8A92A6",
        "progress-bar-1": "#3A57E8",
        "progress-bar-2": "#08B1BA",
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
        "50px": "50px",
        "70%": "70%",
        "closed-nav": "48px",
        "open-nav": "257px",
      },
      width: {
        "content-space-open": "calc(100% - 257px)",
        "content-space-closed": "calc(100% - 48px)",
        "grid-dashboard-space": "calc(100% - 410px)",
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
