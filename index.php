<?php
session_start(); //lancement de la session

require("src/php/function/db.php"); // connexion base de données

if(isset($_SESSION['roomname'])) /* systeme de redirection et de contole de securité */
{
	if($_SESSION['roomname'] != "none")
	{
		header('Location: ./room.php');
		exit();
	}
	if($_SESSION['roomname'] == "none")
	{
		if($_SESSION['findstep'] == 3 OR $_SESSION['findstep'] == 2)
		{
			$del_room = $db->query("DELETE FROM room WHERE player1 = " . $_SESSION['id'] . " OR player2 = " . $_SESSION['id']);
			$del_user = $db->query("DELETE FROM player WHERE id = " . $_SESSION['id']);
		}
		if($_SESSION['findstep'] == 1)
		{
			$del_user = $db->query("DELETE FROM player WHERE id = " . $_SESSION['id']);
		}
	}
}								/* systeme de redirection et de contole de securité */
?>
<!DOCTYPE html>
<html>
	<head>
	</head>

	<body>
		<?php
			if(isset($_GET['eror']))        /* Gestion des ereurs */
			{
				switch ($_GET['eror']) 
				{
					case '2':
						echo "<h3>Pseudo incorect</h3>";
					break;
					
					case '3':
						echo "<h3>Pseudo déja utilisé</h3>";
					break;
					case '4':
						echo "<h3>Ereur lors de la création de la salle</h3>";
					break;
				}
			}						/* Gestion des ereurs */

			if(isset($_GET['gg']))        /* Gestion des victoire / defaite */
			{
				switch ($_GET['gg']) 
				{
					case '1':
						echo "<h3>Bravo, l'adversaire à quitter la partie !</h3>";
					break;
					
					case '2':
						echo "<h3>Vous avez terrasez le joueur adverse !</h3>";
					break;
					case '9':
						echo "<h3>Vous avez perdu !</h3>";
					break;
				}
			}
		?>

		<form action="find.php" method="POST">
			<input type="text" name="pseudo"/>
			<button>Play</button>
		</form>
	</body>
</html>