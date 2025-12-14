// import Chart from '/public/js/admin/Chart.min.js';

const { salesData, profitData, comparison } = window.dashboardData;

// Charts
const salesChart = new Chart(
    document.getElementById('salesChart'),
    {
        type: 'line',
        data: { labels: [], datasets: [{ label: 'Sales', data: [] }] },
        options: { responsive: true }
    }
);

const profitChart = new Chart(
    document.getElementById('profitChart'),
    {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Profit', data: [] }] },
        options: { responsive: true }
    }
);

function getMonthName(month) {
    return new Date(2000, month - 1).toLocaleString('default', { month: 'long' });
}

// Make functions GLOBAL if used by onclick=""
window.updateChart = function (period) {
    const sales = salesData[period];
    const profit = profitData[period];

    salesChart.data.labels = sales.map(i =>
        period === 'daily' ? i.hour :
        period === 'monthly' ? getMonthName(i.month) :
        i.year
    );

    salesChart.data.datasets[0].data = sales.map(i => i.total);
    salesChart.update();

    profitChart.data.labels = profit.map(i =>
        period === 'daily' ? i.hour :
        period === 'monthly' ? getMonthName(i.month) :
        i.year
    );

    profitChart.data.datasets[0].data = profit.map(i => i.profit);
    profitChart.update();
};

// Init
updateChart('daily');

// Restock modal
window.showRestockModal = function (productId) {
    document.getElementById('restockProductId').value = productId;
    new bootstrap.Modal(document.getElementById('restockModal')).show();
};

// Scroll helper
window.scrollToDailySalesDetail = function () {
    document
        .getElementById('dailySalesDetailRow')
        ?.scrollIntoView({ behavior: 'smooth' });
};

window.showComparisonModal = function () {
    new Chart(document.getElementById('compareDailyChart'), {
        type: 'bar',
        data: {
            labels: ['Yesterday', 'Today'],
            datasets: [{
                data: [
                    comparison.daily.yesterday,
                    comparison.daily.today
                ]
            }]
        }
    });

    new bootstrap.Modal(
        document.getElementById('comparisonModal')
    ).show();
};
