<?php
include 'config.php';

function cleanText($value)
{
    return trim((string)$value);
}

function cleanDate($value)
{
    $value = trim((string)$value);

    if ($value === '' || strtolower($value) === 'null') {
        return null;
    }

    return $value;
}

function cleanNumber($value)
{
    $value = trim((string)$value);

    if ($value === '' || strtolower($value) === 'null') {
        return null;
    }

    // Remove commas and currency symbols if present
    $value = str_replace([',', '$'], '', $value);

    return (float)$value;
}

function standardiseSector($sector)
{
    $sector = strtolower(trim((string)$sector));

    $map = [
        'technolgy' => 'Technology',
        'technology' => 'Technology',
        'software' => 'Software',
        'retail' => 'Retail',
        'medical' => 'Medical',
        'finance' => 'Finance',
        'marketing' => 'Marketing',
        'telecommunications' => 'Telecommunications',
        'services' => 'Services',
        'employment' => 'Employment',
        'entertainment' => 'Entertainment'
    ];

    return $map[$sector] ?? ucwords($sector);
}

function resetTables($conn)
{
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("TRUNCATE TABLE sales_pipeline");
    $conn->query("TRUNCATE TABLE sales_teams");
    $conn->query("TRUNCATE TABLE products");
    $conn->query("TRUNCATE TABLE accounts");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
}

function importAccounts($conn)
{
    $file = __DIR__ . '/data/accounts.xml';

    if (!file_exists($file)) {
        die('accounts.xml not found in data folder.');
    }

    $xml = simplexml_load_file($file);

    $stmt = $conn->prepare("
        INSERT INTO accounts
        (account, sector, year_established, revenue, employees, office_location, subsidiary_of)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $count = 0;

    // Supports common XML structures
    $records = $xml->account ?? $xml->row;

    foreach ($records as $row) {
        $account = cleanText($row->account);
        $sector = standardiseSector($row->sector);
        $year = (int)$row->year_established;
        $revenue = cleanNumber($row->revenue);
        $employees = (int)$row->employees;
        $office = cleanText($row->office_location);
        $subsidiary = isset($row->subsidiary_of) ? cleanText($row->subsidiary_of) : null;

        if ($account === '') {
            continue;
        }

        $stmt->bind_param(
            "ssidiis",
            $account,
            $sector,
            $year,
            $revenue,
            $employees,
            $office,
            $subsidiary
        );

        $stmt->execute();
        $count++;
    }

    return $count;
}

function importProducts($conn)
{
    $file = __DIR__ . '/data/products.csv';

    if (!file_exists($file)) {
        die('products.csv not found in data folder.');
    }

    $handle = fopen($file, 'r');
    fgetcsv($handle); // Skip header

    $stmt = $conn->prepare("
        INSERT INTO products
        (product, series, sales_price)
        VALUES (?, ?, ?)
    ");

    $count = 0;

    while (($row = fgetcsv($handle)) !== false) {
        $product = cleanText($row[0] ?? '');
        $series = cleanText($row[1] ?? '');
        $salesPrice = cleanNumber($row[2] ?? null);

        if ($product === '') {
            continue;
        }

        $stmt->bind_param("ssd", $product, $series, $salesPrice);
        $stmt->execute();
        $count++;
    }

    fclose($handle);

    return $count;
}

function importSalesTeams($conn)
{
    $file = __DIR__ . '/data/sales_teams.xml';

    if (!file_exists($file)) {
        die('sales_teams.xml not found in data folder.');
    }

    $xml = simplexml_load_file($file);

    $stmt = $conn->prepare("
        INSERT INTO sales_teams
        (sales_agent, manager, regional_office)
        VALUES (?, ?, ?)
    ");

    $count = 0;
    $records = $xml->row ?? $xml->sales_team;

    foreach ($records as $row) {
        $salesAgent = cleanText($row->sales_agent);
        $manager = cleanText($row->manager);
        $regionalOffice = cleanText($row->regional_office);

        if ($salesAgent === '') {
            continue;
        }

        $stmt->bind_param("sss", $salesAgent, $manager, $regionalOffice);
        $stmt->execute();
        $count++;
    }

    return $count;
}

function importSalesPipeline($conn)
{
    $file = __DIR__ . '/data/sales_pipeline.csv';

    if (!file_exists($file)) {
        die('sales_pipeline.csv not found in data folder.');
    }

    $handle = fopen($file, 'r');
    fgetcsv($handle); // Skip header

    $stmt = $conn->prepare("
        INSERT INTO sales_pipeline
        (opportunity_id, sales_agent, product, account, deal_stage, engage_date, close_date, close_value)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $count = 0;

    while (($row = fgetcsv($handle)) !== false) {
        $opportunityId = cleanText($row[0] ?? '');
        $salesAgent = cleanText($row[1] ?? '');
        $product = cleanText($row[2] ?? '');
        $account = cleanText($row[3] ?? '');
        $dealStage = cleanText($row[4] ?? '');
        $engageDate = cleanDate($row[5] ?? null);
        $closeDate = cleanDate($row[6] ?? null);
        $closeValue = cleanNumber($row[7] ?? null);

        if ($opportunityId === '') {
            continue;
        }

        $stmt->bind_param(
            "sssssssd",
            $opportunityId,
            $salesAgent,
            $product,
            $account,
            $dealStage,
            $engageDate,
            $closeDate,
            $closeValue
        );

        $stmt->execute();
        $count++;
    }

    fclose($handle);

    return $count;
}

// Execute import
resetTables($conn);

$accountsCount = importAccounts($conn);
$productsCount = importProducts($conn);
$salesTeamsCount = importSalesTeams($conn);
$salesPipelineCount = importSalesPipeline($conn);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Data Import Complete</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<main class="main" style="margin-left:0; max-width:1000px; margin:40px auto;">
    <section class="panel">
        <h1>CRM Data Import Complete</h1>
        <p class="muted">
            All CSV and XML records have been extracted, transformed, and loaded into MySQL.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Table</th>
                    <th>Records Imported</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>accounts</td>
                    <td><?php echo $accountsCount; ?></td>
                </tr>
                <tr>
                    <td>products</td>
                    <td><?php echo $productsCount; ?></td>
                </tr>
                <tr>
                    <td>sales_teams</td>
                    <td><?php echo $salesTeamsCount; ?></td>
                </tr>
                <tr>
                    <td>sales_pipeline</td>
                    <td><?php echo $salesPipelineCount; ?></td>
                </tr>
            </tbody>
        </table>

        <div class="actions" style="margin-top:20px;">
            <a href="index.php">Open Dashboard</a>
            <a href="queryEngine.php?report=accounts">View Accounts Report</a>
            <a href="queryEngine.php?report=products">View Products Report</a>
            <a href="queryEngine.php?report=opportunities">View Opportunities Report</a>
        </div>
    </section>
</main>

</body>
</html>