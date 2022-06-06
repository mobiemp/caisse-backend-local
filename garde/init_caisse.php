<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include('../functions.php');
include ('../parametre.php');
include('../DBConfig.php');


$garde = 0.50;
$connexion_locale = $conn;
//$connexion_serveur = remoteConnexion($ip_serveur, $user, $password,$usebdd);

$today_date = date('Y-m-d');
// On recupere tous les tickets du mois sur la caisse.
$sql = 'SELECT * FROM `table_client_ticket` WHERE DATE(date) >= (DATE(NOW()) - INTERVAL 30 DAY )
        AND p_espece_euro != 0
        AND p_cheque_euro = 0
        AND p_cb = 0
        AND p_restaurant = 0
        ';
$query_res = $connexion_locale->query($sql);
$tickets_caisse = $query_res->fetch_all(MYSQLI_ASSOC);
// On recupere les tickets du mois sur le serveur.
//$query_res = $connexion_serveur->query($sql);
//$tickets_serveur = $query_res->fetch_assoc();



$commandes_caisse = array();
foreach($tickets_caisse as $ticket){
    $id_ticket = $ticket['id_ticket'];
    $sql = "SELECT id_ticket,pu_euro,id,qte FROM table_client_commandes WHERE id_ticket = $id_ticket ORDER BY pu_euro DESC";
    $query_res = $connexion_locale->query($sql);
    $nbLigne = $query_res->num_rows;
    while($ligne = $query_res->fetch_assoc()){
        $commandes_caisse[$ticket['id_ticket']]['nbligne'] = ceil($nbLigne*$garde);
        $commandes_caisse[$ticket['id_ticket']]['commandes']['commandes-'.$ligne['id']] = $ligne['pu_euro'] * $ligne['qte'];
    }
}

foreach($commandes_caisse as $key_commande => $commande){
    $slice = array_slice($commande['commandes'],0,$commande['nbligne']);
    if(count($slice)>0){
        foreach($slice as $key => $value){
            // RECUPERE 1 PRODUIT AU HASARD QUI COUTENT MOINS DE 3 EUROS
            $sql = 'SELECT id,prixttc_euro,code_tva,cath,prixttc_promo_euro FROM `table_client_catalogue` WHERE prixttc_euro < 3 ORDER BY RAND() LIMIT 1';
            $query_res = $connexion_locale->query($sql);
            $produits = $query_res->fetch_row();

            $id_produit = $produits[0];
            $prixttc_euro = $produits[1];
            $taux_TVA = ($produits[2] == 8 ? 8.5 : ($produits[2] == 2 ? 2.1 : ($produits[2] == 1 ? 1.05 : 0 )));
            $famille = $produits[3];
            $promo = $produits[4];

            $splited_key = explode('-',$key);
            $id = $splited_key[1];
//            $new_value = mt_rand(0.5 * 10, 1.5 * 10) / 10;
            $sql = "UPDATE table_client_commandes SET pu_euro = $prixttc_euro, id_produit = '$id_produit', taux_tva  = $taux_TVA, famille = $famille, promo = $promo WHERE id = $id";
            $query_res = $connexion_locale->query($sql);
            echo $query_res == true ? "MAJ Réussi" : "FAIL";
            echo '</br>';
        }
    }
//    else if(count($slice) == 1 ){
//        $clé = array_keys($slice)[0];
//        $splited_key = explode('-',$clé);
//        $id = $splited_key[1];
//        $new_pu_euro = array_values($slice)[0] * $garde;
//        $sql = "UPDATE table_client_commandes SET pu_euro = $new_value WHERE id = $id";
//        $query_res = $connexion_locale->query($sql);
//    }
    $sql = "SELECT sum(pu_euro)*qte as total FROM table_client_commandes WHERE id_ticket = $key_commande";
    $query_res = $connexion_locale->query($sql);
    $resultat = $query_res->fetch_row();
    $total_euro_du = $resultat[0];
    // MISE A JOUR DES TICKETS PAYEES EN ESPECES
    $sql = "UPDATE table_client_ticket SET total_euro = total_euro - $total_euro_du,total_euro_du = $total_euro_du, p_espece_euro = p_espece_euro - $total_euro_du WHERE id_ticket  = $key_commande";
    $query_res = $connexion_locale->query($sql);
    echo $query_res == true ? "Success" : 'Fail';
    echo '</br>';
}

