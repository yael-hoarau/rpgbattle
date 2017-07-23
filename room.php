<?php 
require("src/php/function/db.php"); // connexion base de données
session_start();
if(!isset($_SESSION['id']) OR !isset($_SESSION['roomname']) OR $_SESSION['roomname'] == "none")
{
	header("Location: ./index.php");
	exit();
}

$req_player2_pseudo = $db->query("SELECT pseudo FROM player JOIN room WHERE player.id != '" . $_SESSION['id'] . "' AND (player.id = room.player1 OR player.id = room.player2)");
$data_player2_pseudo = $req_player2_pseudo->fetch();
$player2_pseudo = $data_player2_pseudo['pseudo'];
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="src/css/room.css"/>
        <title>RPG battle.io</title>

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
			<div style= "float: left; border:solid;">
				<h1><?php echo $_SESSION['pseudo']; ?></h1>
				<p>Vie : <span id="life_me"></span></p>
				<!-- hidden element player 1 -->
			</div>

			<!-- player 2 -->
			<div style= "float: right; border:solid;">
				<h1><?php echo $player2_pseudo; ?></h1>
				<p>Vie : <span id="life_him" style= "float: right"></span></p>
				<!-- hidden element player 2 -->
			</div>

			<!-- global element -->
			<h1 id="aff_global" style="text-align: center;"></h1>
			<button onclick="operation('attaquer')">Attaquer</button>
			<div id="chatbox">
				<div id="chat_content">
					<p class="talk"><span>Antidot: </span> Voila le message envoyer ya 2 minutes par antidot mec fait toi plaisir avec le resize de fou que tu te mange la</p>
					<p class="talk"><span>Antidot: </span> Voila le message envoyer ya 2 minutes par antidot mec fait toi plaisir avec le resize de fou que tu te mange la</p>
					<p class="talk"><span>Antidot: </span> Voila le message envoyer ya 2 minutes par antidot mec fait toi plaisir avec le resize de fou que tu te mange la</p>
					<p class="talk"><span>Antidot: </span> Voila le message envoyer ya 2 minutes par antidot mec fait toi plaisir avec le resize de fou que tu te mange la</p>
					<p class="talk"><span>Antidot: </span> Voila le message envoyer ya 2 minutes par antidot mec fait toi plaisir avec le resize de fou que tu te mange la</p>
				</div>
				<div id="chat_send">
					<input onfocus="this.style.opacity = '1';" onblur="this.style.opacity = '0.3';" type="text" name="chat" id="chat_input"/>
					<button id="chat_button" onclick="click_newchat()">Envoyer</button>
				</div>
			</div>
			<!-- hidden element global -->
			<button id="redirect_button" style="display:none;" onclick="window.location='./index.php';">Revenir à l'acceuil</button>
		<!-- BODY -->
	</body>
</html>