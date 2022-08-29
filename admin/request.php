<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
include '../DBConfig.php';
include '../functions.php';

$postdata = file_get_contents('php://input');
if(isset($postdata)){
    $request = json_decode($postdata);

    if(isset($request->deleteArticleAdmin)){
        $ref = $request->deleteArticleAdmin;
        $sql = "DELETE FROM table_client_catalogue WHERE ref = '$ref'";
        $delete = $conn->query($sql);
        if($delete){
            echo successResponse('Article supprimé avec succès',1);
        }
        else{
            echo errorResponse('Echec de la suppression de l\'articlee.',0);
        }
    }
    elseif(isset($request->deleteGroup)){
        $ids = $request->deleteGroup;
        $i = 0;
        foreach ($ids as $id){
            $sql = "DELETE FROM table_client_catalogue WHERE num = $id";
            $delete = $conn->query($sql);
            $i++;
        }
        if($i==count($ids)){
            echo successResponse('Article(s) supprimés',1);
        }
        else{
            echo errorResponse('Une erreur c\'est produite ',0);
        }
    }elseif(isset($request->addCatGroup)){
        $ids = $request->addCatGroup;
        $cat = $request->cat;
        $i = 0;
        foreach ($ids as $id){
            $sql = "UPDATE table_client_catalogue SET cath = $cat WHERE num = $id";
            $updateCat = $conn->query($sql);
            $i++;
        }
        if($i==count($ids)){
            echo successResponse('Catégorie mis à jour sur les articles sélectionée ! ',1);
        }
        else{
            echo errorResponse('Une erreur c\'est produite.',0);
        }
    }elseif (isset($request->createCat)){
        $nomcat = htmlspecialchars($request->createCat);
        if($nomcat!= ""){
            $sql = "INSERT INTO `table_client_categorie`( `nomcategorie`, `branche`, `id_categorie`, `id_parent`) VALUES ('$nomcat','0','0','0')";
            $insertCat = $conn->query($sql);
            if ($insertCat){
                $last_id = $conn->insert_id;
                $sql = "UPDATE table_client_categorie SET id_categorie = $last_id WHERE id = $last_id";
                $updateCategorie = $conn->query($sql);
                if($updateCategorie){
                    echo successResponse('Création de la catégorie réussi !',1);
                }
                else{
                    echo errorResponse('Echec de la création de la catégorie.Veuillez réeesayez!',0);
                }
            }
        }
    }
}

 ?>
