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
if(!isset($_POST['form']) || isset($_POST['form']) != 'inscription' )
{
    header('Location: ./index.php');
    exit();
}

if(!isset($_POST['mail']) || !filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
    echo 'Veuillez renseigner un mail valide';
    exit();
}
if(!isset($_POST['form']) || !is_corect($_POST['pseudo']))
{
    echo 'Pseudo incorect, n\'utilisez que des chiffres et des lettres, entre 4 et 15 caractères';
    exit();
}
if(!isset($_POST['mdp']) || empty($_POST['mdp'])){
    echo 'Veuillez renseigner un mot de passe';
    exit();
}
if(!isset($_POST['mdp2']) || $_POST['mdp'] !== $_POST['mdp2']){
    echo 'Le mot de passe et la confirmation ne corespondent pas';
    exit();
}
$req_user_exist = $db->prepare("SELECT * FROM user WHERE mail = ?");
$req_user_exist->execute(array($_POST['mail']));

if( $req_user_exist->fetch()){
    echo 'Le mail est déjà utilisé';
    exit();
}

$req_user_exist = $db->prepare("SELECT * FROM user WHERE pseudo = ?");
$req_user_exist->execute(array($_POST['pseudo']));

if( $req_user_exist->fetch()){
    echo 'Le pseudo est déjà utilisé';
    exit();
}

$req_create = $db->prepare("INSERT INTO user (pseudo, mail, mdp, date_signin) VALUES (?, ?, ?, ?)");
$req_create->execute(array($_POST['pseudo'], $_POST['mail'], md5($_POST['mdp']), date('Y-m-d H:i:s')));

if($req_create->rowCount()){
    echo 'user_signed';
    exit();
}
echo 'Erreur dans l\'inscription';