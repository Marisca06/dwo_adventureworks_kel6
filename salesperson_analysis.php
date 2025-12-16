<?php
require_once 'koneksi.php';

/* ===============================
   QUERY TOP 10 SALESPERSON
   =============================== */
$sql = "
SELECT 
    sp.SalesPersonKey,
    sp.SalesPersonName,
    SUM(f.SalesAmount) AS TotalRevenue
FROM factsales f
JOIN dimsalesperson sp 
    ON f.SalesPersonKey = sp.SalesPersonKey
GROUP BY sp.SalesPersonKey, sp.SalesPersonName
ORDER BY TotalRevenue DESC
LIMIT 10
";
$query = mysqli_query($mysqli, $sql);

$salespersons = [];
$revenues     = [];
$keys         = [];

while ($row = mysqli_fetch_assoc($query)) {
    $salespersons[] = $row['SalesPersonName'];
    $revenues[]     = (float)$row['TotalRevenue'];
    $keys[]         = (int)$row['SalesPersonKey'];
}

/* ===============================
   QUERY TREN PENJUALAN PER TAHUN
   =============================== */
$sqlTrend = "
SELECT 
    sp.SalesPersonName,
    LEFT(f.TimeKey,4) AS Year,
    SUM(f.SalesAmount) AS Revenue
FROM factsales f
JOIN dimsalesperson sp ON f.SalesPersonKey = sp.SalesPersonKey
GROUP BY sp.SalesPersonName, Year
ORDER BY Year ASC

";
$queryTrend = mysqli_query($mysqli, $sqlTrend);

$trendDataRaw = [];
$years = [];
while ($row = mysqli_fetch_assoc($queryTrend)) {
    $trendDataRaw[] = $row;
    if (!in_array($row['Year'], $years)) $years[] = $row['Year'];
}

// Series per salesperson
$trendSeries = [];
foreach ($salespersons as $sp) {
    $data = [];
    foreach ($years as $y) {
        $found = false;
        foreach ($trendDataRaw as $row) {
            if ($row['SalesPersonName'] == $sp && $row['Year'] == $y) {
                $data[] = (float)$row['Revenue'];
                $found = true;
                break;
            }
        }
        if (!$found) $data[] = 0;
    }
    $trendSeries[] = ['name' => $sp, 'data' => $data];
}

/* ===============================
   PIE CHART REVENUE PER SALESPERSON
   =============================== */
$sqlPieRevenue = "
SELECT 
    sp.SalesPersonName,
    SUM(f.SalesAmount) AS Revenue
FROM factsales f
JOIN dimsalesperson sp ON f.SalesPersonKey = sp.SalesPersonKey
GROUP BY sp.SalesPersonName
ORDER BY Revenue DESC
LIMIT 10
";
$queryPieRevenue = mysqli_query($mysqli, $sqlPieRevenue);
$pieRevenueData = [];
while ($row = mysqli_fetch_assoc($queryPieRevenue)) {
    $pieRevenueData[] = [
        'name' => $row['SalesPersonName'],
        'y' => (float)$row['Revenue']
    ];
}

/* ===============================
   PIE CHART JUMLAH SALESPERSON PER GROUP
   =============================== */
$sqlPieGroup = "
SELECT 
    t.GroupName,
    COUNT(sp.SalesPersonKey) AS CountSP
FROM dimsalesperson sp
JOIN dimterritory t ON sp.TerritoryID = t.TerritoryID
GROUP BY t.GroupName
";
$queryPieGroup = mysqli_query($mysqli, $sqlPieGroup);
$pieGroupData = [];
while ($row = mysqli_fetch_assoc($queryPieGroup)) {
    $pieGroupData[] = [
        'name' => $row['GroupName'],
        'y' => (int)$row['CountSP']
    ];
}

