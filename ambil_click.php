<?php
include 'conn.php';

$mainQuery = "SELECT * FROM click";
$mainResult = $conn->query($mainQuery);
$mainData = array();

if ($mainResult->num_rows > 0) {
    while ($row = $mainResult->fetch_assoc()) {
        $mainData[] = array(
            'name' => $row['nama_konten'],
            'sales' => floatval($row['jumlah_click']),
            'y' => floatval($row['jumlah_click']),
            'drilldown' => $row['nama_konten'] ,
            $nama_konten[] = $row['nama_konten'],
            $jumlah_click[] = $row['jumlah_click']
        );
    }
}


// Menutup koneksi ke database
$conn->close();
echo "var mainChartDataC = " . json_encode($mainData) . ";";
?>