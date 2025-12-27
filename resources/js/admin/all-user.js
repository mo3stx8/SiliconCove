// Set delete form action
function setDeleteUser(userId) {
  document.getElementById("deleteUserForm").action =
    "/admin/all-users/" + userId;
}

// Fade out success alert
document.addEventListener("DOMContentLoaded", function () {
  let alertBox = document.getElementById("successMessage");

  if (alertBox) {
    setTimeout(function () {
      alertBox.style.transition = "opacity 1s ease-out";
      alertBox.style.opacity = "0";
      setTimeout(() => alertBox.remove(), 1000);
    }, 2000);
  }
});

// View user details modal
function viewUserDetails(user) {
  document.getElementById("viewUserName").innerText = user.name;
  document.getElementById("viewUserEmail").innerText = user.email;
  document.getElementById("viewUserRole").innerText = user.role;
  document.getElementById("viewUserCreatedAt").innerText =
    new Date(user.created_at).toLocaleDateString();

  const modal = new bootstrap.Modal(
    document.getElementById("viewUserModal")
  );
  modal.show();
}

// Make functions globally accessible
window.setDeleteUser = setDeleteUser;
window.viewUserDetails = viewUserDetails;
