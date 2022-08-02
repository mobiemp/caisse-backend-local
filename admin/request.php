<?php 

if(isset($_POST['startDate']) and isset($_POST['endDate'])){
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];
	echo json_encode(array('startDate'=>$startDate,'endDate'=>$endDate));
}
 ?>