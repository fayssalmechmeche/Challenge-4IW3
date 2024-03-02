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
      },
    ],
  },
  options: {
    scales: {
      y: {
        ticks: { color: "#CCC" },
        beginAtZero: true,
      },
      x: {
        ticks: { color: "#CCC" },
      },
    },
    plugins: {
      legend: {
        display: false,
      },
    },
  },
});

const chartProducts = document.getElementById("chartProducts");
new Chart(chartProducts, {
  type: "doughnut",
  data: {
    labels: labelChartProducts,
    datasets: [
      {
        label: "Total commandes",
        data: dataChartProducts,
        borderWidth: 0,
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
          color: "#AAA",
          boxWidth: 10,
          boxHeight: 10,
        },
      },
    },
  },
});

const chartCustomers = document.getElementById("chartCustomers");
new Chart(chartCustomers, {
  type: "doughnut",
  data: {
    labels: labelChartCustomers,
    datasets: [
      {
        label: "Total commandes",
        data: dataChartCustomers,
        borderWidth: 0,
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
          color: "#AAA",
          boxWidth: 10,
          boxHeight: 10,
        },
      },
    },
  },
});
