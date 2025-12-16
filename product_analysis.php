<?php
require_once 'koneksi.php';

// Ambil warna dari filter (default = semua)
$filterColor = isset($_GET['color']) ? $_GET['color'] : '';

// Filter query tambahan
$colorFilter = $filterColor ? "WHERE p.Color = '$filterColor'" : "";

// ================= KPI VALUES =================
// Total Products
$qTotalProducts = mysqli_query($mysqli, 
    $filterColor 
        ? "SELECT COUNT(DISTINCT ProductKey) AS total FROM dimproduct WHERE Color='$filterColor'"
        : "SELECT COUNT(DISTINCT ProductKey) AS total FROM dimproduct"
);
$totalProducts = mysqli_fetch_assoc($qTotalProducts)['total'] ?? 0;

// Total Profit
$qTotalProfit = mysqli_query($mysqli,
    "SELECT SUM(f.SalesAmount - p.StandardCost*f.OrderQty) AS total
     FROM factsales f 
     JOIN dimproduct p ON f.ProductKey=p.ProductKey
     ".($filterColor ? "WHERE p.Color='$filterColor'" : "")
);
$totalProfit = mysqli_fetch_assoc($qTotalProfit)['total'] ?? 0;

// Total Revenue
$qTotalRevenue = mysqli_query($mysqli,
    "SELECT SUM(f.SalesAmount) AS total
     FROM factsales f 
     JOIN dimproduct p ON f.ProductKey=p.ProductKey
     ".($filterColor ? "WHERE p.Color='$filterColor'" : "")
);
$totalRevenue = mysqli_fetch_assoc($qTotalRevenue)['total'] ?? 0;

// Total Quantity
$qTotalQty = mysqli_query($mysqli,
    "SELECT SUM(f.OrderQty) AS total
     FROM factsales f 
     JOIN dimproduct p ON f.ProductKey=p.ProductKey
     ".($filterColor ? "WHERE p.Color='$filterColor'" : "")
);
$totalQty = mysqli_fetch_assoc($qTotalQty)['total'] ?? 0;

// ================= TOP 10 PRODUCT BY REVENUE =================
$sqlRevenue = "
SELECT p.ProductName, SUM(f.SalesAmount) AS TotalRevenue
FROM factsales f
JOIN dimproduct p ON f.ProductKey = p.ProductKey
$colorFilter
GROUP BY p.ProductName
ORDER BY TotalRevenue DESC
LIMIT 10
";
$queryRevenue = mysqli_query($mysqli, $sqlRevenue);
$products = []; $revenues = [];
while ($row = mysqli_fetch_assoc($queryRevenue)) {
    $products[] = $row['ProductName'];
    $revenues[] = (float)$row['TotalRevenue'];
}

// ================= TOP 10 PRODUCT BY QUANTITY =================
$sqlQty = "
SELECT p.ProductName, SUM(f.OrderQty) AS TotalQty
FROM factsales f
JOIN dimproduct p ON f.ProductKey = p.ProductKey
$colorFilter
GROUP BY p.ProductName
ORDER BY TotalQty DESC
LIMIT 10
";
$queryQty = mysqli_query($mysqli, $sqlQty);
$productsQty = []; $qtys = [];
while ($row = mysqli_fetch_assoc($queryQty)) {
    $productsQty[] = $row['ProductName'];
    $qtys[] = (int)$row['TotalQty'];
}

// ================= TOP 10 PRODUCT BY PROFIT =================
$sqlProfit = "
SELECT p.ProductName, SUM(f.SalesAmount - p.StandardCost * f.OrderQty) AS Profit
FROM factsales f
JOIN dimproduct p ON f.ProductKey = p.ProductKey
$colorFilter
GROUP BY p.ProductName
ORDER BY Profit DESC
LIMIT 10
";
$queryProfit = mysqli_query($mysqli, $sqlProfit);
$productsProfit = []; $profits = [];
while ($row = mysqli_fetch_assoc($queryProfit)) {
    $productsProfit[] = $row['ProductName'];
    $profits[] = (float)$row['Profit'];
}

