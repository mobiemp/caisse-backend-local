<?php

$localIP = getHostByName(getHostName());
include('../parametre.php');
//var_dump($localIP);




$mysqli = new mysqli("localhost","root","","mobipos");

if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

$i = 0;
foreach($ip_caisse as $ip){
// CREATION DE L'UTILISATEUR POUR LE DEVICE
    $sql = "CREATE USER IF NOT EXISTS 'root'@'$ip' IDENTIFIED BY '';";
    $create = $mysqli->query($sql);
    if ($create)
    {
//        exit(mysqli_error($mysqli));
        exit('success');
    }
// GRANT PRIVILEGES
        $sql = "GRANT ALL PRIVILEGES ON mobipos.* TO 'root'@'$ip'  WITH GRANT OPTION;";
    $grant = $mysqli->query($sql);
    if (!$grant or !$grant)
    {
        exit(mysqli_error($mysqli));
    }
    $i++;
}


//if($i == count($ip_caisse)){
//    exit('Configuration des permissions réussi !');
//}
//else{
//    exit('Erreur lors de la création des utilisateurs');
//}
