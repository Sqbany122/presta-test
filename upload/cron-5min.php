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

// USTAW ILOSC 9999 produktów w ID KATEGORI
//$sql3 = "UPDATE ps_product set active = 1 where id_product in (select distinct id_product from ps_category_product WHERE id_category IN (1295, 1296, 1297, 1331, 1335,1332,1333,1334,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1376,1331,1335,1332,1333,1334,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1336,1337,1338,1339,1340,1341,1342,1343,1344,1345,1346,1347,1348,1349,1350,1351,1352,1353,1354,1355,1357,1358,1370,1371,1375,1506))";
$sql3 = "UPDATE ps_product set active = 1 where id_product in (select distinct id_product from ps_category_product WHERE id_category IN (1449))";
//$result3 = $conn->query($sql3);

//$sql4 = "UPDATE ps_product_shop set active = 1 where id_product in (select distinct id_product from ps_category_product WHERE id_category IN (1295, 1296, 1297, 1331, 1335,1332,1333,1334,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1376,1331,1335,1332,1333,1334,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1336,1337,1338,1339,1340,1341,1342,1343,1344,1345,1346,1347,1348,1349,1350,1351,1352,1353,1354,1355,1357,1358,1370,1371,1375,1506))";
$sql4 = "UPDATE ps_product_shop set active = 1 where id_product in (select distinct id_product from ps_category_product WHERE id_category IN (1449))";
//$result4 = $conn->query($sql4);

//$sql3c = "UPDATE ps_stock_available set quantity = 999 where id_product in (select distinct id_product from ps_category_product WHERE id_category IN (1295, 1296, 1297, 1331, 1335,1332,1333,1334,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1376,1331,1335,1332,1333,1334,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1336,1337,1338,1339,1340,1341,1342,1343,1344,1345,1346,1347,1348,1349,1350,1351,1352,1353,1354,1355,1357,1358,1370,1371,1375,1506))";
$sql3c = "UPDATE ps_stock_available set quantity = 999 where id_product in (select distinct ps_category_product.id_product from ps_category_product LEFT JOIN ps_product ON ps_product.id_product = ps_category_product.id_product AND ps_product.active = 1 WHERE id_category IN (1449))";
$result3c = $conn->query($sql3c);

