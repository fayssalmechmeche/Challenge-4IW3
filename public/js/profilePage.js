document.addEventListener("DOMContentLoaded", function () {
  const alertsContainer = document.getElementById("alerts");
  const alertsData = alertsContainer.dataset.alert;

  if (alertsData !== "[]") {
    const alerts = JSON.parse(alertsData);
    Object.entries(alerts).forEach(([type, messages]) => {
      messages.forEach((message) => addFlash(type, message));
    });
  }
});
