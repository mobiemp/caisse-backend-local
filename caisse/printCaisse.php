<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include '../DBConfig.php';
include '../functions.php';
// EN LOCAL
include '../infos.php';
require '../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
// SUR CONTABO
session_start();
$postdata = file_get_contents('php://input');

if(isset($postdata)){
    $request = json_decode($postdata);
    if(isset($request->printCaisse)){
//        echo successResponse($request->printCaisse,1);
        $monnaieArr = $request->printCaisse;
        $totalCaisse = $request->totalCaculCaisse;
        $ticket_body = "";
        foreach ($monnaieArr as $key => $monnaie){
            $keySplitted = explode("-",$key);
            $newKey = $keySplitted[1] . " " . $keySplitted[0];
            $ticket_body .= $newKey  . " : " . $monnaie . "\n";
        }
        $date_sortie_ticket = date('d/m/Y H:s:i');
        $ticket = "CAISSE numero $id_caisse
DATE : $date_sortie_ticket
------------------
$ticket_body

------------------
TOTAL : $totalCaisse EUR

";

        try {
            $connector = null;
            $connector = new CupsPrintConnector($imprimantes_ticket);

            $printer = new Printer($connector);
            $printer -> text($ticket);
            $printer -> cut();
                
            
        $printer->close();

        } catch (Exception $e) {
            echo json_encode("Impossible d'imprimer sur cette imprimante: " . $e->getMessage() . "\n");
        }
        file_put_contents('ticket_calcul_caisse.txt', $ticket);
        echo successResponse($ticket, 1);
    }
}