// USTAW ILOSC 9999 produktów w ID PRODUKTÓW
//$sql3d = "UPDATE ps_stock_available set quantity = 999 where id_product in (select distinct id_product from ps_category_product WHERE id_category IN (1295, 1296, 1297, 1331, 1335,1332,1333,1334,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1376,1331,1335,1332,1333,1334,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1336,1337,1338,1339,1340,1341,1342,1343,1344,1345,1346,1347,1348,1349,1350,1351,1352,1353,1354,1355,1357,1358,1370,1371,1375,1506,83250,83251,83252,83253,83254,83255,83256,83257,83258,83259,83260,83261,83262,83630,83631,83632,83633,83710,83711,83712,83713,83714,83756))";
$sql3d = "UPDATE ps_stock_available set quantity = 999 where id_product in (9727,9731,9732,9733,9734,46131,50594,50594,16513,9729,9733,9734,9727,9730,9731,46228,9732,46131,15566,9735,9736,9728,8437,9185,9182,9186,9187,9190,9191,9199,9188,9197,9201,14377,9192,9196,9194,8453,12069,12068,9193,12665,12669,10954,9200,9206,9211,9209,12670,9435,9215,9214,9205,9210,12668,8438,14940,9202,9208,9203,8457,9624,9622,9627,9626,9625,9628,9623,11517,11516,9127,9126,11482,11513,14422,12206,9133,9138,12208,9130,14458,9616,9613,11515,9125,9619,9618,9614,9617,9629,9631,9139,9145,9144,12661,12663,12662,9143,12623,13361,13362,9120,14107,9132,9124,9122,11518,11520,9121,44801,9147,9148,9990,9989,9146,9118,9150,9149,12514,9161,9158,9154,9156,9151,9293,9294,9153,9159,9155,9152,9157,9162,9288,9163,9300,9296,9297,9938,9615,9304,9289,9165,9172,9303,9428,12228,9301,9174,12155,9169,9170,9292,9292,9173,9166,9177,9175,9178,9176,9180,9179,9295,9290,9291,9181,9183,9287,9286,9285,9184,6113,6091,14394,16113,16114,11017,11934,16063,16064,16065,16066,16067,16070,16072,16073,16074,16075,16080,16081,16082,16092,16093,16094,16062,15523,15524,15525,15526,15527,15528,15529,15530,16101,16102,16103,16104,16105,16106,16107,16108,16109,16110,16111,16112,16113,16114,16115,16116,16117,16118,16119,16120,16121,16122,16123,16124,16125,16127,16135,16136,16137,16138,16139,1001,9996,9999,9998,4100,4113,4108,4114,4111,4069,4110,4112,4131,4134,4126,16128,46374,49194,49212,49260,49332,49333,49334,49335,49336,49337,16098,16099,49617,10001,49619,49328,50835,50815,9126,9619,12206,50423,51114,51178,51771,51774,51777,9614,9617,49328,9121,9989,54047,54051,55137,9186,9296,9428,9615,9624,9735,12069,14940,15526,16062,16102,16104,16124,16127,16135,55430,57032,57439,57442,57848,57853,57854,57855,57856,57875,13535,13538,13544,9727,9728,9729,9730,9731,9732,9733,9734,9735,9736,46131,46228,5059,43396,60984,60985,60986,61729,61730,61733,61736,61739,61742,45979,61747,61750,61769,61170,61771,61167,61176,61169,62056,62311,63184,66569,66570,66640,66641,72956,72958,73224,74904,9152,9153,9156,9159,9163,9170,9296,9298,9300,14412,61747,12208,57442,51771,51777,51774,45979,62988,57439,72958,57032,51114,9120,9118,61730,61736,62311,50423,8915,8925,8926,8928,9726,73705,73706,73707,73711,73712,73713,73714,73715,73716,73717,73718,73719,82473,82474,82475,82476,82477,82478,82479,82480,82481,82482,82483,82484,82485,82486,82487,82488,82489,82490,82491,82492,83815,83250,83251,83252,83253,83254,83255,83256,83257,83258,83259,83260,83261,83262,83630,83631,83632,83633,83710,83711,83712,83712,83713,83714,83756,83925,83926,83929,83930,83961,83962,83965,83966,83969,83970,83973,83974,84309,84310,84596,84597,84598,84599,84600,84601,84627,84628,84629,84630,84631,84722,84723,84724,84725,84726,84727,84728,84729,84730,84731,84732,84733,84734,85667,85668,85802,85963,85941,85924)";
$result3d = $conn->query($sql3d);
// active?

// AND active=1
//$sql8 = "UPDATE ps_product SET active=1 WHERE id_product IN (SELECT id_product FROM ps_stock_available WHERE quantity>0);";
//$result8 = $conn->query($sql8);

//$sql7 = "UPDATE ps_product_shop SET active=1 WHERE id_product IN (SELECT id_product FROM ps_stock_available WHERE quantity>0);";
//$result7 = $conn->query($sql7);

// AND active=0
//$sql6 = "UPDATE ps_product_shop SET active=0 WHERE id_product IN (SELECT id_product FROM ps_stock_available WHERE quantity=0);";
$sql6 = "UPDATE ps_product_shop SET active=0 WHERE id_product IN (SELECT id_product FROM ps_stock_available WHERE quantity=0 AND id_product_attribute = 0 AND id_shop = 1)";
$result6 = $conn->query($sql6);

$sql6a = "UPDATE ps_product SET active=0 WHERE id_product IN (SELECT id_product FROM ps_stock_available WHERE quantity=0 AND id_product_attribute = 0 AND id_shop = 1)";
$result6a = $conn->query($sql6a);

$sql9 = "UPDATE ps_product SET active=0 WHERE id_product IN (SELECT id_product FROM ps_stock_available WHERE quantity=0 AND id_shop = 1);";
$result9 = $conn->query($sql9);

$sql = "UPDATE ps_product set active = 0 where id_product not in (select distinct id_product from ps_image);";
$result = $conn->query($sql);

