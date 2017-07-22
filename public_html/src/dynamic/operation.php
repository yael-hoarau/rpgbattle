<?php 
session_start();
require("../php/function/db.php");

if($_GET['action'] == 'attaquer')
{
	$req_target = $db->query("SELECT pseudo,life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) 
			AND player.pseudo != '" . $_SESSION['pseudo'] . "' ");
	$data_target = $req_target->fetch();
	$target_life = (int)$data_target['life'] - 10;
	$db->exec("UPDATE player SET life = (" . $target_life . ") WHERE player.pseudo = '" . $data_target['pseudo'] . "' ");
}

?>