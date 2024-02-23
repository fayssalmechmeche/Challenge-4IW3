// document.addEventListener("DOMContentLoaded", function () {
//   fetch("/accountant/api")
//     .then((response) => response.json())
//     .then((data) => {
//       createBarChart(data);
//     })
//     .catch((error) => {
//       console.error("Erreur lors de la récupération des données :", error);
//     });
// });

console.log(labelChartCA);
console.log(dataChartCA);

const chartCA = document.getElementById("chartCA");
new Chart(chartCA, {
  type: "bar",
  data: {
    labels: labelChartCA,
    datasets: [
      {
        label: "€",
        data: dataChartCA,
        borderWidth: 0,
        backgroundColor: [
          "rgb(255, 99, 132)",
          "rgb(54, 162, 235)",
          "rgb(255, 205, 86)",
        ],
        color: "#ff89",
      },
    ],
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
      },
    },
    plugins: {
      legend: {
        display: false,
        // labels: {
        //   boxWidth: 0,
        //   boxHeight: 0,
        // },
      },
    },
  },
});

const chartProducts = document.getElementById("chartProducts");
console.log(dataChartProducts);
new Chart(chartProducts, {
  type: "doughnut",
  data: {
    labels: labelChartProducts,
    datasets: [
      {
        label: "Total commandes",
        data: dataChartProducts,
        borderWidth: 0,
        backgroundColor: [
          "rgb(255, 99, 132)",
          "rgb(54, 162, 235)",
          "rgb(255, 205, 86)",
        ],
      },
    ],
  },
  options: {
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: "right",
        labels: {
          boxWidth: 10,
          boxHeight: 10,
        },
      },
    },
  },
});

const chartCustomers = document.getElementById("chartCustomers");
console.log(dataChartCustomers);
new Chart(chartCustomers, {
  type: "doughnut",
  data: {
    labels: labelChartCustomers,
    datasets: [
      {
        label: "Total commandes",
        data: dataChartCustomers,
        borderWidth: 0,
        backgroundColor: [
          "rgb(255, 99, 132)",
          "rgb(54, 162, 235)",
          "rgb(255, 205, 86)",
        ],
      },
    ],
  },
  options: {
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: "right",
        labels: {
          boxWidth: 10,
          boxHeight: 10,
        },
      },
    },
  },
});
