<?php
session_start();
require("src/php/function/db.php");
if($_GET['stop'] == 'true'){
    $_SESSION['findstep'] = 1;
    if(isset($_SESSION['mdp_create_room']))
        unset($_SESSION['mdp_create_room']);

    $db->query("DELETE FROM room WHERE player1 = " . $_SESSION['id']);
    unset($_SESSION['id']);
    echo 'stopok';
    exit();
}
if(!isset($_SESSION['id_user']) || !isset($_POST['mdp'])){
    echo 'Vous n\'avez pas les droits requis';
    exit();
}

$db->query("DELETE FROM room WHERE player1 = '" . $_SESSION['id'] . "'");


$_SESSION['mdp_create_room'] = $_POST['mdp'];
$_SESSION['findstep'] = 2;

echo 'ok';

