<?php 
require("src/php/function/db.php"); // connexion base de données
session_start();
if(!isset($_SESSION['pseudo']) OR !isset($_SESSION['roomname']) OR $_SESSION['roomname'] == "none")
{
	header("Location: ./index.php");
	exit();
}

$req_player2_pseudo = $db->query("SELECT pseudo FROM player JOIN room WHERE player.pseudo != '" . $_SESSION['pseudo'] . "' AND (player.id = room.player1 OR player.id = room.player2)");
$data_player2_pseudo = $req_player2_pseudo->fetch();
$player2_pseudo = $data_player2_pseudo['pseudo'];
?>
<!DOCTYPE html>
<html>
	<head>
	</head>

	<body>
		<!-- SCRIPT -->		
			<script src="src/js/manage_room.js"></script>
			<script>
				refresh();
			</script>
		<!-- SCRIPT -->

		<!-- BODY -->
			<div style= "float: left; border:solid;">
				<h1 > <?php echo $_SESSION['pseudo']; ?></h1>
				<p  >Vie : <span id="life_me"></span></p>
			</div>
			<div style= "float: right; border:solid;">
				<h1 > <?php echo $player2_pseudo; ?></h1>
				<p  >Vie : <span id="life_him" style= "float: right"></span> </p>
			</div>
			<h1 style= " text-align: center;">Room nb : <?php echo $_SESSION['roomname']; ?></h1>
			<h1 id="test_room" style="text-align: center;"></h1>
			<button onclick="leave('button')">Leave !</button>
			<button onclick="operation('attaquer')">Attaquer</button>
			<!-- hidden element -->
				<button id="redirect_button" style="display: none;" onclick="window.location = 'index.php';">Revenir à l'ecran principal</button>
			<!-- hidden element -->
		<!-- BODY -->
	</body>
</html>