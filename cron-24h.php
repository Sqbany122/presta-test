 <?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "parkur_pegazshop";
$password = "vrligNsIFzMdwZUL";
$dbname = "parkur_pegazshop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// GENERUJEMY WYSYLKI DLA WSZYZTKICH PRODUKTÓW POZA PASZAMI I SUPLEMENTAMI
//$sql_delivery = "select distinct id_product from ps_category_product WHERE id_category NOT IN (1331,1335,1332,1333,1334,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1336,1337,1338,1339,1340,1341,1342,1343,1344,1345,1346,1347,1348,1349,1350,1351,1352,1353,1354,1355,1357,1358,1370,1371,1375)";
$sql_delivery = "select distinct id_product from ps_category_product WHERE id_category NOT IN (1293,1492,1133,1134,1135,1196,1197,1479,1380,1397,1400,1482,1401,1402,1403,1404,1405,1406,1407,1408,1409,1410,1411,1412,1413,1398,1399,1438,1497,1415,1427,1428,1429,1430,1431,1416,1432,1417,1433,1434,1419,1435,1420,1436,1421,1437,1422,1483,1423,1484,1424,1485,1425,1486,1426,1487,1418,1304,1305,1471)";
$result_delivery = $conn->query($sql_delivery);
$daa = 0;

while ($rowd = $result_delivery->fetch_assoc()) {
    $iddel = $rowd['id_product'];

    $carriers_to_insert = array(3, 7, 13, 90, 91, 94, 95, 189, 195, 196, 199);

    foreach ($carriers_to_insert as $carrier_id) {
        // Sprawdź, czy kombinacja istnieje już w tabeli
        $check_duplicate_sql = "SELECT COUNT(*) AS count FROM `ps_product_carrier` WHERE `id_product` = $iddel AND `id_carrier_reference` = $carrier_id AND `id_shop` = 1";
        $result_check_duplicate = $conn->query($check_duplicate_sql);
        $count = $result_check_duplicate->fetch_assoc()['count'];

        if ($count == 0) {
            // Kombinacja nie istnieje, dodaj nowy wiersz
            $sql_add_del = "INSERT INTO `ps_product_carrier` (`id_product`, `id_carrier_reference`, `id_shop`) VALUES ($iddel, $carrier_id, 1)";
            $result_add_del = $conn->query($sql_add_del);
        } else {
            // Kombinacja już istnieje, pomiń dodawanie
            //echo "Kombinacja już istnieje dla id_product = $iddel i id_carrier_reference = $carrier_id <br>";
        }
    }
}

/*while($rowd = $result_delivery->fetch_assoc()) {
	$iddel = $rowd['id_product'];
	//$daa++;
	//echo $iddel.'<br>';

	$sql_add_del = "INSERT INTO `ps_product_carrier` (`id_product`, `id_carrier_reference`, `id_shop`) VALUES
	(".$iddel.", 3, 1),
	(".$iddel.", 7, 1),
	(".$iddel.", 13, 1),
	(".$iddel.", 90, 1),
	(".$iddel.", 91, 1),
	(".$iddel.", 94, 1),
	(".$iddel.", 95, 1),
	(".$iddel.", 189, 1)";
	$result_add_del = $conn->query($sql_add_del);
}*/

$conn->close();

?>
