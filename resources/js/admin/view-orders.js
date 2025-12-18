/* ===============================
   DELETE ORDER
================================ */
window.setDeleteOrder = function (id) {
    const deleteForm = document.getElementById("deleteOrderForm");
    if (deleteForm) {
        deleteForm.action = `/admin/orders/${id}/delete`;
    }
};

/* ===============================
   AUTO-HIDE SUCCESS MESSAGE
================================ */
document.addEventListener("DOMContentLoaded", function () {
    const alertBox = document.getElementById("successMessage");

    if (alertBox) {
        setTimeout(() => {
            alertBox.style.transition = "opacity 1s ease-out";
            alertBox.style.opacity = "0";

            setTimeout(() => alertBox.remove(), 1000);
        }, 2000);
    }
});

/* ===============================
   DATATABLE INITIALIZATION
================================ */
$(document).ready(function () {
    $(".data-table").each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                paging: false,
                searching: false,
                ordering: true,
                info: false,
            });
        }
    });
});
