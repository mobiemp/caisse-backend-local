<?php 	
header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include '../functions.php';
include '../DBConfig.php';
$postdata = file_get_contents('php://input');
if (isset($postdata)) {
    $request  = json_decode($postdata);
//    $clear = $request->clear;
    $session = $request->session;
    $jsons_data = file_get_contents('../jsons/panier.json');
    $json = json_decode($jsons_data);

    foreach ($json as $row){
        if($row->session == $session){
            $id = $row->num;
            $sql = "DELETE FROM table_client_panier WHERE num = $id ";
            $delete = $conn->query($sql);
            var_dump($sql,$delete);
            if($delete){
                $panier = regenerePanier($conn,"SELECT * FROM table_client_panier",'../jsons/panier.json');
//                echo json_encode(array('response'=>1,'result'=>$panier));
//                echo 1;
            }
//            else{
//                echo json_encode(array('response'=>0,'result'=>null));
//            }
        }
    }



//    if ($clear == true) {
//
//        $sql = 'DELETE FROM table_client_panier';
//        if ($conn->query($sql) === TRUE) {
//            echo 1;
//            $fp = fopen('../jsons/panier.json', 'w');
//            fwrite($fp, json_encode([]));
//            fclose($fp);
//        }
//        else{
//            echo 0;
//        }
//
//
//    }
}

 ?>
