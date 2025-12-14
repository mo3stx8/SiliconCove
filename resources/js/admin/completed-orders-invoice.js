// resources/js/admin/completed-orders-invoice.js
// Exposes a global handleInvoiceClick function for inline onclick handlers:
// onclick="handleInvoiceClick(event, this)"
// This function shows a loading spinner and navigates to href.

(function () {
    // Define the function on the window so inline onclick attributes can call it
    window.handleInvoiceClick = function (event, button) {
        try {
            if (event && event.preventDefault) event.preventDefault();

            if (!button) return;

            // Add disabled state
            button.classList.add('disabled');

            // Toggle icon/text/spinner
            var icon = button.querySelector('.invoice-icon');
            var text = button.querySelector('.invoice-text');
            var spinner = button.querySelector('.loading-spinner');

            if (icon) icon.classList.add('d-none');
            if (text) text.classList.add('d-none');
            if (spinner) spinner.classList.remove('d-none');

            // Get destination
            var url = button.getAttribute('href');
            if (url) {
                // Use location.href to navigate
                window.location.href = url;
            }

            // In case navigation doesn't happen quickly (e.g. blocked), restore after 3s
            setTimeout(function () {
                button.classList.remove('disabled');
                if (icon) icon.classList.remove('d-none');
                if (text) text.classList.remove('d-none');
                if (spinner) spinner.classList.add('d-none');
            }, 3000);
        } catch (err) {
            // eslint-disable-next-line no-console
            console.error("Error in handleInvoiceClick:", err);
        }
    };
})();
