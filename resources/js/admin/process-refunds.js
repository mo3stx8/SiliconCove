/* ===============================
   CSRF SETUP & DATATABLES
================================ */
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    });

    if ($("#pendingRefundsTable").length) {
        $("#pendingRefundsTable").DataTable();
    }

    if ($("#processedRefundsTable").length) {
        $("#processedRefundsTable").DataTable();
    }
});

/* ===============================
   VIEW REFUND MODAL
================================ */
window.viewRefund = function (order) {
    $("#orderNumber").text(order.order_no);
    $("#customerName").text(order.name);
    $("#productName").text(order.product?.name || "N/A");
    $("#orderDate").text(order.created_at);
    $("#paymentMethod").text(order.payment_method.toUpperCase());

    const statusClass = {
        refund_requested: "bg-warning",
        refunded: "bg-success",
        refund_rejected: "bg-danger",
    }[order.raw_status] || "bg-secondary";

    $("#refundStatus").html(
        `<span class="badge ${statusClass}">
            ${order.raw_status.replace("_", " ").toUpperCase()}
        </span>`
    );

    $("#totalAmount").text(order.total_amount);
    $("#requestDate").text(order.refund_requested_date);
    $("#refundReason").text(order.refund_reason || "No reason provided");

    $("#viewRefundModal").modal("show");
};

/* ===============================
   APPROVE / DENY LOGIC
================================ */
let currentOrderId = null;

window.approveRefund = function (orderId) {
    currentOrderId = orderId;
};

window.denyRefund = function (orderId) {
    currentOrderId = orderId;
};

window.submitRefundAction = function (action) {
    if (!currentOrderId) return;

    const url =
        action === "approve"
            ? `/admin/orders/${currentOrderId}/approve-refund`
            : `/admin/orders/${currentOrderId}/deny-refund`;

    $.ajax({
        url: url,
        type: "POST",
        success: function (response) {
            if (response.success) {
                $(`#${action}RefundModal`).modal("hide");
                window.location.reload();
            } else {
                alert(response.message || "Error processing refund");
            }
        },
        error: function (xhr) {
            console.error("Refund error:", xhr);
            alert("Error processing refund");
        },
    });
};
