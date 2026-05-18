<?php
include 'config.php';

$report = $_GET['report'] ?? 'accounts';

$queries = [
    "accounts" => [
        "title" => "Accounts Report",
        "description" => "Displays all account information from the CRM data warehouse.",
        "sql" => "SELECT * FROM accounts"
    ],

    "products" => [
        "title" => "Products Report",
        "description" => "Displays all products including series and price.",
        "sql" => "SELECT * FROM products"
    ],

    "opportunities" => [
        "title" => "Sales Opportunities Report",
        "description" => "Displays won and lost sales opportunities with product and value details.",
        "sql" => "
            SELECT 
                opportunity_id,
                sales_agent,
                product,
                account,
                deal_stage,
                close_value,
                close_date
            FROM sales_pipeline
            WHERE deal_stage IN ('Won', 'Lost')
            ORDER BY close_date DESC
        "
    ],

    "revenue_analysis" => [
        "title" => "Establishment Year Revenue Analysis",
        "description" => "Displays total annual revenue grouped by company establishment year.",
        "sql" => "
            SELECT 
                year_established,
                SUM(revenue) AS total_revenue
            FROM accounts
            GROUP BY year_established
            ORDER BY year_established
        "
    ],

    "sales_analysis" => [
        "title" => "Sales Opportunity Analysis",
        "description" => "Displays average sales opportunity value segmented by product.",
        "sql" => "
            SELECT 
                product,
                ROUND(AVG(close_value), 2) AS average_opportunity_value
            FROM sales_pipeline
            WHERE close_value IS NOT NULL
            GROUP BY product
            ORDER BY average_opportunity_value DESC
        "
    ]
];

if (!array_key_exists($report, $queries)) {
    $report = "accounts";
}

$current = $queries[$report];
$result = $conn->query($current["sql"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($current["title"]); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<aside class="sidebar">
    <div class="brand">
        <h2>CRM Warehouse</h2>
        <p>PHP Query Engine</p>
    </div>

    <nav>
        <a href="index.php">Dashboard</a>
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
            <h1><?php echo htmlspecialchars($current["title"]); ?></h1>
            <p><?php echo htmlspecialchars($current["description"]); ?></p>
        </div>
        <span class="status">Query Engine Active</span>
    </header>

    <section class="panel">
        <h2>Selected Query</h2>
        <p class="muted">This SQL query is executed by the PHP query engine.</p>

        <pre style="background:#f1f5f9; padding:16px; border-radius:12px; overflow:auto; margin-top:15px;"><?php echo htmlspecialchars($current["sql"]); ?></pre>
    </section>

    <section class="panel">
        <h2>Query Results</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <?php while ($field = $result->fetch_field()): ?>
                            <th><?php echo htmlspecialchars($field->name); ?></th>
                        <?php endwhile; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <?php foreach ($row as $value): ?>
                                <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="muted">No records found, or the query could not be executed.</p>

            <?php if ($conn->error): ?>
                <p style="color:red; margin-top:10px;">
                    <?php echo htmlspecialchars($conn->error); ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

</body>
</html>

<?php
$conn->close();
?>