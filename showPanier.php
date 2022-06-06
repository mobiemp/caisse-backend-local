<?php 
include 'DBConfig.php';

// Create connection

$sql = "SELECT * FROM table_client_panier";

$result = $conn->query($sql);

if ($result->num_rows >0) {
 
	 while($row[] = $result->fetch_assoc()) {
	 
		 $tem = $row;
		 
		 $json['panier'] = $tem;
 
 }
 
} else {
 echo "Panier vide.";
}
$conn->close();



$fp = fopen('jsons/panier.json', 'w');
fwrite($fp, json_encode($json));
fclose($fp);
 ?>