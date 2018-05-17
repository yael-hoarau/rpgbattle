<?php
session_start();
require("src/php/function/db.php");

if(!isset($_SESSION['id_user']) || !isset($_POST['id_room'])){
    echo 'Vous n\'avez pas les droits pour cette action';
    exit();
}

function isPresent($id, $db)
{
    $req_ping = $db->prepare("SELECT ping FROM player WHERE id = ?");
    $req_ping->execute(array($id));
    $data_ping = $req_ping->fetch();
    $current_date = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s'));
    date_sub($current_date, date_interval_create_from_date_string('10 seconds'));
    $ping_date = date_create_from_format('Y-m-d H:i:s', $data_ping['ping']);
    return  $ping_date > $current_date;
}

$req_room = $db->prepare("SELECT * FROM room WHERE id= ?" );
$req_room->execute(array($_POST['id_room']));
if(!( $data_room = $req_room->fetch())){
    echo 'Cette partie n\'est plus disponible';
    exit();
}

if(!isPresent($data_room['player1'], $db)){
    echo 'Ce joueur n\'est plus disponible';
    exit();
}

if($data_room['player1'] == $_SESSION['id']){
    echo 'Vous ne pouvez pas entrer dans votre propre partie';
    exit();
}

if($data_room['mdp'] != ''){
    if(isset($_POST['mdp']) && !empty($_POST['mdp'])){
        if($_POST['mdp'] == $data_room['mdp']){
            if(isset($_SESSION['id_user']) && !isset($_SESSION['id'])){
                $req = $db->query("INSERT INTO player (pseudo, ping, life, mana) 
            VALUES ('" . $_SESSION['pseudo'] . "', '" . date('Y-m-d H:i:s') . "', 100, 100)");
                $_SESSION['id'] = $db->lastInsertId();
            }
            $_SESSION['idroom'] = $data_room['id'];
            $turn = mt_rand(1, 2) == 1 ? $data_room['player1'] : $_SESSION['id'];
            $db->query("UPDATE room SET state = 'running', player2 = " . $_SESSION['id'] . ", turn = " . $turn .
                ", turn_date = '" . date('Y-m-d H:i:s') . "'  WHERE id = " . $data_room['id']);
            echo 'joinok';
            exit();
        }
        else {
            echo 'Mauvais mot de passe';
            exit();
        }

    }
    echo 'needmdp';
    exit();
}

if($_POST['confirm'] == 'true'){
    if(isset($_SESSION['id_user']) && !isset($_SESSION['id'])){
        $req = $db->query("INSERT INTO player (pseudo, ping, life, mana) 
      VALUES ('" . $_SESSION['pseudo'] . "', '" . date('Y-m-d H:i:s') . "', 100, 100)");
        $_SESSION['id'] = $db->lastInsertId();
    }

    $_SESSION['idroom'] = $data_room['id'];
    $turn = mt_rand(1, 2) == 1 ? $data_room['player1'] : $_SESSION['id'];
    $db->query("UPDATE room SET state = 'running', player2 = " . $_SESSION['id'] . ", turn = " . $turn .
        ", turn_date = '" . date('Y-m-d H:i:s') . "'  WHERE id = " . $data_room['id']);

    echo 'joinok';
    exit();
}
echo'confirm';







