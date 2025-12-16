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
<title>AdventureWorks - OLAP Dashboard</title>
<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="css/sb-admin-2.min.css" rel="stylesheet">
<script src="vendor/chart.js/Chart.min.js"></script>
<style>
    /* Styling iframe OLAP agar pas di content SB Admin 2 */
    #olapFrame {
        width: 100%;
        height: 600px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        margin-bottom: 30px;
    }
    a.menu-olap {
        display: block;
        padding: 12px 16px;
        margin-bottom: 8px;
        background: #1e3a8a;
        color: #fff;
        text-decoration: none;
        border-radius: 6px;
        transition: 0.3s;
    }
    a.menu-olap:hover {
        background: #2563eb;
    }
    
</style>
<script>
    function loadOLAP(query) {
        document.getElementById("olapFrame").src = "http://localhost:8080/mondrian/testpage.jsp?query=" + query;
    }
</script>
</head>
<body id="page-top">

<div id="wrapper">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Topbar -->
            <?php include 'topbar.php'; ?>

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">OLAP Dashboard - SalesCube</h1>

                <!-- Menu OLAP -->
                <div class="mb-3">
                    <a class="menu-olap" onclick="loadOLAP('top_salesperson')">Salesperson dengan Performa Penjualan Tertinggi</a>
                    <a class="menu-olap" onclick="loadOLAP('top_customer_2005')">Top 10 Customer Berdasarkan Total Pembelian</a>
                    <a class="menu-olap" onclick="loadOLAP('top_territory')">Wilayah / Country dengan Revenue Tertinggi</a>
                    <a class="menu-olap" onclick="loadOLAP('top_product_revenue')">Top 10 Produk dengan Revenue Terbesar</a>
                    <a class="menu-olap" onclick="loadOLAP('top_product_qty')">Produk Terlaris Berdasarkan Jumlah Penjualan</a>
                </div>

                <!-- Iframe OLAP -->
                <iframe id="olapFrame" src="http://localhost:8080/mondrian/testpage.jsp?query=top_salesperson"></iframe>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Content -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
