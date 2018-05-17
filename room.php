<?php 
require("src/php/function/db.php"); // connexion base de données
session_start();
if(!isset($_SESSION['id']) OR !isset($_SESSION['idroom']) OR $_SESSION['idroom'] == -1)
{
	header("Location: ./index.php");
	exit();
}

/*$req_player2_pseudo = $db->query("SELECT pseudo FROM player JOIN room WHERE player.id != '" . $_SESSION['id'] . "' AND (player.id = room.player1 OR player.id = room.player2) AND room.id = " . $_SESSION['idroom']);
$data_player2_pseudo = $req_player2_pseudo->fetch();
$player2_pseudo = $data_player2_pseudo['pseudo'];*/
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="src/css/room.css"/>
        <title>RPG battle.io</title>
        <script src="src/js/modernizr-3.5.0.min.js"></script>
        <script src="src/js/jquery-3.2.1.min.js"></script>
        <script src="src/js/jquery-ui.min.js"></script>

        <script src="src/js/visual_effects.js"></script>
        <script src="src/js/manage_room.js"></script>
	</head>

	<body onKeyPress="keypress(event)">
		<!-- SCRIPT -->		
			<script>
				refresh();
			</script>
		<!-- SCRIPT -->

		<!-- BODY -->

			<!-- player 1 -->
			<div id="my_character" class="shadow-pulse">
				<h1 id="my_pseudo"></h1>
				<p>
					Vie : <span id="my_life"></span><br>
					Mana : <span id="my_mana"></span><br>
					Bouclier : <span id="my_shield"></span><br>
				</p>

				<!-- hidden element player 1 -->
			</div>


			<!-- player 2 -->
			<div id="his_character">
				<h1 id="his_pseudo"></h1>
				<p>
					Vie : <span id="his_life" ></span><br>
					Mana : <span id="his_mana" ></span><br>
					Bouclier : <span id="his_shield" ></span><br>
				</p>
				<!-- hidden element player 2 -->
			</div>

			<!-- global elements -->
        <div id="activeBorder" class="active-border">
            <div id="circle" class="circle">
                <span id ="prec" class="prec">0</span>
                <span id="startDeg" class="0"></span>
            </div>
        </div>
            <div id="global_elements">

                <h1 id="aff_global"></h1>
                <div id ="actions">
                    <button onclick="operation('attaquer')">Attaquer</button><br/>
                    <button onclick="operation('bouclier')">Bouclier ( 20 pour 10 mana)</button><br/>
                    <button onclick="operation('passer')">Passer</button><br/>
                </div>
                <button id="capituler" onclick="operation('capituler')">Capituler</button><br/>


                <!-- hidden element global -->
                <button id="redirect_button" onclick="window.location='./index.php';">Revenir à l'accueil</button><br/>
                <span id="indication_turn" style="display: none">C'est au tour de <span id="turn"></span></span>
                <span id="indication" style="display: none"></span>
            </div>

            <!-- chatbox -->
			<div id="chatbox">
				<div id="chat_content">
					<p class="talk"><span>Système: </span> Bienvenue sur le chat !</p>
					<p class="talk"> Vous pouvez entrer un message à envoyer à l'adversaire </p>
				</div>
				<div id="chat_send">
					<input onfocus="this.style.opacity = '1';" onblur="this.style.opacity = '0.3';" type="text" name="chat" id="chat_input"/>
					<button id="chat_button" onclick="click_newchat()">Envoyer</button>
				</div>
                <!-- hidden element chatbox -->
                <span id="background_color_turn" style="display:none">1</span>
			</div>

            <!-- history -->
            <div id="history">
                <span> Affichage de l'historique des actions de la partie : </span>
                <!-- hidden element chatbox -->
                <span id="last_id_history" style="display:none">0</span>
                <span id="history_count" style="display:none">1</span>
            </div>

		<!-- BODY -->
	</body>
</html>