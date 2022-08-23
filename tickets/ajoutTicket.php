<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

require '../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;

$postdata = file_get_contents('php://input');
if (isset($postdata)) {
    $request = json_decode($postdata);

    $totalPanier = $request->total;

    $qte_total = 0;
    $ticket = "";
    $ticket_ligne = "";
    $commandes = [];
    $total_remise_euro = 0;
    $total_remise_pourcent = 0;
    $total_euro_du = 0;
    $totalTTC = 0;
    $totalHT = 0;
    $totalTVA8 = 0;
    $totalTVA2 = 0;
    $totalTVA1 = 0;
    $retour_article = 0;
    include('print-ticket.php');
    include '../DBConfig.php';
    session_start();
    $id_caisse = $_SESSION['id_caisse'];
    $session = $_SESSION['session'];
    $sql = "SELECT * FROM table_client_panier WHERE id_caisse = $id_caisse AND session = $session";
    $query = $conn->query($sql);
    while ($ligne = $query->fetch_assoc()) {

        $sessionPanier = $ligne['session'];
        $ref = $ligne['ref'];
        if ($sessionPanier == $session && $ref != "0007") {
            $deconsigne = 0;
            $qte = $ligne['qte'];
            $qte_total += $qte;
            $promo =  $ligne['pu_euro'] - $ligne['promo'];
            $pu_euro = (float)$ligne['pu_euro'];
            $taux_tva = (float)$ligne['taux_tva'];
            $idproduit = $ligne['id_produit'];
            $remise_euro = $ligne['remise_euro'];
            $remise_pourcent = $ligne['remise'];
            $titre = $ligne['titre'];
            $famille = $ligne['famille'];
            $retour = $ligne['retour'];


            if($retour==1){
                $retour_article+=$pu_euro*$qte;
                $qte = -$qte;
            }
            $totalTTC += $remise_pourcent > 0 ? ($pu_euro * ($remise_pourcent/100)) * $qte :$qte * $pu_euro;
            $totalHT += $remise_pourcent > 0 ? ($pu_euro - ($pu_euro * ($remise_pourcent / 100))) / (1+$taux_tva/100)  * $qte : ($pu_euro / (1+$taux_tva/100)) * $qte;
            if($taux_tva == 8.5){
                if($remise_pourcent>0){

                    $prix_apres_remise = $pu_euro - ($pu_euro*($remise_pourcent/100));
                    $montant_tva = $prix_apres_remise - ($prix_apres_remise/(1+$taux_tva/100));
                    $totalTVA8 += $montant_tva * $qte;
                }
                else{
                    $totalTVA8 += ($pu_euro -($pu_euro / (1+$taux_tva/100))) * $qte;
                }

            }
            if($taux_tva == 2.1){
                if($remise_pourcent>0){
                    $prix_apres_remise = $pu_euro - ($pu_euro*($remise_pourcent/100));
                    $montant_tva = $prix_apres_remise - ($prix_apres_remise/(1+$taux_tva/100));
                    $totalTVA2 += $montant_tva * $qte;
                }
                else{
                    $totalTVA2 += ($pu_euro -($pu_euro / (1+$taux_tva/100))) * $qte;
                }

            }
            if($taux_tva == 1.05){
                if($remise_pourcent>0){
                    $prix_apres_remise = $pu_euro - ($pu_euro*($remise_pourcent/100));
                    $montant_tva = $prix_apres_remise - ($prix_apres_remise/(1+$taux_tva/100));
                    $totalTVA1 += $montant_tva * $qte;
                }
                else{
                    $totalTVA1 += ($pu_euro -($pu_euro / (1+$taux_tva/100))) * $qte;
                }
            }

            $total_remise_euro += $remise_euro;
            $remise_pourcents = $remise_pourcent > 0 ? $pu_euro * $qte * ($remise_pourcent / 100) : 0;
            $total_remise_pourcent += $remise_pourcents;
            $taux_tva_ticket = $taux_tva . "%";


            // GENERATION DES COMMANDES

            $commandes[] = (object) array('id_caisse' => $id_caisse, 'pu_euro' => $pu_euro , 'idproduit' => $idproduit, 'qte' => $qte, 'remise' => $remise_euro + $remise_pourcents,  'taux_tva' => $taux_tva, 'famille' => $famille, 'promo' => $promo );
            $prix_total = $pu_euro * $qte;
            $prix_total_ticket = formatNumber(prix_total);
            
//            $titre = $remise_pourcent > 0 ?  : $titre;
//            $pu_euro = ($remise_pourcent > 0 ? $pu_euro - $pu_euro *  ($remise_pourcent / 100) : ($remise_euro > 0 ? $pu_euro - $remise_euro :  $pu_euro));

            $ligne_ticket = setStringLen($titre, $designiation_limit);
            $ligne_prix_commande = setStringLen($prix_total_ticket, $mttc_limit, true);
            $ligne_tva = setStringLen($taux_tva_ticket, $tva_limit);
            $pu_euro = $ligne['promo'] > 0 ? $promo : $pu_euro;

            $ligne_qte = setStringLen($qte . "*" . $pu_euro, $qte_prix_limit);
            $ticket_ligne .= $ligne_qte . $ligne_ticket . "  " .$ligne_prix_commande . " " . $ligne_tva. "\n";

            if($remise_pourcent > 0 ){
                $ticket_ligne .= str_repeat(" ",4).setStringLen("Remise de -".$remise_pourcent."%",$designiation_limit)." ";
                $ticket_ligne.= setStringLen("-".formatNumber(($pu_euro * ($remise_pourcent / 100) * $qte)), $mttc_limit, true);

                $ticket_ligne .= "\n";
            }
            elseif($remise_euro>0){
                $ticket_ligne .= str_repeat(" ",12).setStringLen("Remise",$designiation_limit).str_repeat(" ",12);
                $ticket_ligne.= setStringLen("-".formatNumber(($pu_euro * $qte - $remise_euro)), $mttc_limit, true);
                $ticket_ligne .= "\n";
            }
            elseif($ligne['promo']>0){
                $ligne_ticket .= str_repeat(" ",4).setStringLen("Remise".formatNumber($ligne['promo']),$designiation_limit).str_repeat(" ",12);
                $ticket_ligne = setStringLen(formatNumber($prix_total-$ligne['promo']), $mttc_limit, true);
                $ticket_ligne = "\n";
            }




//      $ticket_ligne .= sprintf($ticket_layout, strval($qte) . "*" . strval($pu_euro), $titreTicket, strval($pu_euro) . "€", strval($taux_tva_rounded) . "%") . "\n";
            if (strlen($ref) > 9 && strlen($ref) <= 13  ) {
                $sql = "SELECT choix_mode_prix,stock,stock_alerte,num FROM table_client_catalogue WHERE ref = '$ref'";

                $query_res = $conn->query($sql);
                $res = $query_res->fetch_row();
                $mode = $res[0];
                $stock = $res[1];
                $num = $res[3];
                $total_euro_du += ($mode == 3 ? $pu_euro * $qte : ($mode == 2 ? ($pu_euro + ($pu_euro * $taux_tva / 100)) : $pu_euro * $qte));
                // MISE A JOUR DU STOCK
                if ($stock > 0) {
                    $sql = "UPDATE table_client_catalogue 
                    SET stock = stock - $qte  
                    WHERE num = $num";
                    $updateCatalogue = $conn->query($sql);
                }
            }
            elseif($idproduit == "#DIVERS"){
                $total_euro_du += $pu_euro *$qte;
            }
        }
    }

    $retour_article = 0;
    $echange_article = 0;
    $date = date('Y-m-d H:i:s');
    $printTicket = true;

    // RECUPERE LE MONTANT POUR CHAQUE TYPE DE PAIEMENT

    $p_espece = (float)$request->espece;
    $p_cb = (float)$request->cb;
    $p_cheque = (float)$request->cheques;
    $p_restaurant = (float)$request->ticket_restaurant;
    $total_euro = $p_espece + $p_cb + $p_cheque + $p_restaurant;
    $total_remise = $total_remise_euro + $total_remise_pourcent;
    if ($p_espece == 0) {
            $printTicket = false; // ON OUVRE PAS LE TIROIR CAISSE SI PAS DE PAIEMENT EN ESPECE
    }
    $arendre = 0;
    if(isset($request->rendu)){
       if($request->rendu == "true"){
            $arendre = $request->arendre;
            $p_espece = $request->espece - $request->arendre;
        }
    }

        $sql = "INSERT INTO table_client_ticket (`id_caisse`, `p_cheque_euro`, `p_cb`, `p_espece_euro`, `p_restaurant`,`total_remise`,`deconsigne`, `retourarticle`, `echangearticle`, `total_euro`, `total_euro_du`, `qte_total`,`date`, `sendserveur`,`id_ticket`) 
        VALUES ($id_caisse,$p_cheque,$p_cb,$p_espece,$p_restaurant,$total_remise,$deconsigne,$retour_article,$echange_article,$total_euro,$total_euro_du,$qte_total, '" . $date . "',1,0)";
            $insertTicket=   $conn->query($sql);
            $last_id = $conn->insert_id;
            $numero_ticket = "Numero de ticket: ".$last_id."\n";

            $totalPanier = formatNumber($totalPanier);
            $totalHT = formatNumber($totalHT);
            $totalTVA8 = formatNumber($totalTVA8);
            $totalTVA2 = formatNumber($totalTVA2);
            $totalTVA1 = formatNumber($totalTVA1);
            $total_a_payer = str_repeat(" ", 10) ."TOTAL A PAYER TTC". str_repeat(" ",4).setStringLen($totalPanier,6)." EUR"; 
            $total_ht = str_repeat(" ", 16) ."TOTAL HT". str_repeat(" ",4).setStringLen($totalHT,6)." EUR";
            $total_tva8 = str_repeat(" ", 16) ."TVA 8.5%". str_repeat(" ",4).setStringLen($totalTVA8,6)." EUR";
            $total_tva2 = str_repeat(" ", 16) ."TVA 2.1%". str_repeat(" ",4).setStringLen($totalTVA2,6)." EUR";
            $total_tva1 = str_repeat(" ", 15) ."TVA 1.05%". str_repeat(" ",4).setStringLen($totalTVA1,6)." EUR";

            $ticket_part2 = $total_a_payer."\n";
            $ticket_part2 .= $total_ht ."\n";

            if($totalTVA8>0){
                $ticket_part2 .= $total_tva8 ."\n";
            }
            if($totalTVA2>0){
                $ticket_part2 .= $total_tva2 ."\n";
            }
            if($totalTVA1>0){
                $ticket_part2 .= $total_tva1."\n";
            }

            $footer_ticket = "T".$id_caisse."W".$last_id." - ".date('d/m/Y H:i:s',strtotime($date));
            

            $details_paiement = "  Details du paiement:";
            if($p_espece>0){
                $details_paiement .= "\n     > ".formatNumber($p_espece)." € EN ESPECES";
            }
            if($arendre>0){
                $details_paiement.= "\n      > MONNAIE RENDU ".formatNumber($arendre)." EUR";
            }
            if($p_cb>0){
                $details_paiement .= "\n     > ".formatNumber($p_cb)." € EN CB";
            }
            if($p_cheque>0){
                $details_paiement .= "\n     > ".formatNumber($p_cheque)." € EN CHEQUE";
            }


            $ticket .= $ticket_entete
            .$ticket_corps
            .$ticket_ligne
            ."\n"
            .$separator
            .$ticket_part2
            ."--------------------------------------"
            ."\n\n"
            .$details_paiement
            ."\n\n"
            .$ticket_pied
            ."\n\n"
            ."  ======================================"
            ."\n"
            ."       ".$footer_ticket
            ."\n";

            file_put_contents('ticket.txt', $ticket);
            try {
                $connector = null;
                $connector = new CupsPrintConnector($imprimantes_ticket);

                $printer = new Printer($connector);
                if ($printTicket == true) {
                    $printer -> text($ticket);
                    $printer -> cut();
                    $printer->pulse();
                    echo json_encode(array('response' => 1, 'message' => 'IMPRIME TICKET + OUVERTURE TIROIR CAISSE', 'session' => $session, 'id_caisse' => $id_caisse));
                } else {
                    $printer -> text($ticket);
                    $printer -> cut();
                    echo json_encode(array('response' => 1, 'message' => 'IMPRIME TICKET SEULEMENT', 'session' => $session, 'id_caisse' => $id_caisse));
                }
                $printer->close();
            } catch (Exception $e) {
                echo json_encode("Impossible d'imprimer sur cette imprimante: " . $e->getMessage() . "\n");
            }

            // } else {
            //     echo json_encode(array('response' => 0, 'message' => 'ERREUR INSERTION TICKET'));
            // }
        }