// ================= AMBIL WARNA UNTUK FILTER =================
$qColors = mysqli_query($mysqli, "SELECT DISTINCT Color FROM dimproduct ORDER BY Color");
$colors = [];
while ($row = mysqli_fetch_assoc($qColors)) {
    $colors[] = $row['Color'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Product Dashboard</title>
<link href="css/sb-admin-2.min.css" rel="stylesheet">
<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
</head>
<body id="page-top">
<div id="wrapper">
<?php include 'sidebar.php'; ?>
<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<?php include 'topbar.php'; ?>

<div class="container-fluid">

<div class="row mb-4">
    <div class="col-xl-12 col-md-12">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters">
                    <div class="col-12">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Product Analysis Dashboard
                        </div>
                        <p class="mb-0 text-gray-800">
                            Product Analysis Dashboard digunakan untuk menyajikan analisis produk berdasarkan 
                            Top 10 Produk dengan revenue tertinggi, Top 10 Produk dengan jumlah penjualan terbanyak, 
                            serta Top 10 Produk dengan profit tertinggi guna mengidentifikasi produk dengan kinerja 
                            terbaik.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= FILTER BY COLOR ================= -->
<form method="get" class="mb-4">
    <div class="row align-items-center g-3">
        <div class="col-auto">
            <label for="colorFilter" class="mb-2 mb-md-0 font-weight">Filter by Color:</label>
        </div>
        <div class="col-md-3 col-6">
            <select name="color" id="colorFilter" class="form-control">
                <option value="">All</option>
                <?php foreach ($colors as $color): ?>
                    <option value="<?= $color ?>" <?= $color==$filterColor?'selected':'' ?>><?= $color ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary px-4">Apply</button>
        </div>
    </div>
</form>

<!-- ================= KPI CARDS ================= -->
<div class="row mb-4">

    <!-- Total Products -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Products
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($totalProducts) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Revenue
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($totalRevenue,0,",",".") ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Profit -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Profit
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($totalProfit,0,",",".") ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Quantity -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Quantity
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($totalQty) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- ================= FULL-WIDTH CHARTS ================= -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-body">
                <h5>Top 10 Products by Revenue</h5>
                <div id="chartRevenue" style="height:400px;"></div>
            </div>
        </div>
    </div>
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-body">
                <h5>Top 10 Products by Quantity Sold</h5>
                <div id="chartQty" style="height:400px;"></div>
            </div>
        </div>
    </div>
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-body">
                <h5>Top 10 Products by Profit</h5>
                <div id="chartProfit" style="height:400px;"></div>
            </div>
        </div>
    </div>
</div>

</div> <!-- container-fluid -->
</div> <!-- content -->
</div> <!-- content-wrapper -->
</div> <!-- wrapper -->

<script>
// ====== CHART REVENUE ======
Highcharts.chart('chartRevenue', {
    chart: { type: 'column' },
    title: { text: 'Top 10 Products by Revenue' },
    xAxis: { categories: <?= json_encode($products) ?> },
    yAxis: { title: { text: 'Revenue' } },
    series: [{ name: 'Revenue', data: <?= json_encode($revenues) ?> }],
    exporting: { enabled: true }
});

// ====== CHART QUANTITY (BAR) ======
Highcharts.chart('chartQty', {
    chart: { type: 'bar' },
    title: { text: 'Top 10 Products by Quantity Sold' },
    xAxis: { categories: <?= json_encode($productsQty) ?> },
    yAxis: { title: { text: 'Quantity Sold' } },
    series: [{ name: 'Quantity', data: <?= json_encode($qtys) ?> }],
    exporting: { enabled: true }
});

// ====== CHART PROFIT (LINE) ======
Highcharts.chart('chartProfit', {
    chart: { type: 'line' },
    title: { text: 'Top 10 Products by Profit' },
    xAxis: { categories: <?= json_encode($productsProfit) ?> },
    yAxis: { title: { text: 'Profit' } },
    series: [{ name: 'Profit', data: <?= json_encode($profits) ?> }],
    exporting: { enabled: true }
});
</script>

</body>
</html>
