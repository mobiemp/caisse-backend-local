<?php 	
header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');


$postdata = file_get_contents('php://input');

if (isset($postdata)) {
    $request  = json_decode($postdata);
    $clear = $request->clear;
    if ($clear == true) {
        include '../DBConfig.php';

        $sql = 'DELETE FROM table_client_panier';
        if ($conn->query($sql) === TRUE) {
            echo 1;
            $fp = fopen('../jsons/panier.json', 'w');
            fwrite($fp, json_encode([]));
            fclose($fp);
        }
        else{
            echo 0;
        }
        
        
    }
}

 ?>