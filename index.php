<?php
include 'config.php';

$totalAccounts = $conn->query("SELECT COUNT(*) AS total FROM accounts")->fetch_assoc()['total'] ?? 0;
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'] ?? 0;
$wonDeals = $conn->query("SELECT COUNT(*) AS total FROM sales_pipeline WHERE deal_stage = 'Won'")->fetch_assoc()['total'] ?? 0;
$lostDeals = $conn->query("SELECT COUNT(*) AS total FROM sales_pipeline WHERE deal_stage = 'Lost'")->fetch_assoc()['total'] ?? 0;
$totalRevenue = $conn->query("SELECT SUM(revenue) AS total FROM accounts")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRM Sales Data Warehouse</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<aside class="sidebar">
    <div class="brand">
        <h2>CRM Warehouse</h2>
        <p>Assessment 3</p>
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

<main class="main">
    <header class="topbar">
        <div>
            <h1>CRM Sales Data Warehouse Dashboard</h1>
            <p>Web-based CRM reporting and analysis system for computer hardware sales.</p>
        </div>
        <span class="status">MySQL Warehouse Connected</span>
    </header>

    <section class="cards">
        <div class="card">
            <p>Total Accounts</p>
            <h2><?php echo $totalAccounts; ?></h2>
        </div>
        <div class="card">
            <p>Total Products</p>
            <h2><?php echo $totalProducts; ?></h2>
        </div>
        <div class="card">
            <p>Won Opportunities</p>
            <h2><?php echo $wonDeals; ?></h2>
        </div>
        <div class="card">
            <p>Lost Opportunities</p>
            <h2><?php echo $lostDeals; ?></h2>
        </div>
    </section>

    <section class="panel">
        <h2>Total Account Revenue</h2>
        <p class="muted">Combined annual revenue from all accounts in the warehouse.</p>
        <h1 style="margin-top:15px;">$<?php echo number_format($totalRevenue, 2); ?></h1>
    </section>

    <section class="panel">
        <h2>Required Reports & Analysis</h2>
        <p class="muted">Select a function to run through the PHP query engine.</p>

        <div class="actions" style="margin-top:18px;">
            <a href="queryEngine.php?report=accounts">Accounts Report</a>
            <a href="queryEngine.php?report=products">Products Report</a>
            <a href="queryEngine.php?report=opportunities">Sales Opportunities Report</a>
            <a href="queryEngine.php?report=revenue_analysis">Establishment Year Revenue Analysis</a>
            <a href="queryEngine.php?report=sales_analysis">Sales Opportunity Analysis</a>
        </div>
    </section>

    <section class="panel">
        <h2>System Architecture</h2>
        <p class="muted">
            CSV/XML Dataset → Java ETL Process → MySQL Data Warehouse → PHP Query Engine → Web Dashboard
        </p>
    </section>
</main>

</body>
</html>

<?php $conn->close(); ?>