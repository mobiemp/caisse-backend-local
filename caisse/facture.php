<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include '../DBConfig.php';

// EN LOCAL
include '../infos.php';
include '../tickets/print-ticket.php';
// SUR CONTABO
$postdata = file_get_contents('php://input');
if(isset($postdata)){
	$request = json_decode($postdata);
	if(isset($request->numero_ticket)){
		$numero_ticket = htmlspecialchars($request->numero_ticket);
		$client = htmlspecialchars($request->client);
		$sql = "SELECT ct.titre as titre,c.pu_euro as pu_euro, c.qte as qte,c.remise as remise, c.promo as promo,c.taux_tva as taux_tva FROM table_client_commandes c INNER JOIN table_client_catalogue ct ON c.id_produit = ct.id WHERE id_ticket = $numero_ticket ;";
		$commandes = $conn->query($sql);

		$prix_total_ticket = 0;
		$totalTTC = 0;
		$totalHT = 0;
		$totalTVA8 = 0;
		$totalTVA1 = 0;
		$totalTVA2 =0;
		$totalTVA1 = 0;
		$ticket_ligne = "";
		$ticket = "";
		$retour_article = 0;

		$ticket_info = $conn->query("SELECT date,total_euro,total_euro_du,p_espece_euro,p_cb,p_cheque_euro FROM table_client_ticket WHERE id_ticket = $numero_ticket");
		$ticket_info = $ticket_info->fetch_assoc();
		$date = $ticket_info['date'];
		$total_euro = $ticket_info['total_euro'];
		$total_euro_du = $ticket_info['total_euro_du'];
		$arendre = $total_euro > $total_euro_du ? $total_euro - $total_euro_du : 0;
		$p_espece = $ticket_info['p_espece_euro'];
		$p_cb = $ticket_info['p_cb'];
		$p_cheque = $ticket_info['p_cheque_euro'];
		
		if($commandes->num_rows>0){
			while($row = $commandes->fetch_assoc()){

				$titre = $row['titre'];
				$qte = $row['qte'];
				$pu_euro = $row['pu_euro'];
				$prix = $pu_euro * $row['qte'];
				$prix_total_ticket+=$prix;
				$taux_tva = $row['taux_tva'];
				$taux_tva_ticket = $taux_tva . "%";
				$remise = $row['remise'];
				$promo = $row['promo'] ;


				$ligne_ticket = setStringLen($titre, $designiation_limit);
				$ligne_prix_commande = setStringLen($prix_total_ticket, $mttc_limit, true);
				$ligne_tva = setStringLen($taux_tva_ticket, $tva_limit);
				$pu_euro = $row['promo'] > 0 ? $promo : $pu_euro;

				$ligne_qte = setStringLen($qte . "*" . $pu_euro, $qte_prix_limit);
				$ticket_ligne .= $ligne_qte . $ligne_ticket . "  " .$ligne_prix_commande . " " . $ligne_tva. "\n";


				// if($remise_pourcent > 0 ){
				// 	$ticket_ligne .= str_repeat(" ",4).setStringLen("Remise de (-".$remise_pourcent."%)",$designiation_limit)." ";
				// 	$ticket_ligne.= setStringLen("-".formatNumber(($pu_euro * ($remise_pourcent / 100) * $qte)), $mttc_limit, true);

				// 	$ticket_ligne .= "\n";
				// }
				if($remise>0){
					$ticket_ligne .= str_repeat(" ",12).setStringLen("Remise",$designiation_limit).str_repeat(" ",12);
					$ticket_ligne.= setStringLen("-".formatNumber(($promo * $qte - $remise)), $mttc_limit, true);
					$ticket_ligne .= "\n";
				}
				elseif($promo>0){
					if($remise>0){
						$ticket_ligne .= str_repeat(" ",3).setStringLen("Remise".$remise."",$designiation_limit-1)." ";
						$ticket_ligne .= str_repeat(" ",12).setStringLen("Remise",$designiation_limit).str_repeat(" ",12);
						$ticket_ligne.= setStringLen("-".formatNumber(($promo * $qte - $remise)), $mttc_limit, true);
					}
					//elseif($remise_euro>0){
						//$ticket_ligne .= str_repeat(" ",12).setStringLen("Remise ",9).str_repeat(" ",12);
						//$ticket_ligne.= setStringLen("-".formatNumber(($promo * $qte - $remise_euro)), $mttc_limit, true);
					//}
					else{
						$ticket_ligne .= str_repeat(" ",12).setStringLen("Remise",9)." ";
						$ticket_ligne .= setStringLen("-".formatNumber($promo-$remise), $mttc_limit, true);
					}
					$ticket_ligne .= "\n";
					//$ligne_ticket .= str_repeat(" ",4).setStringLen("Remise".formatNumber($promo),$designiation_limit).str_repeat(" ",12);
					//$ticket_ligne .= setStringLen(formatNumber($prix_total_ticket-$promo), $mttc_limit, true);
				}
				//else{
					//$ticket_ligne .= str_repeat(" ",12).setStringLen("Remise",9)." ";
					//$ticket_ligne .= setStringLen("-".formatNumber($prix_total_ticket), $mttc_limit, true);
				//}

				$totalTTC += $remise > 0 ? $pu_euro * $qte - $remise : $pu_euro * $qte;
				$totalHT += $remise > 0 ? (($pu_euro - $remise) / (1+($taux_tva/100)))*$qte : ($pu_euro / (1+($taux_tva/100)))*$qte;

				if($taux_tva == 8.5){

					if($remise>0){

						$prix_apres_remise = $pu_euro - $remise;
						$montant_tva = $prix_apres_remise - ($prix_apres_remise/(1+$taux_tva/100));
						$totalTVA8 += $montant_tva * $qte;
					}
					else{
						$totalTVA8 += ($pu_euro -($pu_euro / (1+$taux_tva/100))) * $qte;
					}
				}
				if($taux_tva == 2.1){

					if($remise>0){
						$prix_apres_remise = $pu_euro - $remise;
						$montant_tva = $prix_apres_remise - ($prix_apres_remise/(1+$taux_tva/100));
						$totalTVA2 += $montant_tva * $qte;
					}
					else{
						$totalTVA2 += ($pu_euro -($pu_euro / (1+$taux_tva/100))) * $qte;
					}
				}
				if($taux_tva == 1.05){

					if($remise>0){
						$prix_apres_remise = $pu_euro - $remise;
						$montant_tva = $prix_apres_remise - ($prix_apres_remise/(1+$taux_tva/100));
						$totalTVA1 += $montant_tva * $qte;
					}
					else{
						$totalTVA1 += ($pu_euro -($pu_euro / (1+$taux_tva/100))) * $qte;
					}
				}
			}

			$numero_ticket = $numero_ticket;
			$ticket .= $ticket_entete
			.$ticket_corps
			.$ticket_ligne
			."\n"
			.$separator;


			$totalTTC = formatNumber($totalTTC);
			$totalHT = formatNumber($totalHT);
			$totalTVA8 = formatNumber($totalTVA8);
			$totalTVA2 = formatNumber($totalTVA2);
			$totalTVA1 = formatNumber($totalTVA1);
			$total_a_payer = str_repeat(" ", 10) ."TOTAL A PAYER TTC". str_repeat(" ",4).setStringLen($totalTTC,6)." EUR"; ;
			$total_ht = str_repeat(" ", 19) ."TOTAL HT". str_repeat(" ",4).setStringLen($totalHT,6)." EUR";
			$total_tva8 = str_repeat(" ", 19) ."TVA 8.5%". str_repeat(" ",4).setStringLen($totalTVA8,6)." EUR";
			$total_tva2 = str_repeat(" ", 19) ."TVA 2.1%". str_repeat(" ",4).setStringLen($totalTVA2,6)." EUR";
			$total_tva1 = str_repeat(" ", 19) ."TVA 1.05%". str_repeat(" ",4).setStringLen($totalTVA1,6)." EUR";

			$ticket_part2 = $total_a_payer."\n";
			$ticket_part2 .= $total_ht ."\n";

			if($totalTVA8 != 0){
				$ticket_part2 .= $total_tva8 ."\n";
			}
			if($totalTVA2 != 0){
				$ticket_part2 .= $total_tva2 ."\n";
			}
			if($totalTVA1 != 0){
				$ticket_part2 .= $total_tva1."\n";
			}


			$header_ticket = "FACTURE T".$id_caisse."W".$numero_ticket;
			$footer_ticket = "T".$id_caisse."W".$numero_ticket." - ".date('d/m/Y H:i:s',strtotime($date));

			echo json_encode(array('response' => 1, 'ticket' => $ticket,'ticket_part2'=>$ticket_part2,"header"=>$header_ticket,"arendre"=>$arendre,"espece"=>$p_espece,"cb"=>$p_cb,"cheque"=>$p_cheque ,"ticket_pied"=>$ticket_pied,"footer"=>$footer_ticket,'session' => 99, 'id_caisse' => $id_caisse));

		}
	}

}
