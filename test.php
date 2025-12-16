<!DOCTYPE html>
<html>
<head>
    <title>Contoh Highcharts - Drilldown dengan Sinkronisasi xAxis dari Database</title>
    <!-- Sisipkan referensi ke Highcharts di sini -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>
<body>
    <div id="chartContainer" style="width: 600px; height: 400px;"></div>

    <script>
        // Mendefinisikan data xAxis dan drilldown
        var mainData = [
            <?php
            include 'koneksi.php';
            
            $mainQuery = "SELECT * FROM satahun";
            $mainResult = $conn->query($mainQuery);
            if ($mainResult->num_rows > 0) {
                while ($row = $mainResult->fetch_assoc()) {
                    echo "{ 
                        name: '" . $row['tahun'] . "',
                        x: " . floatval($row['tahun']) . ",
                        persen: " . $row['PersenTahun'] . ",
                        y: " . floatval($row['Sales_Amount']) . ",
                        drilldown: '" . $row['tahun'] . "'
                    },";
                }
            }
            $conn->close();
            ?>
        ];

        var drilldownData = [
            <?php
            $drilldownQuery = "SELECT * FROM sabulan";
            $drilldownResult = $conn->query($drilldownQuery);
            $drilldownData = array();
            if ($drilldownResult->num_rows > 0) {
                while ($row = $drilldownResult->fetch_assoc()) {
                    $tahun = $row['tahun'];
                    if (!isset($drilldownData[$tahun])) {
                        $drilldownData[$tahun] = array(
                            'name' => $tahun,
                            'id' => $tahun,
                            'data' => array()
                        );
                    }
                    $drilldownData[$tahun]['data'][] = array(
                        $row['nama_bulan'],
                        floatval($row['Sales_Amount']),
                        $row['bulan']
                    );
                }
            }
            foreach ($drilldownData as $drilldown) {
                echo "{ 
                    id: '" . $drilldown['id'] . "',
                    name: '" . $drilldown['name'] . "',
                    data: " . json_encode($drilldown['data']) . "
                },";
            }
            ?>
        ];

        // Membuat grafik menggunakan data dari database
        var chart = Highcharts.chart('chartContainer', {
            chart: {
                type: 'bar',
                events: {
                    drilldown: function (e) {
                        var series = this.series[0];
                        var drilldownData = getDrilldownData(e.point.drilldown);
                        this.addSeriesAsDrilldown(e.point, drilldownData);
                        this.setTitle({ text: e.point.category }, { text: e.point.name });
                    },
                    drillup: function (e) {
                        this.setTitle({ text: 'Grafik dengan Drilldown dan Sinkronisasi xAxis dari Database' });
                    }
                }
            },
            title: {
                text: 'Grafik dengan Drilldown dan Sinkronisasi xAxis dari Database'
            },
            xAxis: {
                type: 'category',
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Arial, sans-serif'
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'Sales Amount'
                }
            },
            series: [{
                name: 'Tahun',
                colorByPoint: true,
                data: mainData
            }]
        });

        function getDrilldownData(id) {
            for (var i = 0; i < drilldownData.length; i++) {
                if (drilldownData[i].id === id) {
                    return drilldownData[i];
                }
            }
            return null;
        }
    </script>
</body>
</html>
