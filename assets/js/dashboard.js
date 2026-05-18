document.addEventListener("DOMContentLoaded", function () {
    const revenueChartCanvas = document.getElementById("revenueChart");
    const statusChartCanvas = document.getElementById("statusChart");

    // Revenue by Establishment Year
    if (revenueChartCanvas) {
        new Chart(revenueChartCanvas, {
            type: "bar",
            data: {
                labels: ["1996", "2003", "2010"],
                datasets: [{
                    label: "Annual Revenue ($)",
                    data: [12000000, 8500000, 15000000],
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Won vs Lost Opportunities
    if (statusChartCanvas) {
        new Chart(statusChartCanvas, {
            type: "doughnut",
            data: {
                labels: ["Won Opportunities", "Lost Opportunities"],
                datasets: [{
                    data: [2, 1],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: "65%",
                plugins: {
                    legend: {
                        position: "bottom"
                    }
                }
            }
        });
    }
});