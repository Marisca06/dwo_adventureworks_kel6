<?php
require_once 'koneksi.php';

/* ===============================
   QUERY TOP 10 CUSTOMER 2005–2008
=============================== */
$sql = "
SELECT 
    c.CustomerKey,
    c.FullName AS CustomerName,
    SUM(f.SalesAmount) AS TotalSpent,
    c.CountryRegion
FROM factsales f
JOIN dimcustomer c ON f.CustomerKey = c.CustomerKey
JOIN dimtime t ON f.TimeKey = t.TimeKey
WHERE t.Year BETWEEN 2005 AND 2008
GROUP BY c.CustomerKey, c.FullName, c.CountryRegion
ORDER BY TotalSpent DESC
LIMIT 10
";
$query = mysqli_query($mysqli, $sql);

$customers = [];
$totalSpent = [];
$customerKeys = [];
while ($row = mysqli_fetch_assoc($query)) {
    $customers[] = $row['CustomerName'];
    $totalSpent[] = (float)$row['TotalSpent'];
    $customerKeys[] = (int)$row['CustomerKey'];
}

/* ===============================
   KPI DATA
=============================== */
$qTotalCustomer = mysqli_query($mysqli, "SELECT COUNT(DISTINCT CustomerKey) total FROM factsales");
$totalCustomer = mysqli_fetch_assoc($qTotalCustomer)['total'];

$qTotalPurchase = mysqli_query($mysqli, "SELECT SUM(SalesAmount) total FROM factsales");
$totalPurchase = mysqli_fetch_assoc($qTotalPurchase)['total'];

$avgPurchase = $totalPurchase / $totalCustomer;

/* ===============================
   Pie Chart Data: Customer per Country
=============================== */
$sqlPieCountry = "
SELECT CountryRegion, COUNT(CustomerKey) AS CountCustomer
FROM dimcustomer
GROUP BY CountryRegion
";
$queryPieCountry = mysqli_query($mysqli, $sqlPieCountry);
$pieCountryData = [];
while ($row = mysqli_fetch_assoc($queryPieCountry)) {
    $pieCountryData[] = [
        'name' => $row['CountryRegion'],
        'y' => (int)$row['CountCustomer']
    ];
}

/* ===============================
   LINE CHART: Tren Pembelian per Tahun
=============================== */
$sqlLine = "
SELECT LEFT(t.TimeKey,4) AS Year, SUM(f.SalesAmount) AS Total
FROM factsales f
JOIN dimtime t ON f.TimeKey = t.TimeKey
GROUP BY Year
ORDER BY Year
";
$queryLine = mysqli_query($mysqli, $sqlLine);
$lineYears = [];
$lineValues = [];
while ($row = mysqli_fetch_assoc($queryLine)) {
    $lineYears[] = $row['Year'];
    $lineValues[] = (float)$row['Total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Dashboard Customer</title>
<link href="css/sb-admin-2.min.css" rel="stylesheet">
<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
</head>
<body id="page-top">
<div id="wrapper">

<!-- SIDEBAR -->
<?php include 'sidebar.php'; ?>

<!-- CONTENT -->
<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<!-- TOPBAR -->
<?php include 'topbar.php'; ?>

<div class="container-fluid">

<div class="row mb-4">
    <div class="col-xl-12 col-md-12">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters">
                    <div class="col-12">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Customer Analysis Dashboard
                        </div>
                        <p class="mb-0 text-gray-800">
                            Customer Analysis Dashboard digunakan untuk menyajikan analisis perilaku dan kontribusi 
                            pelanggan terhadap penjualan berdasarkan total pembelian, tren pembelian per periode, 
                            serta distribusi pelanggan berdasarkan wilayah guna mendukung 
                            evaluasi loyalitas dan strategi pemasaran.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= KPI ================= -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Customer</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($totalCustomer) ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Purchases</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($totalPurchase,0,",",".") ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Purchase</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($avgPurchase,0,",",".") ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= TOP 10 CUSTOMER ================= -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5>Top 10 Customer</h5>
            <button id="btnReset" class="btn btn-sm btn-secondary">Reset Chart</button>
        </div>
        <div id="topCustomerChart" style="height:400px;"></div>
    </div>
</div>

<!-- ================= LINE & PIE CHART ================= -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5>Purchase Trends per Year</h5>
                <div id="lineChart" style="height:400px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5>Customer Distribution per Country</h5>
                <div id="pieCountryChart" style="height:400px;"></div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>
</div>

<script>
const customers = <?= json_encode($customers) ?>;
const values = <?= json_encode($totalSpent) ?>;
const keys = <?= json_encode($customerKeys) ?>;
const lineYears = <?= json_encode($lineYears) ?>;
const lineValues = <?= json_encode($lineValues) ?>;
const pieData = <?= json_encode($pieCountryData) ?>;

let topChart;

// ==== Top 10 Customer Chart with Drilldown & Reset ====
function loadTopChart() {
    topChart = Highcharts.chart('topCustomerChart', {
        chart: { type: 'column' },
        title: { text: 'Top 10 Customer (2005–2008)' },
        xAxis: { categories: customers },
        yAxis: { title: { text: 'Total Pembelian' } },
        tooltip: { pointFormat: '<b>{point.y:,.0f}</b>' },
        series: [{
            name: 'Total Pembelian',
            data: values,
            cursor: 'pointer',
            point: {
                events: {
                    click: function() {
                        drillDown(keys[this.index], customers[this.index]);
                    }
                }
            }
        }]
    });
}

// Drilldown bulanan
function drillDown(customerKey, customerName) {
    fetch(`drill_customer.php?customerKey=${customerKey}`)
        .then(res => res.json())
        .then(data => {
            topChart.update({
                title: { text: 'Pembelian Bulanan: ' + customerName },
                xAxis: { categories: data.labels },
                series: [{ name: 'Total Pembelian', data: data.values }]
            });
        });
}

// Tombol Reset chart
document.getElementById('btnReset').onclick = loadTopChart;

// Load chart awal
loadTopChart();

// ==== Line Chart ====
Highcharts.chart('lineChart', {
    chart: { type: 'line' },
    title: { text: null },
    xAxis: { categories: lineYears },
    yAxis: { title: { text: 'Total Pembelian' } },
    tooltip: { pointFormat: '<b>{point.y:,.0f}</b>' },
    series: [{ name: 'Total Pembelian', data: lineValues }],
    exporting: { enabled: true }
});

// ==== Pie Chart ====
Highcharts.chart('pieCountryChart', {
    chart: { type: 'pie' },
    title: { text: null },
    tooltip: { pointFormat: '{series.name}: <b>{point.y}</b>' },
    series: [{
        name: 'Customer',
        colorByPoint: true,
        data: pieData
    }],
    exporting: { enabled: true }
});
</script>

</body>
</html>
