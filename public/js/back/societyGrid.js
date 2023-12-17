new gridjs.Grid({
    columns: [
      {
        name: "Name",
        formatter: (cell) => gridjs.html(`<b>${cell}</b>`),
      },
      "Email",
      {
        name: "Actions",
        formatter: (_, row) =>
          gridjs.html(`<a href='mailto:${row.cells[1].data}'>Email</a>`),
      },
    ],
    pagination: {
      limit: 5,
    },
    search: true,
    fixedHeader: true,
    data: [
      ["John", "john@example.com", "<button></button>"],
      ["Mark", "mark@gmail.com", "(01) 22 888 4444"],
      ["Eoin", "eoin@gmail.com", "0097 22 654 00033"],
      ["Sarah", "sarahcdd@gmail.com", "+322 876 1233"],
      ["Afshin", "afshin@mail.com", "(353) 22 87 8356"],
      ["John", "john@example.com", "<button></button>"],
      ["Mark", "mark@gmail.com", "(01) 22 888 4444"],
      ["Eoin", "eoin@gmail.com", "0097 22 654 00033"],
      ["Sarah", "sarahcdd@gmail.com", "+322 876 1233"],
      ["Afshin", "afshin@mail.com", "(353) 22 87 8356"],
      ["John", "john@example.com", "<button></button>"],
      ["Mark", "mark@gmail.com", "(01) 22 888 4444"],
      ["Eoin", "eoin@gmail.com", "0097 22 654 00033"],
      ["Sarah", "sarahcdd@gmail.com", "+322 876 1233"],
      ["Afshin", "afshin@mail.com", "(353) 22 87 8356"],
      ["John", "john@example.com", "<button></button>"],
      ["Mark", "mark@gmail.com", "(01) 22 888 4444"],
      ["Eoin", "eoin@gmail.com", "0097 22 654 00033"],
      ["Sarah", "sarahcdd@gmail.com", "+322 876 1233"],
      ["Afshin", "afshin@mail.com", "(353) 22 87 8356"],
      ["John", "john@example.com", "<button></button>"],
      ["Mark", "mark@gmail.com", "(01) 22 888 4444"],
      ["Eoin", "eoin@gmail.com", "0097 22 654 00033"],
      ["Sarah", "sarahcdd@gmail.com", "+322 876 1233"],
      ["Afshin", "afshin@mail.com", "(353) 22 87 8356"],
    ],
    // style: {
    //   table: {
    //     // border: "5px solid #ccc",
    //     // width: "10%",
    //     height: "100px !important",
    //   },
    // },
  }).render(document.getElementById("gridSociety"));
  