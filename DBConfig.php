<?php 
//Define your host here.
$HostName = "localhost";
 
//Define your database name here.
$DatabaseName = "mobipos";
 
//Define your database username here.
$HostUser = "caisse";
 
//Define your database password here.
$HostPass = "za2xY+MM1d_5fy#s";
// $HostPass = "";

$conn = new mysqli($HostName, $HostUser, $HostPass, $DatabaseName);

if ($conn->connect_error) {
 
 		echo json_encode(array('message'=>"Connection failed: " . $conn->connect_error));
	}
 
?>
