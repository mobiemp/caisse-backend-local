<?php
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
include 'DBConfig.php';
include 'functions.php';


if(isset($_GET['action']) && $_GET['action'] == 'categorieListe'){
	$sql = "SELECT * FROM table_client_categorie";
	$result = $conn->query($sql);

	if ($result->num_rows >0) {

		while($row[] = $result->fetch_assoc()) {
			$tem = $row;

			$json['categories'] = $tem;

		}

	} else {
		echo json_encode("Aucune categories trouvés.") ;
	}
	echo json_encode($json);
	$conn->close();
}