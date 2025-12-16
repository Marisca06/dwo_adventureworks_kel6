<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}


require_once 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AdventureWorks - Dashboard</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="vendor/chart.js/Chart.min.js"></script>
    <style>
        table th, table td { vertical-align: middle !important; }
        .card-header-custom {
            font-weight: bold;
            font-size: 1rem;
            color: #fff;
        }
    </style>
</head>
<body id="page-top">

<div id="wrapper">

<!-- ================= SIDEBAR ================= -->
<?php include 'sidebar.php'; ?>

<!-- ================= CONTENT ================= -->
<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<!-- ================= TOPBAR ================= -->
<?php include 'topbar.php'; ?>

<!-- ================= PAGE CONTENT ================= -->
<div class="container-fluid">

<div class="row mb-4">
    <div class="col-xl-12 col-md-12">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Dashboard AdventureWorks
                        </div>
                        <p class="mb-0 text-gray-800">
                            Dashboard ini menyajikan ringkasan performa penjualan AdventureWorks yang diolah dari Data Warehouse. Informasi ditampilkan dalam bentuk 
                            Key Performance Indicator (KPI), tabel analisis, dan visualisasi grafik untuk membantu memahami tren penjualan, kontribusi pelanggan, produk, 
                            wilayah, serta kinerja salesperson.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


    <!-- ================= KPI ROW ================= -->
    <div class="row">
        <?php
        $kpis = [
            ['title'=>'Total Customer', 'query'=>"SELECT COUNT(DISTINCT CustomerKey) total FROM factsales", 'icon'=>'fa-users', 'color'=>'primary'],
            ['title'=>'Total Product', 'query'=>"SELECT COUNT(ProductKey) total FROM dimproduct", 'icon'=>'fa-box', 'color'=>'success'],
            ['title'=>'Total Sales', 'query'=>"SELECT SUM(SalesAmount) total FROM factsales", 'icon'=>'fa-dollar-sign', 'color'=>'info'],
            ['title'=>'Total Transaction', 'query'=>"SELECT COUNT(DISTINCT SalesOrderID) total FROM factsales", 'icon'=>'fa-receipt', 'color'=>'warning'],
        ];
        foreach($kpis as $kpi){
            $q = mysqli_query($mysqli,$kpi['query']);
            $value = mysqli_fetch_assoc($q)['total'];
            echo '<div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-'.$kpi['color'].' shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-'.$kpi['color'].' text-uppercase mb-1">'.$kpi['title'].'</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">'.number_format($value,0,",",".").'</div>
                                </div>
                                <div class="col-auto"><i class="fas '.$kpi['icon'].' fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                  </div>';
        }
        ?>
    </div>
    <!-- ================= END KPI ================= -->

    <!-- ================= TOP 3 SALESPERSON & CUSTOMER ================= -->
    <div class="row">
        <?php
        $sql_salesperson = "
            SELECT sp.SalesPersonName, SUM(f.SalesAmount) AS TotalRevenue
            FROM factsales f
            JOIN dimsalesperson sp ON f.SalesPersonKey = sp.SalesPersonKey
            GROUP BY sp.SalesPersonName
            ORDER BY TotalRevenue DESC
            LIMIT 3
        ";
        $query_salesperson = mysqli_query($mysqli, $sql_salesperson);

        $sql_customer = "
            SELECT c.CustomerKey, c.FullName AS CustomerName, SUM(f.SalesAmount) AS TotalSpent
            FROM factsales f
            JOIN dimcustomer c ON f.CustomerKey = c.CustomerKey
            JOIN dimtime t ON f.TimeKey = t.TimeKey
            WHERE t.Year BETWEEN 2005 AND 2008
            GROUP BY c.CustomerKey, c.FullName
            ORDER BY TotalSpent DESC
            LIMIT 3
        ";
        $query_customer = mysqli_query($mysqli, $sql_customer);
        ?>

        <!-- Top 3 Salesperson -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary card-header-custom">
                    <i class="fas fa-user-tie"></i> Top 3 Salesperson Revenue
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Salesperson</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no=1;
                            while($row=mysqli_fetch_assoc($query_salesperson)){
                                echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['SalesPersonName']}</td>
                                        <td>".number_format($row['TotalRevenue'],0,",",".")."</td>
                                      </tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top 3 Customer -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success card-header-custom">
                    <i class="fas fa-users"></i> Top 3 Customer (2005–2008)
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Customer</th>
                                <th>Total Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no=1;
                            while($row=mysqli_fetch_assoc($query_customer)){
                                echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['CustomerName']}</td>
                                        <td>".number_format($row['TotalSpent'],0,",",".")."</td>
                                      </tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3 CHARTS SEJARAH ================= -->
    <div class="row">

        <?php
        // Top 3 Product by Revenue
        $sqlRevenue = "
            SELECT p.ProductName, SUM(f.SalesAmount) AS TotalRevenue
            FROM factsales f
            JOIN dimproduct p ON f.ProductKey = p.ProductKey
            GROUP BY p.ProductName
            ORDER BY TotalRevenue DESC
            LIMIT 3
        ";
        $queryRevenue = mysqli_query($mysqli, $sqlRevenue);
        $products=[]; $revenues=[];
        while($row=mysqli_fetch_assoc($queryRevenue)){
            $products[]=$row['ProductName'];
            $revenues[]=(float)$row['TotalRevenue'];
        }

        // Revenue per Region
        $sqlRegion = "
            SELECT t.GroupName, SUM(f.SalesAmount) AS TotalRevenue
            FROM factsales f
            JOIN dimterritory t ON f.TerritoryKey = t.TerritoryKey
            GROUP BY t.GroupName
            ORDER BY TotalRevenue DESC
        ";
        $queryRegion = mysqli_query($mysqli,$sqlRegion);
        $regions=[]; $regionRevenue=[];
        while($row=mysqli_fetch_assoc($queryRegion)){
            $regions[]=$row['GroupName'];
            $regionRevenue[]=(float)$row['TotalRevenue'];
        }

        // Top 3 Product by Quantity
        $sqlQty = "
            SELECT p.ProductName, SUM(f.OrderQty) AS TotalQty
            FROM factsales f
            JOIN dimproduct p ON f.ProductKey = p.ProductKey
            GROUP BY p.ProductName
            ORDER BY TotalQty DESC
            LIMIT 3
        ";
        $queryQty = mysqli_query($mysqli,$sqlQty);
        $productsQty=[]; $qtys=[];
        while($row=mysqli_fetch_assoc($queryQty)){
            $productsQty[]=$row['ProductName'];
            $qtys[]=(int)$row['TotalQty'];
        }
        ?>

        <!-- Chart 1: Top Product by Revenue -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white card-header-custom">
                    <i class="fas fa-chart-bar"></i> Top 3 Product by Revenue
                </div>
                <div class="card-body">
                    <canvas id="chartRevenue"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 2: Revenue per Region -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-warning text-white card-header-custom">
                    <i class="fas fa-chart-pie"></i> Revenue per Region
                </div>
                <div class="card-body">
                    <canvas id="chartRegion"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 3: Top Product by Quantity -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-danger text-white card-header-custom">
                    <i class="fas fa-boxes"></i> Top 3 Product by Quantity
                </div>
                <div class="card-body">
                    <canvas id="chartQty"></canvas>
                </div>
            </div>
        </div>

    </div>
    <!-- ================= END CHARTS ================= -->

