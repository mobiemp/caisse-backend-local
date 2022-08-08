<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
include '../DBConfig.php';
include '../functions.php';


$postdata = file_get_contents('php://input');
if(isset($postdata)){
    $request = json_decode($postdata);
    session_start();
    $id_caisse = $_SESSION['id_caisse'];
    if(isset($request->clientSuivant)){
        $actuelSession = $request->clientSuivant;
        $_SESSION['session'] = $actuelSession + 1;
        $newSession = $_SESSION['session'];

        regenerePanier($conn,"SELECT * FROM table_client_panier WHERE session = $newSession AND id_caisse = $id_caisse","../jsons/panier.json");
        echo json_encode(array("response" => 1 , "data" => $_SESSION['session']));
    }
    else if(isset($request->clientPrecedent)){
        $actuelSession = $request->clientPrecedent;
        $_SESSION['session'] = $actuelSession - 1;
        $newSession = $_SESSION['session'];
        regenerePanier($conn,"SELECT * FROM table_client_panier WHERE session = $newSession AND id_caisse = $id_caisse ","../jsons/panier.json");
        echo json_encode(array("response" => 1 , "data" => $_SESSION['session']));
    }
}
