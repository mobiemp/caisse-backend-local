<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
include 'DBConfig.php';
include 'functions.php';

$postdata = file_get_contents('php://input');
$response = array();
if (isset($postdata)) {
	$request = json_decode($postdata);
	if (isset($request->article)) {
		$id_produit = $request->article->id;;
		$ref = $request->article->ref;
		$credit = 0;
		$pu_euro = $request->article->prixttc_euro;
		$tva = $request->article->code_tva;
		$taux_tva = ($tva == 8 ? 8.5 : ($tva == 2 ? 2.1 : ($tva == 1 ? 1.05 : 0)));
		$remise = 0;
		$qte = 1;
		$titre = $request->article->titre;
		$date = time();
		// $id_caisse = $request->id_caisse;
		$session = "1";
		$retour = 'false';




		// $query = mysqli_query($conn, "SELECT ref FROM table_client_panier WHERE ref='".$ref."'");

		$sql = "SELECT ref FROM table_client_panier WHERE ref=" . $ref;
		$query = mysqli_query($conn, $sql);
		$panier = $conn->query($sql)->fetch_assoc();

		if ($panier == null) {
			$sql = "INSERT INTO table_client_panier (`session`,`id_produit`, `ref`, `qte`, `credit`, `pu_euro`, `promo`, `retour`, `famille`, `titre`, `taux_tva`,`date`, `remise`) VALUES ('" . $session . "','" . $id_produit . "' ,'" . $ref . "', $qte, 0 , $pu_euro, 0, $retour ,0,'" . $titre . "',$taux_tva,$date, $remise)";
			$conn->query($sql);
		} else {
			$sql = "UPDATE table_client_panier SET qte= qte + 1 WHERE ref=" . $panier['ref'];
			$conn->query($sql);
		}

	} else if (isset($request->articleDivers)) {

		$ref_inconnu = '164750';
        $session = $request->session;
		$id_produit = "#DIVERS";
		$counter = mysqli_query($conn, "SELECT count FROM table_counter WHERE type = 'produit_divers'");
		$row = $counter->fetch_row();
		$count = $row[0];
		$ref = 'articleinconnu' . $ref_inconnu . $count;

		$credit = 0;
		$pu_euro = $request->prixDivers;
		$tva = $request->codeTVA;
		$taux_tva = ($tva == 8 ? 8.5 : ($tva == 2 ? 2.1 : ($tva == 1 ? 1.05 : 0)));
		$remise = 0;
		$qte = 1;
		$titre = "Divers";
		$date = time();
		// $id_caisse = $request->id_caisse;
		$retour = 'false';

		$sql = "INSERT INTO table_client_panier 
    (`session`,`id_produit`, `ref`, `qte`, `credit`, `pu_euro`, `promo`, `retour`, `famille`, `titre`, `taux_tva`,`date`, `remise`) 
    VALUES ('" . $session . "','" . $id_produit . "' ,'" . $ref . "', $qte, 0 , $pu_euro, 0, $retour ,0,'" . $titre . "',$taux_tva,$date, $remise)";
		if ($conn->query($sql) == true) {
			$conn->query("UPDATE table_counter SET count = count + 1 WHERE type = 'produit_divers'");
            $json = regenerePanier($conn,"SELECT * FROM table_client_panier",'jsons/panier.json');
            echo json_encode(array('response' => 1 ,'json' => $json));
            die();
		}
	} else if (isset($request->retourArticle)) {
		$id_produit = $request->retourArticle->id;;
		$ref = $request->retourArticle->ref;
		$id = $request->retourArticle->num;
		$credit = 0;
		$pu_euro = $request->retourArticle->prixttc_euro;
		$tva = $request->retourArticle->code_tva;
		$taux_tva = ($tva == 8 ? 8.5 : ($tva == 2 ? 2.1 : ($tva == 1 ? 1.05 : 0)));
		$remise = 0;
		$qte = 1;
		$titre = 'RET ART: ' . $request->retourArticle->titre;
		$date = time();
		// $id_caisse = $request->id_caisse;
		$session = $request->session;
		$retour = 'true';

		$sql = "SELECT ref,retour FROM table_client_panier WHERE num= '$id' AND retour = $retour ";
		$query = mysqli_query($conn, $sql);
		if ($query->num_rows == 0) {
			$sql = "INSERT INTO table_client_panier (`session`,`id_produit`, `ref`, `qte`, `credit`, `pu_euro`, `promo`, `retour`, `famille`, `titre`, `taux_tva`,`date`, `remise`) VALUES ('" . $session . "','" . $id_produit . "' ,'" . $ref . "', $qte, 0 , -$pu_euro, 0, $retour ,0,'" . $titre . "',$taux_tva,$date, $remise)";
			$conn->query($sql);
		} else {
			$sql = "UPDATE table_client_panier SET qte= qte + 1 WHERE ref=" . $ref;
			$conn->query($sql);
		}

		
	} else if (isset($request->deleteArticle)) {
		$num = $request->deleteArticle;
		$sql = "DELETE FROM table_client_panier WHERE num = $num";
		$delete = $conn->query($sql);
		if ($delete == TRUE) {
			$sql = "SELECT * FROM table_client_panier";
			$query = $conn->query($sql);
			$nbligne = $query->num_rows;
			$response['response'] = $nbligne > 0 ? 1 : 0;
		} else {
			echo json_encode(array('response' => 0, 'message' => 'Échec de la suppresion du fichier'));
		}
	}
	else if(isset($request->ajoutRemise)){
		$remise = $request->ajoutRemise;
		$ref = $request->refRemise;
		$sql = "UPDATE table_client_panier SET remise = $remise WHERE ref = $ref";
		$ajoutRemise = $conn->query($sql);
		if($ajoutRemise){
			echo $remise;
			die();
		}
	}
	else if(isset($request->updateQTE)){
		$qte = $request->updateQTE;
		$ref = $request->refQte;
		$sql = "UPDATE table_client_panier SET qte = $qte WHERE ref = $ref";
		$updateQte = $conn->query($sql);
		if($updateQte){
			echo $qte;
			die();
		}
	}
	else if(isset($request->addPromo)){
	    $promo = $request->addPromo;
	    $totalPanier = $request->totalPanier;
	    $session = $request->session;
	    if($promo < 100){
            $montant_promo = $totalPanier * ($promo/100);
            $titre = "Remise Exceptionnelle";
            $date = time();
            $sql = "INSERT INTO table_client_panier
    (`session`,`id_produit`,`ref`, `qte`, `credit`, `pu_euro`, `promo`, `retour`, `famille`, `titre`, `taux_tva`,`date`, `remise`)
	VALUES ($session,
	        '#promo',
	        '0007',
	        1, 0 ,
	        $montant_promo, 0, 'false',0,'" . $titre . "',0,'" . $date . "', 0)";
            $ajoutPromo = $conn->query($sql);
            if($ajoutPromo){
                $json = regenerePanier($conn,"SELECT * FROM table_client_panier",'jsons/panier.json');
                echo json_encode(array('response' => 1 , 'result' => $json ));
                die();

            }else{
                echo json_encode(array('response' => 0 , 'result' => null ));
                die();
            }

        }
	    else{
	        echo json_encode(array('response' => 0 , 'result' => null ));
	        die();
        }

    }
	

	$sql = "SELECT * FROM table_client_panier";

	$result = $conn->query($sql);
	if ($result->num_rows > 0) {

		while ($row[] = $result->fetch_assoc()) {

			$tem = $row;

			$json = $tem;
		}
		echo json_encode($json);

		$fp = fopen('jsons/panier.json', 'w');
		fwrite($fp, json_encode($json));
		fclose($fp);
	}
	else if(isset($request->deleteArticle) && $response['response'] == 0){
		echo json_encode(array('response' => 0));
		$fp = fopen('jsons/panier.json', 'w');
		fwrite($fp, json_encode([]));
		fclose($fp);
	}
}
