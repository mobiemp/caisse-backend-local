<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');


$user = "root";
$password = "";
$usebdd = "mobipos";


$mode_serveur = 0;
$ip_serveur = '192.168.1.15';
$id_caisse = 1;
$ip_caisse = array('15');
// Choisir le type de caisse -- 1 => caisse avec codebarre / 2 => caisse avec produits & categorie sans codebarre
$type_caisse = 1;
$magasin = "C'IDEAL Magasin Art Discount";
$adresse = "Chemin LEFAGUYES La Cocoteraie BAT 3";
$adresse2 = "97440 SAINT ANDRE";
$numero_telephone = "0262 50 18 18";
$siret = "49205116400019";

if(isset($_GET['action']) && $_GET['action'] == 'info_caisse'){
	echo json_encode(array(
		'id_caisse' => $id_caisse,
		'type_caisse' => $type_caisse
	));
}

