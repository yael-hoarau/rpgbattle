<?php
session_start();
require("src/php/function/db.php");
$data_user_final = new stdClass();
$data_user_final->info = 'user_disconnected';
if(isset($_SESSION['id_user'])){
    $req_user = $db->prepare("SELECT * FROM user WHERE id = ?  ");
    $req_user->execute(array($_SESSION['id_user']));
    $data_user = $req_user->fetch();
    $_SESSION['pseudo'] = $data_user['pseudo'];
    $_SESSION['mail'] = $data_user['mail'];
    $_SESSION['mdp'] = $data_user['mdp'];
    $_SESSION['score'] = $data_user['score'];
    $_SESSION['nb_game'] = $data_user['nb_game'];
    $_SESSION['date_signin'] = $data_user['date_signin'];
    $_SESSION['idroom'] = -1;
    //$_SESSION['findstep'] = 1;

    $data_user_final->mail = $_SESSION['mail'];
    $data_user_final->pseudo = $_SESSION['pseudo'];
    $data_user_final->score = $_SESSION['score'];
    $data_user_final->nb_game = $_SESSION['nb_game'];
    $data_user_final->date_signin = $_SESSION['date_signin'];
    $data_user_final->info = 'user_connected';
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

echo json_encode($data_user_final);