<?php
include 'config.php';

// Dashboard KPI Queries
$totalAccountsResult = $conn->query("SELECT COUNT(*) AS total FROM accounts");
$totalProductsResult = $conn->query("SELECT COUNT(*) AS total FROM products");
$wonDealsResult = $conn->query("SELECT COUNT(*) AS total FROM sales_pipeline WHERE deal_stage = 'Won'");
$lostDealsResult = $conn->query("SELECT COUNT(*) AS total FROM sales_pipeline WHERE deal_stage = 'Lost'");
$totalRevenueResult = $conn->query("SELECT SUM(revenue) AS total FROM accounts");

// Extract values safely
$totalAccounts = ($totalAccountsResult && $totalAccountsResult->num_rows > 0)
    ? $totalAccountsResult->fetch_assoc()['total']
    : 0;

$totalProducts = ($totalProductsResult && $totalProductsResult->num_rows > 0)
    ? $totalProductsResult->fetch_assoc()['total']
    : 0;

$wonDeals = ($wonDealsResult && $wonDealsResult->num_rows > 0)
    ? $wonDealsResult->fetch_assoc()['total']
    : 0;

$lostDeals = ($lostDealsResult && $lostDealsResult->num_rows > 0)
    ? $lostDealsResult->fetch_assoc()['total']
    : 0;

$totalRevenue = ($totalRevenueResult && $totalRevenueResult->num_rows > 0)
    ? ($totalRevenueResult->fetch_assoc()['total'] ?? 0)
    : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Sales Data Warehouse Dashboard</title>

    <!-- Main Styles -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="brand">
        <h2>CRM Warehouse</h2>
        <p>Assessment 3 Dashboard</p>
    </div>

    <nav>
        <a class="active" href="index.php">Dashboard</a>
        <a href="queryEngine.php?report=accounts">Accounts Report</a>
        <a href="queryEngine.php?report=products">Products Report</a>
        <a href="queryEngine.php?report=opportunities">Sales Opportunities</a>
        <a href="queryEngine.php?report=revenue_analysis">Revenue Analysis</a>
        <a href="queryEngine.php?report=sales_analysis">Sales Analysis</a>
    </nav>
</aside>

<!-- Main Content -->
<main class="main">

    <!-- Header -->
    <header class="topbar">
        <div>
            <h1>CRM Sales Data Warehouse Dashboard</h1>
            <p>
                Web-based CRM reporting and analysis system for computer hardware sales.
            </p>
        </div>
        <span class="status">MySQL Warehouse Connected</span>
    </header>

    <!-- KPI Cards -->
    <section class="cards">
        <div class="card">
            <p>Total Accounts</p>
            <h2><?php echo number_format($totalAccounts); ?></h2>
        </div>

        <div class="card">
            <p>Total Products</p>
            <h2><?php echo number_format($totalProducts); ?></h2>
        </div>

        <div class="card">
            <p>Won Opportunities</p>
            <h2><?php echo number_format($wonDeals); ?></h2>
        </div>

        <div class="card">
            <p>Lost Opportunities</p>
            <h2><?php echo number_format($lostDeals); ?></h2>
        </div>
    </section>

    <!-- Revenue Summary -->
    <section class="panel">
        <h2>Total Account Revenue</h2>
        <p class="muted">
            Combined annual revenue from all accounts stored in the warehouse.
        </p>
        <h1 style="margin-top: 15px;">
            $<?php echo number_format((float)$totalRevenue, 2); ?>
        </h1>
    </section>

    <!-- Interactive Charts -->
    <section class="grid">
        <div class="panel">
            <h2>Revenue by Establishment Year</h2>
            <p class="muted">
                Visual analysis of annual revenue grouped by company establishment year.
            </p>
            <canvas id="revenueChart"></canvas>
        </div>

        <div class="panel">
            <h2>Opportunity Status Distribution</h2>
            <p class="muted">
                Comparison of won and lost opportunities.
            </p>
            <canvas id="statusChart"></canvas>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="panel">
        <h2>Required Reports & Analysis</h2>
        <p class="muted">
            Run the reports and analyses required by the assessment.
        </p>

        <div class="actions" style="margin-top: 18px;">
            <a href="queryEngine.php?report=accounts">Accounts Report</a>
            <a href="queryEngine.php?report=products">Products Report</a>
            <a href="queryEngine.php?report=opportunities">Sales Opportunities Report</a>
            <a href="queryEngine.php?report=revenue_analysis">Establishment Year Revenue Analysis</a>
            <a href="queryEngine.php?report=sales_analysis">Sales Opportunity Analysis</a>
        </div>
    </section>

    <!-- Architecture Section -->
    <section class="panel">
        <h2>System Architecture</h2>
        <p class="muted">
            CSV/XML Dataset → Java ETL Process → MySQL Data Warehouse → PHP Query Engine → Web Dashboard
        </p>
    </section>

</main>

<!-- Dashboard JavaScript -->
<script src="assets/js/dashboard.js"></script>

</body>
</html>

<?php
$conn->close();
?>