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
    $clear = $request->clear;
    $session = $request->session;
    $id_caisse = $request->id_caisse;
    if ($clear == true) {
        $sql = "DELETE FROM table_client_panier WHERE session = $session AND id_caisse = $id_caisse";
        if ($conn->query($sql) === TRUE) {
            echo 1;
            $fp = fopen('../jsons/panier.json', 'w');
            fwrite($fp, json_encode([]));
            fclose($fp);
        } else {
            echo 0;
        }
    }
}




