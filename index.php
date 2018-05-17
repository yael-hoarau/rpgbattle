<?php
session_start(); //lancement de la session
require("src/php/function/db.php"); // connexion base de données
require("src/php/function/security.php");
include('bot.php');


if(test_ban($_SERVER['REMOTE_ADDR'], $db))
{
	header('Location: http://bfy.tw/D11J');
	exit();
}

if(isset($_SESSION['disconnect'])) // si le joueur a été redirigé après une déconnexion non volontaire
    $_SESSION['disconnect'] = false;

//if(isset($_SESSION['id_user']) && isset($_SESSION['id']) )
  //  unset($_SESSION['id']);

if(isset($_SESSION['idroom'])) /* systeme de redirection et de controle de securité */
{
	if($_SESSION['idroom'] != -1)
	{
		header('Location: ./room.php');
		exit();
	}
	else if (!isset($_SESSION['id_user']) && $_SESSION['findstep'] == 1)
	{
		session_destroy();
	}
}								/* systeme de redirection et de controle de securité */
?>
<!DOCTYPE html>
<html>
	<head>
        <link rel="stylesheet" href="src/css/index.css"/>
        <script src="src/js/modernizr-3.5.0.min.js"></script>
        <script src="src/js/jquery-3.2.1.min.js"></script>


	</head>

	<body>
        <h3 id="error_indication"></h3>


        <div id="connected" style="display: none;">
            <div id="infos_user" ></div>
            <form id="form_create_room"></form>
        </div>
        <div id="forms" style="display: block">
            <form id="form_guest" action="connection.php" method="POST"></form>
            <form id="form_connection" action="connection.php" method="POST"></form>
            <form id="form_inscription" action="inscription.php" method="POST" style="display: none"></form>
        </div>
        <table id="leaderboard">
            <tr id="change_score_table_buttons">
                <th id="change_table_score" onclick="change_leaderboard('score')">Classement par score</th>
                <td id="change_table_victory" onclick="change_leaderboard('victory')">Classement par pourcentage de victoire</td>
            </tr>
            <tr>
                <td>Classement</td>
                <td>Pseudo</td>
                <td>Score</td>
            </tr>
            <span id="type_leaderboard" style="display: none">score</span>
        </table>
        <div id="info_stats">
            <table class="table_stat"> <table id="tab_wait" style="display: none"></table> </table>
            <table id="tab_run" style="display: none"></table>
        </div>
        <div id="find">
            <img id="img_gif" style="display: none;" src="src/img/load.gif">
            <h1 id="info_find"></h1>
            <button id="stop_wait" style="display: none"></button>
        </div>
        <span id="is_connected" style="display: none">false</span>

	</body>
    <script src="src/js/find.js"></script>
    <script src="src/js/forms.js"></script>
    <script><?php if($_SESSION['findstep'] == 3) echo 'waitfor();' ?>  </script>

</html>