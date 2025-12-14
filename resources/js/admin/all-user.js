function setDeleteUser(userId) {
  document.getElementById("deleteUserForm").action =
    "/admin/all-users/" + userId;
}

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
