<?php
require_once 'koneksi.php';

$customerKey = $_GET['customerKey'];

$sql = "
SELECT 
    t.Year,
    SUM(f.SalesAmount) AS TotalSpent
FROM factsales f
JOIN dimtime t ON f.TimeKey = t.TimeKey
WHERE f.CustomerKey = ?
AND t.Year BETWEEN 2005 AND 2008
GROUP BY t.Year
ORDER BY t.Year
";

$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "i", $customerKey);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$labels = [];
$values = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = 'Tahun ' . $row['Year'];
    $values[] = (float)$row['TotalSpent'];
}

echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
