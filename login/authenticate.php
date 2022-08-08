<?php

if (!isset($_POST['username'], $_POST['password'])) {
    $error = 'Veuillez remplir les champs nom d\'utilisateur et mot de passe !';
} else {
    include ('dbconfig.php');
    if ($stmt = $con->prepare('SELECT id, password,client_id FROM user WHERE username = ?')) {
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $password,$client_id);
            $stmt->fetch();
            if (password_verify($_POST['password'], $password)) {
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['username'];
                $_SESSION['id'] = $id;
                $_SESSION['client_id'] = $client_id;
//                echo 'Welcome ' . $_SESSION['name'] . '!';
                echo json_encode(array('response' => 1, 'message' => 'Connexion réussi !'));
            } else {
                echo json_encode(array('response' => 0, 'message' => 'Nom d\'utilisateur et/ou mot de passe incorrect !'));
            }
        } else {
            echo json_encode(array('response' => 0, 'message' => 'Nom d\'utilisateur et/ou mot de passe incorrect !'));
        }
        $stmt->close();
    }
}


?>
