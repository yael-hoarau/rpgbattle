<?php 
session_start();
require("src/php/function/db.php");
require("src/php/function/security.php");
require("src/php/function/test_pseudo.php");

if(test_ban($_SERVER['REMOTE_ADDR'], $db))
{
	header('Location: http://bfy.tw/D11J');
	exit();
}

if(!isset($_POST['form']))
{
	header('Location: ./index.php');
	exit();
}

if($_POST['form'] == 'guest'){
    if(!is_corect($_POST['pseudo']))
    {
        echo 'Pseudo incorect, n\'utilisez que des chiffres et des lettres';
        exit();
    }
    do{
        $pseudo = $_POST['pseudo'] . '#' . mt_rand(1000, 9999);
        $req_pseudo_taken = $db->query("SELECT pseudo FROM player WHERE pseudo ='". $pseudo . "'");
    }while($req_pseudo_taken->fetch());


    $req = $db->query("INSERT INTO player (pseudo, ping, life, mana) VALUES ('" . $pseudo . "', '" . date('Y-m-d H:i:s') . "', 100, 100)");

    $_SESSION['id'] = $db->lastInsertId();
    $_SESSION['pseudo'] = $pseudo;
    $_SESSION['idroom'] = -1;
    $_SESSION['findstep'] = 1;

    echo 'guest_connected';
} else if ($_POST['form'] == 'connection'){
    $data_user_final = new stdClass();
    $data_user_final->info = 'Erreur de connexion';
    $ismail = false;
    if(isset($_POST['pseudo_mail']) && !empty($_POST['pseudo_mail']))
        $ismail = strpos( $_POST['pseudo_mail'], '@') !== false;
    else
        $data_user_final->info = 'Pseudo manquant';

    if (isset($_POST['mdp']) && empty($_POST['mdp'])  && $data_user_final->info != 'Pseudo manquant' )
        $data_user_final->info = 'Mot de passe manquant';

    if($ismail)
        $req_user = $db->prepare("SELECT * FROM user WHERE mail = ? AND mdp = ? ");
    else
        $req_user = $db->prepare("SELECT * FROM user WHERE pseudo = ? AND mdp = ? ");

    $req_user->execute(array($_POST['pseudo_mail'],  md5($_POST['mdp'])));

    if($data_user = $req_user->fetch()){
        $_SESSION['id_user'] = $data_user['id'];
        header('Location:isConnected.php');
        /*$_SESSION['pseudo'] = $data_user['pseudo'];
        $_SESSION['mail'] = $data_user['mail'];
        $_SESSION['mdp'] = $data_user['mdp'];
        $_SESSION['score'] = $data_user['score'];
        $_SESSION['nb_game'] = $data_user['nb_game'];
        $_SESSION['date_signin'] = $data_user['date_signin'];
        $_SESSION['idroom'] = -1;
        $_SESSION['findstep'] = 1;

        $data_user_final->mail = $_SESSION['mail'];
        $data_user_final->pseudo = $_SESSION['pseudo'];
        $data_user_final->score = $_SESSION['score'];
        $data_user_final->nb_game = $_SESSION['nb_game'];
        $data_user_final->date_signin = $_SESSION['date_signin'];
        $data_user_final->info = 'user_connected';*/

    }
    else
        if($data_user_final->info != 'Pseudo manquant' && $data_user_final->info != 'Mot de passe manquant')
            $data_user_final->info = 'Pseudo/Mail et/ou mot de passe erroné(s)';

    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');

    echo json_encode($data_user_final);
}




?>