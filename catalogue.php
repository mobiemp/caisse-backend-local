<?php
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
include 'DBConfig.php';
include 'functions.php';

// Create connection
if (isset($_GET['action']) && $_GET['action'] == 'articleList' ) {
	$sql = "SELECT * FROM `table_client_catalogue` ORDER BY `table_client_catalogue`.`dateajout` DESC";
	// var_dump($sql);die();
	$result = $conn->query($sql);

	if ($result->num_rows >0) {

		while($row[] = $result->fetch_assoc()) {
			$tem = $row;

			$json['articles'] = $tem;

		}

	} else {
		echo json_encode("Aucun articles trouvés.") ;
	}
	echo json_encode($json);
	$conn->close();
}


$postdata = file_get_contents('php://input');
if (isset($postdata)) {
	$request = json_decode($postdata);
	if (isset($request->retourArticle)) {
		$ref = $request->retourArticle;
		$sql = "SELECT * FROM table_client_catalogue WHERE ref = '$ref' ";
		$result = $conn->query($sql);
		if($result->num_rows >0){
			$article = $result->fetch_assoc();
			echo json_encode($article);
		}
		else{
			echo 0;
		}

		
		$conn->close();
	}
	if(isset($request->search)){
		$search = $request->search;
		echo json_encode($search);
	}
	if(isset($request->ajoutArticle)){

		$form = $request->ajoutArticle;
		$famille = $request->categorie;
		$unite = $request->unite;
		$mode = $request->mode;
	    $gencode = $form->gencode;
		$designation = $form->designation;
		$codetva = (int) $form->codeTVA;
		$quantite = (float) $form->quantite;
		$prix = (int) $form->prix;
		$promottc = $form->promo;

		if($designation == ""){
			errorResponse('Vous devez entrer un titre.',0);
		}
		if($codetva == ""){
			errorResponse('Vous devez entrer un code de TVA(8.5, 2.1, 1.5).',0);
		}
		if($prix == "0.00" or $prix == "" or !is_numeric($prix)){
			errorResponse('Vous devez entrer un prix.',0);
		}
		if($famille == ""){
			$famille = 0;
		}
		if($unite == ""){
			$unite = 0;
		}
		$id_produit = random_strings(12);
		$mode = $mode == 'prixTTC' ? 3 : 2;
		$mode_prix_2 = $mode == 2 ? $prix :  '0.00';
		$mode_prix_3 = $mode == 2 ? $prix  : '0.00';
		$dateajout =  date('Y-m-d H:i:s');

		$sql = "INSERT INTO table_client_catalogue(`cath`,`id`,`ref`,`titre`,`prixttc_euro`,`prixttc_promo_euro`,`code_tva`,`promo_debut`,`promo_fin`,`choix_mode_prix`,`mode_prix_1_achat_ht`,`mode_prix_1_marge`,`mode_prix_2_fixe_ht`,`mode_prix_3_fixe_ttc`,`dateajout`,`datemodif`,`accueil`,`stock`,`stock_alerte`,`unite`,`qte_unite`,`package`,`prix_variable`,`img`,`send_web`) 
	VALUES($famille,'$id_produit','$gencode','$designation',$prix,$promottc,$codetva,'1000-01-01 00:00:00','1000-01-01 00:00:00',$mode,'0.00','30.00',$mode_prix_2,$mode_prix_3,'$dateajout','1000-01-01 00:00:00',0,99,'-99',$unite,$quantite,'',0,'',1)";
		$insert_article = $conn->query($sql);

		if($insert_article == true){
			successResponse('Article ajouté au catalogue avec succès !',1);
		}
		else{
			errorResponse($sql,0);
		}
		
	}
}
