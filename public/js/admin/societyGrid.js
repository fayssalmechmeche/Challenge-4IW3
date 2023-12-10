new gridjs.Grid({
  columns: [
    {
      name: "Raison Sociale",
      formatter: (cell) => gridjs.html(`<b>${cell}</b>`),
    },
    "Adresse",
    "Téléphone",
    "Email",
    {
      name: "Actions",
      formatter: (_, row) =>
        gridjs.html(`
        <a href='mailto:${row.cells[0].data}'>👁‍🗨</a>
        <a href='mailto:${row.cells[3].data}'>📝</a>
        <a href='mailto:${row.cells[3].data}'>❌</a>
      `),
    },
  ],
  pagination: {
    limit: 5,
  },
  search: true,
  fixedHeader: true,
  data: [
    [
      "Orange",
      "22 Rue des Charbons",
      "0767633333",
      "john@example.com",
      "<button></button>",
    ],
    [
      "Amazon",
      "13 Avenue Felix Ford",
      "0767633333",
      "mark@gmail.com",
      "(01) 22 888 4444",
    ],
    [
      "Google",
      "67 Allée des Dauphins",
      "0767633333",
      "eoin@gmail.com",
      "0097 22 654 00033",
    ],
    [
      "Apple",
      "3 Bd de la Gare",
      "0767633333",
      "sarahcdd@gmail.com",
      "+322 876 1233",
    ],
    [
      "Ubisoft",
      "19 Rue des Potiers",
      "0767633333",
      "afshin@mail.com",
      "(353) 22 87 8356",
    ],
    [
      "John",
      "22 Rue des Charbons",
      "0767633333",
      "john@example.com",
      "<button></button>",
    ],
    [
      "Mark",
      "13 Avenue Felix Ford",
      "0767633333",
      "mark@gmail.com",
      "(01) 22 888 4444",
    ],
    [
      "Eoin",
      "67 Allée des Dauphins",
      "0767633333",
      "eoin@gmail.com",
      "0097 22 654 00033",
    ],
    [
      "Sarah",
      "3 Bd de la Gare",
      "0767633333",
      "sarahcdd@gmail.com",
      "+322 876 1233",
    ],
    [
      "Afshin",
      "19 Rue des Potiers",
      "0767633333",
      "afshin@mail.com",
      "(353) 22 87 8356",
    ],
    [
      "John",
      "22 Rue des Charbons",
      "0767633333",
      "john@example.com",
      "<button></button>",
    ],
    [
      "Mark",
      "13 Avenue Felix Ford",
      "0767633333",
      "mark@gmail.com",
      "(01) 22 888 4444",
    ],
    [
      "Eoin",
      "67 Allée des Dauphins",
      "0767633333",
      "eoin@gmail.com",
      "0097 22 654 00033",
    ],
    [
      "Sarah",
      "3 Bd de la Gare",
      "0767633333",
      "sarahcdd@gmail.com",
      "+322 876 1233",
    ],
    [
      "Afshin",
      "19 Rue des Potiers",
      "0767633333",
      "afshin@mail.com",
      "(353) 22 87 8356",
    ],
  ],
  // style: {
  //   table: {
  //     // border: "5px solid #ccc",
  //     // width: "10%",
  //     height: "100px !important",
  //   },
  // },
}).render(document.getElementById("tabSocietyGridJs"));
