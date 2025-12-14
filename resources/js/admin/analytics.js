// resources/js/view-sales.js
// Moved from inline Blade script. Expects global jQuery ($), moment, daterangepicker, ApexCharts.
// window.salesConfig must be defined (inline in Blade).

/* jQuery parts */
jQuery(document).ready(function($) {
    var cfg = window.salesConfig || {};

    // Initialize DateRangePicker with saved values or defaults
    var startDate = cfg.startDate || moment().format('MM/DD/YYYY');
    var endDate = cfg.endDate || moment().format('MM/DD/YYYY');

    $('#dateRange').daterangepicker({
        startDate: startDate,
        endDate: endDate,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // Auto-submit form when date range changes
    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        $('#salesFilterForm').submit();
    });

    // Handle success message fade out
    var alertBox = document.getElementById("successMessage");
    if (alertBox) {
        setTimeout(function() {
            alertBox.style.transition = "opacity 1s ease-out";
            alertBox.style.opacity = "0";
            setTimeout(function() {
                if (alertBox && alertBox.remove) alertBox.remove();
            }, 1000);
        }, 2000);
    }
});

/* DOMContentLoaded parts for charts */
document.addEventListener("DOMContentLoaded", function() {
    var cfg = window.salesConfig || {};

    // Sales Chart Data
    var monthlySales = cfg.monthlySales || {};
    var salesChartOptions = {
        series: [{
            name: 'Sales',
            data: Object.values(monthlySales)
        }],
        chart: {
            height: 300,
            type: 'area',
            toolbar: { show: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: { categories: Object.keys(monthlySales) },
        tooltip: {
            y: {
                formatter: function(val) {
                    var n = Number(val) || 0;
                    return "$" + n.toLocaleString();
                }
            }
        },
        colors: ['#0d6efd'],
        noData: {
            text: 'No sales data available',
            align: 'center',
            verticalAlign: 'middle',
            style: { fontSize: '16px' }
        }
    };

    var salesChartEl = document.querySelector("#salesChart");
    if (salesChartEl && typeof ApexCharts !== 'undefined') {
        var salesChart = new ApexCharts(salesChartEl, salesChartOptions);
        salesChart.render();
    }

    // Payment Method Chart
    var paymentData = cfg.paymentDistribution || {};
    var totalPayment = Object.values(paymentData).reduce(function(a, b) { return a + b; }, 0);

    var labels = Object.entries(paymentData).map(function(entry) {
        var method = entry[0], value = entry[1];
        var percentage = totalPayment === 0 ? 0 : ((value / totalPayment) * 100).toFixed(1);
        var label = '';

        switch (method) {
            case 'cod':
                label = 'Cash on Delivery (COD) - ' + percentage + '%';
                break;
            case 'Kuraimi Bank USD':
                label = 'Kuraimi Bank USD - ' + percentage + '%';
                break;
            case 'Kuraimi Bank SR':
                label = 'Kuraimi Bank SR - ' + percentage + '%';
                break;
            case 'cash':
                label = method.toUpperCase() + ' - ' + percentage + '%';
                break;
            default:
                label = method + ' - ' + percentage + '%';
        }
        return label;
    });

    var paymentMethodOptions = {
        series: Object.values(paymentData),
        labels: labels,
        chart: { type: 'pie', height: 350 },
        colors: ['#0d6efd', '#198754', '#ffc107', '#6c757d'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { height: 280 },
                legend: { position: 'bottom', offsetY: 5 }
            }
        }],
        legend: { position: 'bottom', horizontalAlign: 'center', floating: false, offsetY: 5 }
    };

    var paymentMethodEl = document.querySelector("#paymentMethodChart");
    if (paymentMethodEl && typeof ApexCharts !== 'undefined') {
        var paymentMethodChart = new ApexCharts(paymentMethodEl, paymentMethodOptions);
        paymentMethodChart.render();
    }
});
