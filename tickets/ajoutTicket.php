<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

require '../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$postdata = file_get_contents('php://input');
if (isset($postdata)) {
	$request  = json_decode($postdata);
	$panierData = $request->panier;
	$totalPanier = $request->totalPanier;
	$id_caisse = $request->id_caisse;
	$qte_total = 0;
	$ticket = "";
	$ticket_ligne = "";
	$commandes = [];
	$total_remise = 0;
	$total_euro_du = 0;
	include('print-ticket.php');
    include '../DBConfig.php';
	foreach ($panierData as $data) {

		$total_remise += $data->remise;
		$remise = $data->remise;
		$deconsigne = 0;
		$qte_total += $data->qte;
		$qte = $data->qte;
		$totalTTC = $qte * $data->pu_euro;
		$titre = $data->titre;
		$pu_euro = $data->pu_euro;
		$taux_tva = (float) $data->taux_tva;
//		$taux_tva = ($codetva == 8 ? 8.5 : ($codetva == 2 ? 2.1 : ($codetva == 1 ? 1.05 : 0 ))) ;
		$taux_tva_ticket = $taux_tva . "%";
		$remise = $data->remise;
        $idproduit = $data->id_produit;

		$commandes[]=$data;
//		$titreTicket = strlen($titre) > 10 ? substr($titre, 0, 7) : $titre;
		$prix_total = $qte * $pu_euro;
        $prix_total_ticket = number_format((float)$prix_total, 2, '.','') . "\xE2\x82\xAc";
        $ttc = $total_a_payer . $totalTTC . "€";
        $ticket_ligne .= setStringLen($qte."*".$pu_euro,$qte_prix_limit) . setStringLen($titre,$designiation_limit) . setStringLen($prix_total_ticket,$mttc_limit) . setStringLen($taux_tva_ticket,$tva_limit) . "\n";
//		$ticket_ligne .= sprintf($ticket_layout, strval($qte) . "*" . strval($pu_euro), $titreTicket, strval($pu_euro) . "€", strval($taux_tva_rounded) . "%") . "\n";

        $sql = "SELECT choix_mode_prix,stock,stock_alerte,num FROM table_client_catalogue WHERE id = '$idproduit' ";
        $query_res = $conn->query($sql);
        $res = $query_res->fetch_row();
        $mode = $res[0];
		$stock = $res[1];
		$stock_alerte = $res[2];
		$num = $res[3];

        $total_euro_du += ($mode == 3 ? $pu_euro * $qte : ($mode == 2 ? ( $pu_euro + ($pu_euro * $taux_tva / 100 )) : $pu_euro * $qte ));

		// MISE A JOUR DU STOCK
		if($stock_alerte < 0 && $stock > 0 ){
			$sql = "UPDATE table_client_catalogue SET stock = stock - $stock , stock_alerte = stock_alerte + $stock_alerte WHERE num = $num";
			$updateCatalogue = $conn->query($sql);
		}

	}
	$ticket .= $ticket_entete . $ticket_corps . $ticket_ligne ."\n" . $ttc . $ticket_pied;
	file_put_contents('ticket.txt', $ticket);

	$retour_article = 0;
	$echange_article = 0;
	$date = date('Y-m-d H:i:s');
	$printTicket = true;

	// RECUPERE LE MONTANT POUR CHAQUE TYPE DE PAIEMENT

	$paiements = $conn->query('SELECT * FROM table_paiement_temp ORDER BY id DESC LIMIT 1');
	if ($paiements) {
		$resultat = $paiements->fetch_assoc();
		$p_espece = $resultat['espece_euro'];
		$p_cb = $resultat['cb_euro'];
		$p_cheque = $resultat['cheques_euro'];
		$p_restaurant = $resultat['ticket_restaurant'];

		$total_euro = $p_espece + $p_cb + $p_cheque + $p_restaurant;


		if($p_espece == 0){
			$printTicket = true; // ON OUVRE PAS LE TIROIR CAISSE SI PAS DE PAIEMENT EN ESPECE
		}
	}
	$sql = "INSERT INTO table_client_ticket (`id_caisse`, `p_cheque_euro`, `p_cb`, `p_espece_euro`, `p_restaurant`,`total_remise`,`deconsigne`, `retourarticle`, `echangearticle`, `total_euro`, `total_euro_du`, `qte_total`,`date`, `sendserveur`,`id_ticket`) VALUES ($id_caisse,$p_cheque,$p_cb,$p_espece,$p_restaurant,$total_remise,$deconsigne,$retour_article,$echange_article,$total_euro,$total_euro_du,$qte_total, '" . $date . "',1,0)";
		if ($conn->query($sql) === TRUE) {
			$last_id = $conn->insert_id;
			$updateTicket = $conn->query("UPDATE table_client_ticket SET id_ticket = $last_id WHERE id = $last_id");
			foreach($commandes as $commande){
				$idproduit = $commande->id_produit;
				$qte = $commande->qte;
				$pu_euro = $commande->pu_euro;
				$taux_tva = $commande->taux_tva;
				$famille = $commande->famille;
				$remise = $commande->remise;
				$d = date('Y-m-d');
				$newCommandes = $conn->query("INSERT INTO `table_client_commandes`(`id_ticket`, `id_caisse`, `id_produit`, `qte`,`pu_euro`, `promo`, `remise`, `taux_tva`, `famille`, `date`, `sendserveur`) 
					VALUES ($last_id,$id_caisse,'$idproduit',$qte,$pu_euro,$qte,$remise,$taux_tva,$famille,'$d',1)");
			}
			
			$resetPaiementTemp = $conn->query('DELETE FROM table_paiement_temp WHERE temp = 1');
			if($resetPaiementTemp){
				try {
					// $connector = null;
					// $connector = new WindowsPrintConnector("Receipt Printer");

					// $printer = new Printer($connector);
					if ($printTicket == true) {
						// $printer -> text($ticket);
						// $printer -> cut();
						// $printer->pulse();
						echo json_encode(array('response' => 1, 'message' => 'IMPRIME TICKET + OUVERTURE TIROIR CAISSE'));
						exit;
					} else {
						// $printer -> text($ticket);
						// $printer -> cut();
						echo json_encode(array('response' => 1, 'message' => 'IMPRIME TICKET SEULEMENT'));
						exit;
					}

					// $printer->close();
				} catch (Exception $e) {
					echo json_encode("Impossible d'imprimer sur cette imprimante: " . $e->getMessage() . "\n");
					exit;
				}
			}
			else{
				echo json_encode(array('response' => 0, 'message' => 'ERREUR RESET PAIEMENT'));
				exit;
			}

		} else {
			echo json_encode(array('response' => 0, 'message' => 'ERREUR INSERE TICKET'));
			exit;
		}
	}
