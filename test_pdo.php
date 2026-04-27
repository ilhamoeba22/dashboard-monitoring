<?php
$serverName = "192.168.1.130,44333";
try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=MCI_MAR26_01042026;TrustServerCertificate=1", "sa", "bon");
    echo "Connected successfully with bon\n";
} catch(PDOException $e) {
    echo "Connection failed with bon: " . $e->getMessage() . "\n";
}
