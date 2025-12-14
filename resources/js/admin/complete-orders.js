$(document).ready(function () {
  $("#completedOrdersTable").DataTable({
    paging: true, // Enable pagination
    searching: true, // Enable search filter
    ordering: true, // Enable column sorting
    info: true, // Show table info
    lengthMenu: [5, 10, 25, 50], // Define page length options
  });
});

document.addEventListener("DOMContentLoaded", function () {
  let alertBox = document.getElementById("successMessage");

  if (alertBox) {
    setTimeout(function () {
      alertBox.style.transition = "opacity 1s ease-out";
      alertBox.style.opacity = "0";
      setTimeout(() => alertBox.remove(), 1000); // Remove from DOM after fade out
    }, 2000); // Show for 2 seconds before fading
  }
});
