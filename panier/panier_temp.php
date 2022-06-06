<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

$postdata = file_get_contents('php://input');

if (isset($postdata)) {
    $request  = json_decode($postdata);
    $espece = $request->espece;
    $cb = $request->cb;
    $cheques = $request->cheques;
    $totalTemp = $request->totalTemp;
    $ticket_restaurant = $request->ticket_restaurant;

    include '../DBConfig.php';
    $isExist = $conn->query('SELECT * FROM table_paiement_temp');
    $nbligne = $isExist->num_rows;
    
    if ($nbligne == 0) {
        $sql = "INSERT INTO table_paiement_temp(espece_euro,cb_euro,cheques_euro,total_temp,ticket_restaurant) VALUES ($espece,$cb,$cheques,$totalTemp,$ticket_restaurant)";
        if ($conn->query($sql)) {
            $sql = 'SELECT * FROM table_paiement_temp ORDER BY id DESC LIMIT 1';
            $temp_paiement = $conn->query($sql);
            echo $temp_paiement == TRUE ? 1 : 0;
        }
    } else {
        $sql = "UPDATE table_paiement_temp 
        SET espece_euro = espece_euro + $espece, 
        cb_euro = cb_euro + $cb, 
        cheques_euro = cheques_euro + $cheques, 
        total_temp = $totalTemp,
        ticket_restaurant = $ticket_restaurant
        WHERE temp = 1 ORDER BY id DESC
        LIMIT 1";
        if ($conn->query($sql)) {
            $sql = 'SELECT * FROM table_paiement_temp ORDER BY id DESC LIMIT 1';
            $temp_paiement = $conn->query($sql);
            echo $temp_paiement == TRUE ? 2 : 0;
        }
    }
}