// Data untuk mapping group → salespersons
$groupMapping = $mysqli->query("SELECT sp.SalesPersonName, t.GroupName FROM dimsalesperson sp JOIN dimterritory t ON sp.TerritoryID = t.TerritoryID")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>AdventureWorks - Salesperson Performance</title>
<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="css/sb-admin-2.min.css" rel="stylesheet">

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
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
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters">
                    <div class="col-12">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Salesperson Performance Dashboard
                        </div>
                        <p class="mb-0 text-gray-800">
                            Salesperson Performance Dashboard digunakan untuk menyajikan analisis kinerja tenaga 
                            penjualan berdasarkan total revenue, tren penjualan tahunan, persentase kontribusi revenue, 
                            serta distribusi salesperson berdasarkan kelompok penjualan. 
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= KPI SALESPERSON ================= -->
<div class="row">

    <!-- TOP SALESPERSON REVENUE -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Top Salesperson Revenue 
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $q = mysqli_query($mysqli,"
                                SELECT sp.SalesPersonName, SUM(f.SalesAmount) total 
                                FROM factsales f 
                                JOIN dimsalesperson sp ON f.SalesPersonKey = sp.SalesPersonKey
                                GROUP BY sp.SalesPersonName
                                ORDER BY total DESC
                                LIMIT 1
                            ");
                            $row = mysqli_fetch_assoc($q);
                            echo number_format($row['total'],0,",",".") . " ({$row['SalesPersonName']})";
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOTAL ACTIVE SALESPERSON -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Active Salesperson
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $q = mysqli_query($mysqli,
                                "SELECT COUNT(DISTINCT SalesPersonKey) total FROM factsales");
                            echo number_format(mysqli_fetch_assoc($q)['total']);
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AVERAGE SALES PER SALESPERSON -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Average Sales per Salesperson
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $q = mysqli_query($mysqli,"
                                SELECT ROUND(SUM(f.SalesAmount)/COUNT(DISTINCT f.SalesPersonKey)) avgSales
                                FROM factsales f
                            ");
                            echo number_format(mysqli_fetch_assoc($q)['avgSales'],0,",",".");
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- ================= END KPI SALESPERSON ================= -->


<button id="btnReset" class="btn btn-sm btn-secondary mb-3">Reset Filter</button>

<!-- BAR CHART -->
<div class="card shadow mb-4">
    <div class="card-body">
        <h5>Top 10 Salesperson by Total Revenue</h5>
        <div id="barChart"></div>
    </div>
</div>

<!-- LINE CHART -->
<div class="card shadow mb-4">
    <div class="card-body">
        <h5>Sales Trends per Year</h5>
        <div id="lineChart"></div>
    </div>
</div>

<!-- ROW 2 PIE CHARTS -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5>Revenue Percentage per Salesperson</h5>
                <div id="pieRevenueChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5>Distribution of Salesperson per Group </h5>
                <div id="pieGroupChart"></div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>

<script>
let salespersons = <?= json_encode($salespersons) ?>;
let revenues = <?= json_encode($revenues) ?>;
let keys = <?= json_encode($keys) ?>;
let years = <?= json_encode($years) ?>;
let trendSeries = <?= json_encode($trendSeries) ?>;
let pieRevenueData = <?= json_encode($pieRevenueData) ?>;
let pieGroupData = <?= json_encode($pieGroupData) ?>;
let groupMapping = <?= json_encode($groupMapping) ?>;

// Simpan data asli untuk reset
let originalTrendSeries = JSON.parse(JSON.stringify(trendSeries));
let originalBarCategories = [...salespersons];
let originalBarData = [...revenues];
let originalPieRevenueData = JSON.parse(JSON.stringify(pieRevenueData));

let barChart, lineChart, pieRevenueChart, pieGroupChart;

function initCharts() {

    // BAR CHART
    barChart = Highcharts.chart('barChart', {
        chart: { type: 'bar' },
        title: { text: 'Top 10 Salesperson by Total Revenue' },
        xAxis: { categories: salespersons, title: { text: 'Salesperson' } },
        yAxis: { title: { text: 'Total Revenue' } },
        tooltip: { pointFormat: '<b>{point.y:,.0f}</b>' },
        series: [{ name: 'Total Revenue', data: revenues }],
        exporting: { enabled: true },
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function() { filterChartsBySalesperson(this.category); }
                    }
                }
            }
        }
    });

    // LINE CHART
    lineChart = Highcharts.chart('lineChart', {
        chart: { type: 'line' },
        title: { text: 'Sales Trends per Year' },
        xAxis: { categories: years },
        yAxis: { title: { text: 'Total Revenue' } },
        tooltip: { shared: true, valueDecimals: 0 },
        series: trendSeries,
        exporting: { enabled: true }
    });

    // PIE REVENUE
    pieRevenueChart = Highcharts.chart('pieRevenueChart', {
        chart: { type: 'pie' },
        title: { text: null },
        tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f} %' },
                point: { events: { click: function() { filterChartsBySalesperson(this.name); } } }
            }
        },
        series: [{ name: 'Revenue', colorByPoint: true, data: pieRevenueData }],
        exporting: { enabled: true }
    });

    // PIE GROUP
    pieGroupChart = Highcharts.chart('pieGroupChart', {
        chart: { type: 'pie' },
        title: { text: null },
        tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f} %' },
                point: { events: { click: function() { filterChartsByGroup(this.name); } } }
            }
        },
        series: [{ name: 'Salesperson Count', colorByPoint: true, data: pieGroupData }],
        exporting: { enabled: true }
    });
}

