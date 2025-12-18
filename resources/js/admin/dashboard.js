/* ===============================
   GLOBAL DATA (from Blade)
================================ */
const salesData = window.salesData || {};
const profitData = window.profitData || {};

/* ===============================
   CHART INITIALIZATION
================================ */
let salesChart, profitChart;

document.addEventListener("DOMContentLoaded", () => {
    initSalesChart();
    initProfitChart();
    updateCharts("daily");
});

/* ===============================
   SALES CHART
================================ */
function initSalesChart() {
    const ctx = document.getElementById("salesChart")?.getContext("2d");
    if (!ctx) return;

    salesChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: [],
            datasets: [{
                label: "Sales",
                data: [],
                borderColor: "rgb(75, 192, 192)",
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => "$" + value.toLocaleString()
                    }
                }
            }
        }
    });
}

/* ===============================
   PROFIT CHART
================================ */
function initProfitChart() {
    const ctx = document.getElementById("profitChart")?.getContext("2d");
    if (!ctx) return;

    profitChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: [],
            datasets: [{
                label: "Profit",
                data: [],
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                borderColor: "rgb(75, 192, 192)",
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => "$" + value.toLocaleString()
                    }
                }
            }
        }
    });
}

/* ===============================
   UPDATE CHARTS
================================ */
window.updateChart = function (period) {
    updateCharts(period);
};

function updateCharts(period) {
    const sData = salesData[period] || [];
    const pData = profitData[period] || [];

    // Sales
    salesChart.data.labels = sData.map(item =>
        period === "daily" ? item.hour :
        period === "monthly" ? getMonthName(item.month) :
        item.year
    );
    salesChart.data.datasets[0].data = sData.map(item => item.total);
    salesChart.update();

    // Profit
    profitChart.data.labels = pData.map(item =>
        period === "daily" ? item.hour :
        period === "monthly" ? getMonthName(item.month) :
        item.year
    );
    profitChart.data.datasets[0].data = pData.map(item => item.profit);
    profitChart.update();
}

/* ===============================
   HELPERS
================================ */
function getMonthName(monthNumber) {
    return new Date(2000, monthNumber - 1).toLocaleString("default", {
        month: "long"
    });
}

/* ===============================
   RESTOCK MODAL
================================ */
window.showRestockModal = function (productId) {
    document.getElementById("restockProductId").value = productId;
    new bootstrap.Modal(document.getElementById("restockModal")).show();
};

/* ===============================
   SCROLL
================================ */
window.scrollToDailySalesDetail = function () {
    const el = document.getElementById("dailySalesDetailRow");
    if (el) {
        el.scrollIntoView({ behavior: "smooth", block: "start" });
    }
};

/* ===============================
   COMPARISON MODAL
================================ */
let compareDailyChart, compareMonthlyChart, compareYearlyChart;

window.showComparisonModal = function () {
    destroyComparisonCharts();

    renderCompareChart("compareDailyChart", window.compareDailyData);
    renderCompareChart("compareMonthlyChart", window.compareMonthlyData);
    renderCompareChart("compareYearlyChart", window.compareYearlyData);

    new bootstrap.Modal(document.getElementById("comparisonModal")).show();
};

function renderCompareChart(canvasId, chartData) {
    const ctx = document.getElementById(canvasId)?.getContext("2d");
    if (!ctx) return;

    return new Chart(ctx, {
        type: "bar",
        data: {
            labels: chartData.labels,
            datasets: [{
                label: "Sales",
                data: chartData.data,
                backgroundColor: ["#6c757d", "#0d6efd"]
            }]
        }
    });
}

function destroyComparisonCharts() {
    [compareDailyChart, compareMonthlyChart, compareYearlyChart].forEach(c => {
        if (c) c.destroy();
    });
}
