/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["**/**.html.twig"],
  safelist: [
    "rotate-45",
    "translate-y-3.5",
    "absolute",
    "duration-300",
    "ease-out",
    "bg-nav-btn",
    "opacity-0",
    "-rotate-45",
    "-translate-y-3.5",
    "top-[-1000px]",
    "top-28",
    "duration-300",
    "ease-out",
    "top-[-1000px]",
    "duration-300",
    "ease",
    "bg-green-400", "top-1/2", "translate-x-full", 'bg-orange-400','hover:bg-orange-600'
  ],
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
        "nav-btn": "#3347FD",
      },
      spacing: {
        "90-vh": "90vh",
        "95%": "95%",
        "2px": "2px",
        "15%": "15%",
        "A4w": "794px",
        "A4h": "1123px",
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
      },
    },
  },
  plugins: [],
};
