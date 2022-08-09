<?php

if(isset($_GET['id_caisse']) && isset($_GET['userid'])){
    session_start();
    session_destroy();

    include "dbconfig.php";
    $base_url = $_SERVER['SERVER_NAME'];

    $id_caisse = $_GET['id_caisse'];
    $userid = $_GET['userid'];

    $sql = "DELETE FROM id_caisse_used WHERE id_caisse = $id_caisse AND user_id = $userid";
    $free_caisse = $con->query($sql);
    if($free_caisse){
        $url = "http://".$base_url."/caisse-backend/login/";
        header("Location: $url");
    }


}



