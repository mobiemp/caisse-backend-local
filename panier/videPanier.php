<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include '../functions.php';
include '../DBConfig.php';
$postdata = file_get_contents('php://input');
if (isset($postdata)) {
    $request = json_decode($postdata);
    if(isset($request->clear)){
        $clear = $request->clear;
        $session = $request->session;
        $id_caisse = $request->id_caisse;
        if ($clear == true) {
            $sql = "DELETE FROM table_client_panier WHERE session = $session AND id_caisse = $id_caisse";
            if ($conn->query($sql) === TRUE) {
                echo 1;
//            $fp = fopen('../jsons/panier.json', 'w');
//            fwrite($fp, json_encode([]));
//            fclose($fp);
                regenerePanier($conn,"SELECT * FROM table_client_panier","../jsons/panier.json");
            } else {
                echo 0;
            }
        }
    }
    elseif(isset($request->videTout)){
        $id_caisse = $request->id_caisse;
        $videTout = $request->videTout;
        if($videTout){
            $sql = "DELETE FROM table_client_panier WHERE id_caisse = $id_caisse";
            $delete = $conn->query($sql);
            if($delete){
                regenerePanier($conn,"SELECT * FROM table_client_panier","../jsons/panier.json");
                echo json_encode(array("response" => 1));
            }
        }
    }

}




