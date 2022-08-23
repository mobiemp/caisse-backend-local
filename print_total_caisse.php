<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include 'DBConfig.php';
include 'functions.php';
include 'parametre.php';
include 'infos.php';
require 'vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
$postdata = file_get_contents('php://input');
if(isset($postdata)){
    $request = json_decode($postdata);
    $id_caisse = $request->id_caisse;
    $today_date = date('Y-m-d');
    $sql = "SELECT * FROM `table_client_ticket` WHERE date like '%$today_date%' AND id_caisse = $id_caisse";
    $tickets = $conn->query($sql);
    $nbligne = $tickets->num_rows;
    $total_espece = 0;
    $total_cb = 0;
    $total_cheques = 0;
    $total_ttc = 0;
    $total_remise = 0;
    if($nbligne>0){
        while($ticket = $tickets->fetch_assoc()){
            $total_espece+= $ticket['p_espece_euro'];
            $total_cheques += $ticket['p_cheque_euro'];
            $total_cb += $ticket['p_cb'];
            $total_ttc += $ticket['total_euro_du'];
            $total_remise += $ticket['total_remise'];
        }
        $total_ttc = formatNumber($total_ttc);
        $total_ttc = $total_ttc - $total_remise;
        $total_cheques = formatNumber($total_cheques);
        $total_espece = formatNumber($total_espece);
        $total_cb = formatNumber($total_cb);
        $date_sortie_ticket = date('d/m/Y H:s:i');
        $total_caisse = "
CAISSE $id_caisse
DATE : $date_sortie_ticket
-----------------------------

TOTAL ESPECE : $total_espece EUR
TOTAL CB : $total_cb EUR
TOTAL CHEQUE: $total_cheques EUR

------------------------------

TOTAL TTC : $total_ttc EUR


";
        try {
            $connector = null;
            $connector = new CupsPrintConnector($imprimantes_ticket);

            $printer = new Printer($connector);
            $printer->text($total_caisse);
            $printer->cut();
            $printer->close();
            

        } catch (Exception $e) {
            echo json_encode("Impossible d'imprimer sur cette imprimante: " . $e->getMessage() . "\n");
        }
        file_put_contents('ticket_total_caisse.txt', $total_caisse);
        echo json_encode(array('response'=>1,"ticket"=>$total_caisse,'total_espece' => round($total_espece,2),'total_cb'=>round($total_cb,2),'total_cheques'=>round($total_cheques,2),'total_ttc'=>round($total_ttc,2)));
    }
    else{
        echo 0;
    }
}
