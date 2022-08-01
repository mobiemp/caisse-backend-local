<?php

$localIP = getHostByName(getHostName());
include('../parametre.php');


$mysqli = new mysqli("localhost","root","","mobipos");

if ($mysqli -> connect_errno) {//
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

$i = 0;
foreach($ip_caisse as $ip){
// CREATION DE L'UTILISATEUR POUR LE DEVICE
    $sql = "CREATE USER IF NOT EXISTS '$user'@'192.168.1.$ip' IDENTIFIED BY '$password';";
    $create = $mysqli->query($sql);

    $sql = "GRANT ALL PRIVILEGES ON mobipos.* TO '$user'@'192.168.1.$ip'  WITH GRANT OPTION;";
    $grant = $mysqli->query($sql);

    if ($create AND $grant)
    {
       $i++;

    }
}


if($i == count($ip_caisse)){
   exit('Configuration des permissions réussi !');
}
else{
   exit('Erreur lors de la création des utilisateurs');
}

