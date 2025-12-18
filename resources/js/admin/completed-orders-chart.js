document.addEventListener("DOMContentLoaded", function () {
    // monthlyCompletedOrders is injected from Blade
    if (typeof window.monthlyCompletedOrders === "undefined") {
        return;
    }

    const monthlyData = window.monthlyCompletedOrders;
    const allMonths = [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

    const year =
        Object.keys(monthlyData)[0]?.split(" ")[1] ||
        new Date().getFullYear();

    const chartData = allMonths.map(
        month => monthlyData[`${month} ${year}`] ?? 0
    );

    const options = {
        series: [{
            name: "Completed Orders",
            data: chartData
        }],
        chart: {
            height: 300,
            type: "line",
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: "smooth" },
        grid: {
            row: {
                colors: ["#f3f3f3", "transparent"],
                opacity: 0.5
            }
        },
        xaxis: {
            categories: allMonths
        },
        colors: ["#198754"] // success green
    };

    const chartEl = document.querySelector("#completedOrdersChart");
    if (chartEl) {
        const chart = new ApexCharts(chartEl, options);
        chart.render();
    }
});