$sql2 = "UPDATE ps_product_shop set active = 0 where id_product not in (select distinct id_product from ps_image);";
$result2 = $conn->query($sql2);

//

// PRODUKTY W PROMOCJI - AKTYWACJA
//$sql1p = "UPDATE ps_product set active = 1 where id_product = 10000 OR id_product = 10002 OR id_product = 10003 OR id_product = 10004";
//$result1p = $conn->query($sql1p);

//$sql2p = "UPDATE ps_product_shop set active = 1 where id_product = 10000 OR id_product = 10002 OR id_product = 10003 OR id_product = 10004";
//$result2p = $conn->query($sql2p);



//$headers = "From: natalia@pegazshop.pl\r\n";
//mail('rafal@a-creative.pl', 'przykladowa wiadomosc', 'tutaj jakas tresc', $headers);

/*
UPDATE ps_product set active = 0 where id_product not in (select distinct id_product from ps_image);
UPDATE ps_product_shop set active = 0 where id_product not in (select distinct id_product from ps_image);
*/

/*if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
    }
} else {
    echo "0 results";
}*/


// ZMIANA DEFOLTOWEGO ATTRYBUTU JEŻELI BRAK GO W SKLEPIE

// Szukamy atrybuty defoltowe ze stanem magazyowym zero
$sql_a = "SELECT ps_stock_available.id_product, ps_product_attribute_shop.id_product_attribute, default_on FROM ps_stock_available LEFT JOIN ps_product_attribute_shop ON ps_product_attribute_shop.id_product_attribute = ps_stock_available.id_product_attribute WHERE ps_stock_available.quantity=0 AND ps_stock_available.id_product_attribute != 0 AND default_on = 1";
$result_a = $conn->query($sql_a);

if ($result_a->num_rows > 0) {
	
    // output data of each row
	$id_s = array();
	
    while($row = $result_a->fetch_assoc()) {
		
		$id_s_r = array();
        $id_s_r['id_product'] = $row["id_product"];
		$id_s_r['id_product_attribute'] = $row["id_product_attribute"];
		
		$id_s[] = $id_s_r;
    }

	foreach($id_s as $val){
	
		// Szukamy atrybut dla produktu ze stanem magazyowym powyżej zero
		$sql_aSTOCK = "SELECT ps_stock_available.id_product, ps_product_attribute_shop.id_product_attribute, default_on FROM ps_stock_available LEFT JOIN ps_product_attribute_shop ON ps_product_attribute_shop.id_product_attribute = ps_stock_available.id_product_attribute WHERE ps_stock_available.quantity > 0 AND ps_stock_available.id_product_attribute != 0 AND default_on IS NULL AND ps_stock_available.id_product = ".(int)$val['id_product'];
//echo $sql_aSTOCK.'<br />';
		$result_aSTOCK = $conn->query($sql_aSTOCK);

		if ($result_aSTOCK->num_rows > 0) {

			$row = $result_aSTOCK->fetch_assoc();

			// odznaczamy defoltowość tych atrybutow
			$sql_au = "UPDATE `ps_product_attribute` SET `default_on` = NULL WHERE `ps_product_attribute`.`id_product_attribute` = ".(int)$val['id_product_attribute'];
		//	echo $sql_au.'<br />';
			$conn->query($sql_au);

			$sql_au = "UPDATE `ps_product_attribute_shop` SET `default_on` = NULL WHERE `ps_product_attribute_shop`.`id_product_attribute` = ".(int)$val['id_product_attribute'];
		//	echo $sql_au.'<br />';
			$conn->query($sql_au);

			// zmieniamy w dwóch tabelach
			$sql_au = "UPDATE `ps_product_attribute` SET `default_on` = '1' WHERE `ps_product_attribute`.`id_product_attribute` = ".(int)$row['id_product_attribute'];
		//	echo $sql_au.'<br />';
			$conn->query($sql_au);

			$sql_au = "UPDATE `ps_product_attribute_shop` SET `default_on` = '1' WHERE `ps_product_attribute_shop`.`id_product_attribute` = ".(int)$row['id_product_attribute'];
		//	echo $sql_au.'<br />';
			$conn->query($sql_au);
		}
	}
}

$conn->close();

?>
