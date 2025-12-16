let currentOrderId = null;
let currentCompleteOrderId = null;

/* -------------------------
    Approve Order
-------------------------- */
window.setApproveOrder = function (orderId) {
    currentOrderId = orderId;
};

window.submitApproveForm = function () {
    if (!currentOrderId) return;

    const form = document.getElementById("approveOrderForm");
    const formData = new FormData(form);

    fetch(`/admin/orders/${currentOrderId}/approve`, {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": window.csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify(Object.fromEntries(formData)),
    })
        .then(res => res.json())
        .then(data => {
            handleResponse(data, "#approveOrderModal");
        })
        .catch(() => {
            showAlert("Error approving order", "danger");
        });
};

/* -------------------------
    Process Order
-------------------------- */
let currentProcessOrderId = null;

window.setProcessOrder = function (orderId) {
    currentProcessOrderId = orderId;
};

window.submitProcessForm = function () {
    if (!currentProcessOrderId) return;

    fetch(`/admin/orders/${currentProcessOrderId}/process`, {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": window.csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                $("#processOrderModal").modal("hide");
                showAlert(data.message, "success");
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert(data.message, "danger");
            }
        })
        .catch(() => {
            showAlert("Error processing order", "danger");
        });
};

/* -------------------------
    Complete Order
-------------------------- */
window.setCompleteOrder = function (orderId) {
    currentCompleteOrderId = orderId;
};

window.submitCompleteForm = function () {
    if (!currentCompleteOrderId) return;

    const form = document.getElementById("completeOrderForm");
    const submitButton = document.getElementById("completeOrderBtn");
    const formData = new FormData(form);

    submitButton.disabled = true;
    submitButton.className = "btn btn-secondary text-muted opacity-75";
    submitButton.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

    fetch(`/admin/orders/${currentCompleteOrderId}/complete`, {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": window.csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify(Object.fromEntries(formData)),
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success) restoreCompleteButton(submitButton);
            handleResponse(data, "#completeOrderModal");
        })
        .catch(() => {
            restoreCompleteButton(submitButton);
            showAlert("Error completing order", "danger");
        });
};

/* -------------------------
   Helpers
-------------------------- */
function handleResponse(data, modalId) {
    if (data.success) {
        $(modalId).modal("hide");
        showAlert(data.message, "success");
        setTimeout(() => window.location.reload(), 1000);
    } else {
        showAlert(data.message, "danger");
    }
}

function restoreCompleteButton(btn) {
    btn.disabled = false;
    btn.className = "btn btn-success";
    btn.innerHTML = "Complete Order";
}

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document
        .querySelector(".pending-orders")
        .insertAdjacentHTML("afterbegin", alertHtml);
}

/* -------------------------
   DOM Ready
-------------------------- */
document.addEventListener("DOMContentLoaded", function () {
    const alertBox = document.getElementById("successMessage");
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 1000);
        }, 2000);
    }

    $(".data-table").DataTable({
        paging: false,
        searching: false,
        ordering: true,
        info: false,
    });
});
