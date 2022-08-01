yarn e26<?php
error_reporting(E_ERROR | E_PARSE);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
include 'parametre.php';
include 'functions.php';
include 'DBConfig.php';


$HostName = $ip_serveur;
$DatabaseName = "mobipos";
$HostUser = "root";
$HostPass = "";
$response = [];
try {
    $connServeur = new mysqli($HostName, $HostUser, $HostPass, $DatabaseName);
    if ($connServeur->connect_errno) {
      $response['response'] = 404;
    }else{
       $response['response'] = 200; 
    }

} catch (mysqli_sql_exception $e) {
    $response['response'] = 404;
    $response['error'] = $e;
    echo json_encode($response);
    exit;
}


if ($response['response'] == 200) {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        if ($action == 'update') {
            if ($mode_serveur != 1) {
                $catalogue = $connServeur->query('SELECT * from table_client_catalogue WHERE send_web = 1');
                $nbligne = $catalogue->num_rows;

                // $connServeur = connexionDb($ip_serveur, 'root', '', 'mobipos');
                if ($nbligne > 0) {
                    while ($row = $catalogue->fetch_assoc()) {
                        $famille = $row['cath'];
                        $id_produit = $row['id'];
                        $gencode = $row['ref'];
                        $designation = $row['titre'];
                        $prix = $row['prixttc_euro'];
                        $promottc = $row['prixttc_promo_euro'];
                        $codetva = $row['code_tva'];
                        $promo_debut = $row['promo_debut'];
                        $promo_fin = $row['promo_fin'];
                        $mode = $row['choix_mode_prix'];
                        $mode_prix_1_achat = $row['mode_prix_1_achat_ht'];
                        $marge = $row['mode_prix_1_marge'];
                        $mode_prix_2 = $row['mode_prix_2_fixe_ht'];
                        $mode_prix_3 = $row['mode_prix_3_fixe_ttc'];
                        $dateajout = $row['dateajout'];
                        $datemodif = $row['datemodif'];
                        $stock_actuel = $row['stock'];
                        $stock_alerte = $row['stock_alerte'];
                        $unite = $row['unite'];
                        $package = $row['package'] == '' ? 'null' : $row['package'];
                        $img = $row['img'] == '' ? 'null' : $row['img'];
                        $quantite = $row['qte_unite'] == '' ? 'null' : $row['unite'];
                        $prix_variable = $row['prix_variable'];
                        $checkIfExist = $conn->query("SELECT ref FROM table_client_catalogue WHERE ref = '$gencode' ");
                        if($checkIfExist){
                            if($checkIfExist->num_rows > 0){
                             $updateCaisse = $conn->query('UPDATE table_client_catalogue SET `cath` = $famille ,`id` = $id_produit,`ref` = $gencode,`titre` = $designation,`prixttc_euro` = $prix,`prixttc_promo_euro` = $promottc,`code_tva` = $codetva,`promo_debut` = $promo_debut,`promo_fin` = $promo_fin,
                              `choix_mode_prix` = $mode,`mode_prix_1_achat_ht` = $mode_prix_1_achat,`mode_prix_1_marge` = $marge,`mode_prix_2_fixe_ht` = $mode_prix_2,`mode_prix_3_fixe_ttc` = $mode_prix_3,`dateajout` = $dateajout,`datemodif` = $datemodif,`accueil` = 0,`stock` = $stock_actuel,`stock_alerte` = $stock_alerte,`unite` = $unite,`qte_unite` = $quantite,`package` = $package,`prix_variable` = $prix_variable,`img` = $img,`send_web` = 0'); 
                         }
                     }
                     $sql = "INSERT INTO table_client_catalogue(`cath`,`id`,`ref`,`titre`,`prixttc_euro`,`prixttc_promo_euro`,`code_tva`,`promo_debut`,`promo_fin`,`choix_mode_prix`,`mode_prix_1_achat_ht`,`mode_prix_1_marge`,`mode_prix_2_fixe_ht`,`mode_prix_3_fixe_ttc`,`dateajout`,`datemodif`,`accueil`,`stock`,`stock_alerte`,`unite`,`qte_unite`,`package`,`prix_variable`,`img`,`send_web`)
                     VALUES($famille,'$id_produit','$gencode','$designation',$prix,$promottc,$codetva,'$promo_debut','$promo_fin',$mode,$mode_prix_1_achat,$marge,$mode_prix_2,$mode_prix_3,'$dateajout','1000-01-01 00:00:00',0,$stock_actuel,$stock_alerte,$unite,$quantite,$package,$prix_variable,$img,0)";
                     $insert = $conn->query($sql);
                     if ($insert) {
                        $num = $row['num'];
                        $updateServeur = $connServeur->query("UPDATE table_client_catalogue SET send_web = 0 WHERE num = $num ");
                    }
                        // $response['message'] = "MISE A JOUR DU CATALOGUE EN COURS";
                        // $response['result'] = 1;
                }

            }
            // MISE A JOUR TICKET
            $tickets = $conn->query('SELECT * FROM table_client_ticket WHERE sendserveur = 1');
            $nbligne = $tickets->num_rows;


            if($nbligne > 0 ){
                while($ticket = $tickets->fetch_assoc()){
                    $id_caisse = $ticket['id_caisse'];
                    $p_cheque_euro = $ticket['p_cheque_euro'];
                    $p_espece_euro = $ticket['p_espece_euro'];
                    $p_cb = $ticket['p_cb'];
                    $p_restaurant = $ticket['p_restaurant'];
                    $total_remise = $ticket['total_remise'];
                    $deconsigne = $ticket['deconsigne'];
                    $retourarticle = $ticket['retourarticle'];
                    $echangearticle = $ticket['echangearticle'];
                    $total_euro = $ticket['total_euro'];
                    $total_euro_du = $ticket['total_euro_du'];
                    $qte_total = $ticket['qte_total'];
                    $date = $ticket['date'];
                    $id_ticket = $ticket['id_ticket'];

                    $checkIfExist = $connServeur->query('SELECT * FROM table_client_ticket WHERE id_ticket = $id_ticket');

                    if ($checkIfExist) {
                        if($checkIfExist->num_rows > 0){
                            $updateCaisse = $conn->query("
                                UPDATE `table_client_ticket` SET 
                                `id_caisse`=$id_caisse,
                                `p_cheque_euro`=$p_cheque_euro,
                                `p_cb`=$p_cb,
                                `p_espece_euro`=$p_espece_euro,
                                `p_restaurant`=$p_restaurant,
                                `total_remise`=$total_remise,
                                `deconsigne`=$deconsigne,
                                `retourarticle`=$retourarticle,
                                `echangearticle`=$echangearticle,
                                `total_euro`=$total_euro,
                                `total_euro_du`=$total_euro_du,
                                `qte_total`=$qte_total,
                                `date`='$date',
                                `sendserveur`= 0,
                                `id_ticket`= $id_ticket 
                                WHERE id_ticket = $id_ticket
                                ");
                        }
                    }
                    $sql = "INSERT INTO `table_client_ticket`(`id_caisse`, `p_cheque_euro`, `p_cb`, `p_espece_euro`, `p_restaurant`, `total_remise`, `deconsigne`, `retourarticle`, `echangearticle`, `total_euro`, `total_euro_du`, `qte_total`, `date`, `sendserveur`, `id_ticket`) VALUES ($id_caisse,$p_cheque_euro,$p_cb,$p_espece_euro,$p_restaurant,$total_remise,$deconsigne,$retourarticle,$echangearticle,$total_euro,$total_euro_du,$qte_total,'$date',0,$id_ticket)";
                    $insert = $connServeur->query($sql);
                    if($insert){
                        $id = $ticket['id'];
                        $updateCaisse = $conn->query('UPDATE table_client_ticket SET sendserveur = 0 WHERE id = $id');
                    }
                }
            }

            $response['message'] = $nbligne;
            $response['result'] = 0;
        }
    }
}
}
echo json_encode($response);

//        $result ='';
//        if($mode_caisse == 1) {
//            $catalogue = $conn->query('SELECT * from table_client_catalogue WHERE send_web = 0');
//            $nbligne = $catalogue->num_rows;
//            $connServeur = updateCaisse('localhost','root','','mobipos');
//            $result = $nbligne > 0 ? 'MISE A JOUR DU CATALOGUE EN COURS' : '';
//            if($nbligne>0){
//                while ($row = $catalogue->fetch_assoc()) {
//                    $famille = $row['cath'];
//                    $id_produit = $row['id'];
//                    $gencode = $row['ref'];
//                    $designation = $row['titre'];
//                    $prix = $row['prixttc_euro'];
//                    $promottc = $row['prixttc_promo_euro'];
//                    $codetva = $row['code_tva'];
//                    $promo_debut = $row['promo_debut'];
//                    $promo_fin = $row['promo_fin'];
//                    $mode = $row['choix_mode_prix'];
//                    $mode_prix_1_achat = $row['mode_prix_1_achat_ht'];
//                    $marge = $row['mode_prix_1_marge'];
//                    $mode_prix_2 = $row['mode_prix_2_fixe_ht'];
//                    $mode_prix_3 = $row['mode_prix_3_fixe_ttc'];
//                    $dateajout = $row['dateajout'];
//                    $stock_actuel = $row['stock'];
//                    $stock_alerte = $row['stock_alerte'];
//                    $unite = $row['unite'];
//                    $quantite = $row['qte_unite'];
//                    $prix_variable = $row['prix_variable'];
//                    $insert = $connServeur->query("INSERT INTO table_client_catalogue(`cath`,`id`,`ref`,`titre`,`prixttc_euro`,`prixttc_promo_euro`,`code_tva`,`promo_debut`,`promo_fin`,`choix_mode_prix`,`mode_prix_1_achat_ht`,`mode_prix_1_marge`,`mode_prix_2_fixe_ht`,`mode_prix_3_fixe_ttc`,`dateajout`,`datemodif`,`accueil`,`stock`,`stock_alerte`,`unite`,`qte_unite`,`package`,`prix_variable`,`img`,`send_web`)
//                        VALUES($famille,'$id_produit','$gencode','$designation',$prix,$promottc,$codetva,'$promo_debut','$promo_fin',$mode,$mode_prix_1_achat,$marge,$mode_prix_2,$mode_prix_3,'$dateajout','1000-01-01 00:00:00',0,$stock_actuel,$stock_alerte,$unite,$quantite,'',$prix_variable,'',1)");
//                    if($insert){
//                        $num = $row['num'];
//                        $updateServeur = $conn->query("UPDATE table_client_catalogue SET send_web = 0 WHERE num = $num ");
//                    }
//                }
//            }
//        }


// if ($mode_serveur == 1) {
//     $HostName = $ip_adress;
//     $DatabaseName = "mobipos";
//     $HostUser = "root";
//     $HostPass = "";
//     $conn = new mysqli($HostName, $HostUser, $HostPass, $DatabaseName);
//     $users = $conn->query("SELECT host FROM mysql.user WHERE host != '$ip_adress'");

//     $catalogue = $conn->query('SELECT * from table_client_catalogue WHERE send_web = 0');


//     if ($catalogue->num_rows > 0) {
//         while ($user = $users->fetch_assoc()) {
//             if (filter_var($user['host'], FILTER_VALIDATE_IP)) {
//                 $connServeurs = sendToCaisse($user['host'], 'mobipos', 'root', '');
//                 while ($row = $catalogue->fetch_assoc()) {
//                     $famille = $row['cath'];
//                     $id_produit = $row['id'];
//                     $gencode = $row['ref'];
//                     $designation = $row['titre'];
//                     $prix = $row['prixttc_euro'];
//                     $promottc = $row['prixttc_promo_euro'];
//                     $codetva = $row['code_tva'];
//                     $promo_debut = $row['promo_debut'];
//                     $promo_fin = $row['promo_fin'];
//                     $mode = $row['choix_mode_prix'];
//                     $mode_prix_1_achat = $row['mode_prix_1_achat_ht'];
//                     $marge = $row['mode_prix_1_marge'];
//                     $mode_prix_2 = $row['mode_prix_2_fixe_ht'];
//                     $mode_prix_3 = $row['mode_prix_3_fixe_ttc'];
//                     $dateajout = $row['dateajout'];
//                     $stock_actuel = $row['stock'];
//                     $stock_alerte = $row['stock_alerte'];
//                     $unite = $row['unite'];
//                     $quantite = $row['qte_unite'];
//                     $prix_variable = $row['prix_variable'];
//                     $insert = $connServeurs->query("INSERT INTO table_client_catalogue(`cath`,`id`,`ref`,`titre`,`prixttc_euro`,`prixttc_promo_euro`,`code_tva`,`promo_debut`,`promo_fin`,`choix_mode_prix`,`mode_prix_1_achat_ht`,`mode_prix_1_marge`,`mode_prix_2_fixe_ht`,`mode_prix_3_fixe_ttc`,`dateajout`,`datemodif`,`accueil`,`stock`,`stock_alerte`,`unite`,`qte_unite`,`package`,`prix_variable`,`img`,`send_web`) 
//                     VALUES($famille,'$id_produit','$gencode','$designation',$prix,$promottc,$codetva,'$promo_debut','$promo_fin',$mode,$mode_prix_1_achat,$marge,$mode_prix_2,$mode_prix_3,'$dateajout',null,0,$stock_actuel,$stock_alerte,$unite,$quantite,'',$prix_variable,'',1)");
//                 }
//             }
//         }
//     }
// }





// $res = $conn->query('SELECT * from table_client_ticket');
// while($row = $res->fetch_assoc()){
//     var_dump($row);
// }

// $sqlSource = file_get_contents('your_db_1648405322');

// mysqli_multi_query($sql,$sqlSource);