// FILTER BY SALESPERSON
function filterChartsBySalesperson(name) {
    // Hanya ambil series salesperson yang dipilih
    let filteredSeries = trendSeries.filter(s => s.name === name);

    // Reset semua series di lineChart
    while(lineChart.series.length > 0) {
        lineChart.series[0].remove(false);
    }

    // Tambahkan series yang sesuai
    filteredSeries.forEach(s => lineChart.addSeries(s, false));
    lineChart.redraw();

    // PIE REVENUE: hanya yang dipilih
    let filteredPie = pieRevenueData.filter(p => p.name === name);
    pieRevenueChart.series[0].setData(filteredPie, true);

    // BAR CHART: hanya nama yang dipilih
    let idx = salespersons.indexOf(name);
    barChart.xAxis[0].setCategories([salespersons[idx]]);
    barChart.series[0].setData([revenues[idx]]);
}


// FILTER BY GROUP
function filterChartsByGroup(groupName) {
    let spNames = groupMapping.filter(sp => sp.GroupName === groupName).map(sp => sp.SalesPersonName);

    // LINE CHART
    lineChart.update({ series: [] });
    trendSeries.filter(s => spNames.includes(s.name)).forEach(s => lineChart.addSeries(s, false));
    lineChart.redraw();

    // PIE REVENUE
    let filteredPie = pieRevenueData.filter(p => spNames.includes(p.name));
    pieRevenueChart.series[0].setData(filteredPie, true);

    // BAR CHART
    let filteredRevenues = [];
    let filteredNames = [];
    spNames.forEach(name => {
        let idx = salespersons.indexOf(name);
        if (idx > -1) {
            filteredRevenues.push(revenues[idx]);
            filteredNames.push(name);
        }
    });
    barChart.xAxis[0].setCategories(filteredNames);
    barChart.series[0].setData(filteredRevenues);
}

// RESET FILTER
document.getElementById('btnReset').addEventListener('click', () => {
    // LINE CHART: reset hanya top 10 salesperson
    while(lineChart.series.length > 0) {
        lineChart.series[0].remove(false);
    }
    trendSeries
        .filter(s => salespersons.includes(s.name)) // <-- hanya top 10
        .forEach(s => lineChart.addSeries(s, false));
    lineChart.redraw();

    // PIE REVENUE: reset semua top 10
    pieRevenueChart.series[0].setData(originalPieRevenueData, true);

    // BAR CHART: reset top 10
    barChart.xAxis[0].setCategories(originalBarCategories);
    barChart.series[0].setData(originalBarData);
});


initCharts();
</script>

</body>
</html>
