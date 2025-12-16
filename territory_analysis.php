<?php
require_once 'koneksi.php';

/* =============================
   KPI
============================= */
$kpiQuery = mysqli_query($mysqli,"
SELECT 
    COUNT(DISTINCT TerritoryKey) AS totalTerritory,
    COUNT(DISTINCT GroupName) AS totalRegion
FROM dimterritory
");
$kpi = mysqli_fetch_assoc($kpiQuery);

/* =============================
   BAR CHART DATA
   Revenue per Region & Territories
============================= */
// Ambil revenue per region
$sqlRegion = "
SELECT t.GroupName, SUM(f.SalesAmount) AS TotalRevenue
FROM factsales f
JOIN dimterritory t ON f.TerritoryKey = t.TerritoryKey
GROUP BY t.GroupName
ORDER BY TotalRevenue DESC
";
$queryRegion = mysqli_query($mysqli, $sqlRegion);
$regions = [];
$regionRevenue = [];
while($row = mysqli_fetch_assoc($queryRegion)){
    $regions[] = $row['GroupName'];
    $regionRevenue[] = (float)$row['TotalRevenue'];
}

// Ambil revenue per territory
$sqlTerritory = "
SELECT t.GroupName, t.Name AS TerritoryName, SUM(f.SalesAmount) AS TotalRevenue
FROM factsales f
JOIN dimterritory t ON f.TerritoryKey = t.TerritoryKey
GROUP BY t.GroupName, t.Name
";
$queryTerritory = mysqli_query($mysqli, $sqlTerritory);
$territoryData = [];
while($row = mysqli_fetch_assoc($queryTerritory)){
    $territoryData[$row['GroupName']][] = [
        'name'=>$row['TerritoryName'],
        'y'=>(float)$row['TotalRevenue']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Territory Revenue Dashboard</title>
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
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters">
                    <div class="col-12">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Territory Analysis Dashboard
                        </div>
                        <p class="mb-0 text-gray-800">
                            Territory Analysis Dashboard digunakan untuk menyajikan analisis revenue berdasarkan 
                            region atau wilayah penjualan guna mengetahui kontribusi masing-masing region terhadap 
                            total pendapatan perusahaan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI CARDS -->
<div class="row mb-4">

    <!-- Total Territory -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Territory
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $kpi['totalTerritory'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map-marked-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Region -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Region
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $kpi['totalRegion'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-globe-asia fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- BAR CHART -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5>Revenue per Region</h5>
            <button id="btnReset" class="btn btn-sm btn-secondary">Reset Chart</button>
        </div>
        <div id="barChart"></div>
    </div>
</div>

</div>
</div>
</div>
</div>

<script>
let regions = <?= json_encode($regions) ?>;
let regionRevenue = <?= json_encode($regionRevenue) ?>;
let territoryData = <?= json_encode($territoryData) ?>;

let barChart = Highcharts.chart('barChart',{
    chart:{type:'column'},
    title:{text:'Revenue per Region'},
    xAxis:{categories:regions, title:{text:'Region'}},
    yAxis:{title:{text:'Total Revenue'}},
    tooltip:{pointFormat:'<b>{point.y:,.0f}</b>'},
    series:[{name:'Revenue', data:regionRevenue}],
    exporting:{enabled:true},
    plotOptions:{
        series:{
            cursor:'pointer',
            point:{
                events:{
                    click:function(){
                        drillDown(this.category);
                    }
                }
            }
        }
    }
});

// Drilldown Bar Chart: Region -> Territories
function drillDown(region){
    let data = territoryData[region];
    if(!data) return;

    barChart.update({
        title:{text:'Revenue per Territory in '+region},
        xAxis:{categories:data.map(d=>d.name)},
        series:[{name:'Revenue', data:data.map(d=>d.y)}]
    });
}

// Tombol Reset
document.getElementById('btnReset').onclick = function(){
    barChart.update({
        title:{text:'Revenue per Region'},
        xAxis:{categories:regions},
        series:[{name:'Revenue', data:regionRevenue}]
    });
};
</script>

</body>
</html>
