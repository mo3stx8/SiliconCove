let currentOrderId = null;
let currentCompleteOrderId = null;

const orderActionModals = {
    approve: "#approveOrderModal",
    reject: "#rejectOrderModal",
    process: "#processOrderModal",
    complete: "#completeOrderModal",
};

const orderActionForms = {
    approve: "approveOrderForm",
    reject: "rejectOrderForm",
    process: "processOrderForm",
    complete: "completeOrderForm",
};

const orderActionUrls = {
    approve: window.pendingOrderActionUrls?.approve ?? "/admin/orders/__ORDER_ID__/approve",
    reject: window.pendingOrderActionUrls?.reject ?? "/admin/orders/__ORDER_ID__/reject",
    process: window.pendingOrderActionUrls?.process ?? "/admin/orders/__ORDER_ID__/process",
    complete: window.pendingOrderActionUrls?.complete ?? "/admin/orders/__ORDER_ID__/complete",
};

function buildOrderActionUrl(action, orderId) {
    return orderActionUrls[action].replace("__ORDER_ID__", encodeURIComponent(orderId));
}

function storeOrderAction(action, orderId) {
    const formId = orderActionForms[action];
    const modalSelector = orderActionModals[action];

    if (!orderId || !formId || !modalSelector) {
        return;
    }

    const form = document.getElementById(formId);
    const modal = document.querySelector(modalSelector);

    if (!form || !modal) {
        return;
    }

    const actionUrl = buildOrderActionUrl(action, orderId);

    form.action = actionUrl;
    form.dataset.orderId = orderId;
    modal.dataset.orderId = orderId;
}

function getStoredOrderId(action, fallbackOrderId) {
    const formId = orderActionForms[action];
    const modalSelector = orderActionModals[action];

    if (!formId || !modalSelector) {
        return fallbackOrderId || null;
    }

    const form = document.getElementById(formId);
    const modal = document.querySelector(modalSelector);

    return fallbackOrderId || form?.dataset.orderId || modal?.dataset.orderId || null;
}

function getActionForm(action) {
    return document.getElementById(orderActionForms[action]);
}

/* -------------------------
    Approve Order
-------------------------- */
window.setApproveOrder = function (orderId) {
    currentOrderId = orderId;
    storeOrderAction("approve", orderId);
};

window.submitApproveForm = function () {
    const orderId = getStoredOrderId("approve", currentOrderId);

    if (!orderId) {
        showAlert("Please select an order first.", "warning");
        return;
    }

    const form = getActionForm("approve");
    const formData = new FormData(form);

    fetch(buildOrderActionUrl("approve", orderId), {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": window.csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
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
    Reject Order
-------------------------- */
let currentRejectOrderId = null;

window.setRejectOrder = function (orderId) {
    currentRejectOrderId = orderId;
    storeOrderAction("reject", orderId);
};

window.submitRejectForm = function () {
    const orderId = getStoredOrderId("reject", currentRejectOrderId);

    if (!orderId) {
        showAlert("Please select an order first.", "warning");
        return;
    }

    const reason = document.getElementById("rejectReason").value;

    fetch(buildOrderActionUrl("reject", orderId), {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": window.csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ reason }),
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                hideModal("#rejectOrderModal");
                showAlert(data.message, "success");
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert(data.message, "danger");
            }
        })
        .catch(() => {
            showAlert("Error rejecting order", "danger");
        });
};


/* -------------------------
    Process Order
-------------------------- */
let currentProcessOrderId = null;

window.setProcessOrder = function (orderId) {
    currentProcessOrderId = orderId;
    storeOrderAction("process", orderId);
};

window.submitProcessForm = function () {
    const orderId = getStoredOrderId("process", currentProcessOrderId);

    if (!orderId) {
        showAlert("Please select an order first.", "warning");
        return;
    }

    fetch(buildOrderActionUrl("process", orderId), {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": window.csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                hideModal("#processOrderModal");
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
    storeOrderAction("complete", orderId);
};

window.submitCompleteForm = function () {
    const orderId = getStoredOrderId("complete", currentCompleteOrderId);

    if (!orderId) {
        showAlert("Please select an order first.", "warning");
        return;
    }

    const form = getActionForm("complete");
    const submitButton = document.getElementById("completeOrderBtn");
    const formData = new FormData(form);

    submitButton.disabled = true;
    submitButton.className = "btn btn-secondary text-muted opacity-75";
    submitButton.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

    fetch(buildOrderActionUrl("complete", orderId), {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": window.csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
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
        hideModal(modalId);
        showAlert(data.message, "success");
        setTimeout(() => window.location.reload(), 1000);
    } else {
        showAlert(data.message, "danger");
    }
}

function openOrderActionModal(action, orderId) {
    const modalId = orderActionModals[action];

    if (!modalId || !orderId) {
        return;
    }

    if (action === "approve") {
        window.setApproveOrder(orderId);
    } else if (action === "reject") {
        window.setRejectOrder(orderId);
        const rejectReason = document.getElementById("rejectReason");
        if (rejectReason) rejectReason.value = "";
    } else if (action === "process") {
        window.setProcessOrder(orderId);
    } else if (action === "complete") {
        window.setCompleteOrder(orderId);
    }

    showModal(modalId);
}

function showModal(modalId) {
    const modalElement = document.querySelector(modalId);

    if (!modalElement || !window.bootstrap?.Modal) {
        return;
    }

    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
}

function hideModal(modalId) {
    const modalElement = document.querySelector(modalId);

    if (!modalElement || !window.bootstrap?.Modal) {
        return;
    }

    window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
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
    document.addEventListener("show.bs.modal", function (event) {
        const modalAction = event.relatedTarget?.dataset?.orderAction;
        const orderId = event.relatedTarget?.dataset?.orderId;

        storeOrderAction(modalAction, orderId);
    });

    document.addEventListener("click", function (event) {
        if (!(event.target instanceof Element)) {
            return;
        }

        const actionButton = event.target.closest("[data-order-action][data-order-id]");

        if (!actionButton || actionButton.disabled) {
            return;
        }

        openOrderActionModal(
            actionButton.dataset.orderAction,
            actionButton.dataset.orderId
        );
    });

    const alertBox = document.getElementById("successMessage");
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 1000);
        }, 2000);
    }

    if (window.jQuery?.fn?.DataTable) {
        $(".data-table").DataTable({
            paging: false,
            searching: false,
            ordering: true,
            info: false,
            autoWidth: false,
        });
    }
});