</div>
<!-- /.container-fluid -->

</div>
</div>
</div>

<!-- ================= LOGOUT MODAL ================= -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Logout</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Yakin ingin logout?
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a class="btn btn-danger" href="login.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
<script src="vendor/chart.js/Chart.min.js"></script>
<script>
    const ctxRevenue = document.getElementById('chartRevenue').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($products); ?>,
            datasets: [{
                label: 'Revenue',
                data: <?php echo json_encode($revenues); ?>,
                backgroundColor: '#17a2b8'
            }]
        },
        options: { responsive:true, indexAxis: 'y', plugins:{legend:{display:false}} }
    });

    const ctxRegion = document.getElementById('chartRegion').getContext('2d');
    new Chart(ctxRegion, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($regions); ?>,
            datasets: [{
                data: <?php echo json_encode($regionRevenue); ?>,
                backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#6c757d']
            }]
        },
        options: { responsive:true }
    });

    const ctxQty = document.getElementById('chartQty').getContext('2d');
    new Chart(ctxQty, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($productsQty); ?>,
            datasets: [{
                label: 'Quantity',
                data: <?php echo json_encode($qtys); ?>,
                backgroundColor: '#dc3545'
            }]
        },
        options: { responsive:true, indexAxis: 'y', plugins:{legend:{display:false}} }
    });
</script>

</body>
</html>
