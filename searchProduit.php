<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

require('functions.php');
include 'DBConfig.php';


$postdata = file_get_contents('php://input');

if (isset($postdata)) {
	$request = json_decode($postdata);
	$search = $request->search;
	$check = explode('*',$search);
	if(count($check) == 1 ){
	    $qte = 1;
    }
	else{
        $re = '/[^\*]*/';
        preg_match($re,$search,$matches);
        if(preg_match($re,$search,$matches)){
            if(strlen($matches[0]) > 5){
                echo json_encode(response("",2));
                exit;
            }
        }
        $qte = $matches[0] == "" ? 1 : $matches[0];
        $search = explode('*',$search)[1];
    }
	if (validate_EAN13Barcode($search)) {

		$ref = htmlspecialchars(trim($search));
		$sql = "SELECT * FROM table_client_catalogue WHERE ref = '$ref'";
		// Vérifie l'article scanner existe dans la table catalogue
		if($conn->query($sql)->num_rows > 0){
			$catalogue = $conn->query($sql)->fetch_assoc();
			/* Verifie si deja dans panier */
			$sql2 = "SELECT ref,session FROM table_client_panier WHERE ref=" . $ref;
			$query2 = mysqli_query($conn, $sql2);


			$checkIfInPanier = $conn->query($sql2)->fetch_assoc();
			$ref =  $catalogue['ref'];
			$pu_euro = $catalogue['prixttc_euro'];
			$num = $catalogue['num'];
			$session = $request->session;
			$credit = 0;
			$tva = $catalogue['code_tva'];
			$taux_tva = ($tva == 8 ? 8.5 : ($tva == 2 ? 2.1 : ($tva == 1 ? 1.05 : 0)));
			$remise = 0;
//			$qte = 1;
			$titre = $catalogue['titre'];
			$date = time();
			$id_produit = $catalogue['id'];
			$sql = "INSERT INTO table_client_panier (`session`,`id_produit`,`ref`, `qte`, `credit`, `pu_euro`, `promo`, `retour`, `famille`, `titre`, `taux_tva`,`date`, `remise`) VALUES ($session,'" . $id_produit . "','" . $ref . "', $qte, 0 , $pu_euro, 0, 'false',0,'" . $titre . "',$taux_tva,$date, $remise)";
				if (!$checkIfInPanier) {
					$ajout = $conn->query($sql);
				} else {
					if($session == $checkIfInPanier['session']){
						$sql = "UPDATE table_client_panier SET qte = qte + $qte WHERE ref=" . $ref;
						$update = $conn->query($sql);
					}
					else{
						$ajout = $conn->query($sql);
					}

				}
				$reponse = 1;
			}
			else{
				// echo json_encode(response('Ce produit n\'est pas enregistré dans le catalogue',$ref,0));
				$reponse = 0;
			}

			$sql = "SELECT * FROM table_client_panier";

			$result = $conn->query($sql);

			if ($result->num_rows > 0) {

				while ($row[] = $result->fetch_assoc()) {

					$tem = $row;

					$json = $tem;
				}
				echo json_encode(response($json,$reponse));

				$fp = fopen('jsons/panier.json', 'w');
				fwrite($fp, json_encode($json));
				fclose($fp);
			}
			else{
				$json = '{}';
				echo json_encode(response(json_decode($json),$reponse));
				$fp = fopen('jsons/panier.json', 'w');
				fwrite($fp, json_encode(json_decode($json)));
				fclose($fp);
			}
			// Fin vérification article dans table catalogue
		}

	    else if(preg_match("/^\d+$/", $search)) {

			$ref = htmlspecialchars(trim($request->search));
			$sql = "SELECT * FROM table_client_catalogue WHERE ref = '$ref'";
			$query = $conn->query($sql);
			$nbligne = $query->num_rows;
			if ($nbligne == 0 and strlen("$ref") == 13) {
				$ref_1 = substr($ref, 0, 7) . "00000";

				$res_sql = mysqli_query($conn, "SELECT * FROM table_client_catalogue WHERE ref='$ref_1'");
				$nbligne2 = mysqli_num_rows($res_sql);
			// echo json_encode("SELECT * FROM table_client_catalogue WHERE ref='$ref_1'");die();

				if ($nbligne2 > 0) {
					$ref_2 = substr($ref, 7, 5);
					$row = $res_sql->fetch_assoc();
					$pv_valeur = $row['prix_variable'];

					if ($pv_valeur == 0) $nbligne2 = 0;
					if ($pv_valeur == 1) $pv_prix_euro = round($ref_2 / 100);
					if ($pv_valeur == 2) $pv_prix_euro = round($ref_2 / 100 / 6.55957);

					$sql2 = "SELECT ref FROM table_client_panier WHERE ref=" . $ref_1;
					$query2 = mysqli_query($conn, $sql2);
					$checkIfInPanier = $conn->query($sql2)->fetch_assoc();
					if (!$checkIfInPanier) {
						$ref = $ref_1;
						$pu_euro = $pv_valeur;
						$session = "127.0.0.1/1";
						$credit = 0;
						$tva = $row['code_tva'];
						$taux_tva = ($tva == 8 ? 8.5 : ($tva == 2 ? 2.1 : ($tva == 1 ? 1.05 : 0)));
						$remise = 0;
						$qte = 1;
						$titre = $row['titre'];
						$date = time();
						$id_produit = $row['id'];
						$sql = "INSERT INTO table_client_panier (`session`,`id_produit`,`ref`, `qte`, `credit`, `pu_euro`, `promo`, `retour`, `famille`, `titre`, `taux_tva`,`date`, `remise`) 
						VALUES ('" . $session . "','" . $id_produit . "','" . $ref . "', $qte, 0 , $pu_euro, 0, 'false',0,'" . $titre . "',$taux_tva,$date, $remise)";
							$ajout = $conn->query($sql);
						} else {
							$sql = "UPDATE table_client_panier SET qte = qte + 1 WHERE ref=" . $ref_1;
							$update = $conn->query($sql);
						}
					}
				}

				$sql = "SELECT * FROM table_client_panier";

				$result = $conn->query($sql);

				if ($result->num_rows > 0) {

					while ($row2[] = $result->fetch_assoc()) {

						$tem = $row2;

						$json = $tem;
					}

					echo json_encode($json);

					$fp = fopen('jsons/panier.json', 'w');
					fwrite($fp, json_encode($json));
					fclose($fp);
				}
			}
	    else{
	        echo json_encode(response("",2));
        }
//			else{
//				$sql = "SELECT * FROM table_client_catalogue WHERE titre LIKE '%$search%' ";
//				$result = $conn->query($sql);
//				if($result->num_rows > 0){
//					while($row[] = $result->fetch_assoc()){
//						$item = $row;
//						$json = $item;
//					}
//					echo json_encode($json);
//				}
//				else{
//					echo 0;
//				}
//			}
		}
