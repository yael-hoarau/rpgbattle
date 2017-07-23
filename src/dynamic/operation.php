<?php 
session_start();
require("../php/function/db.php");

if($_GET['action'] == 'attaquer')
{
	$req_target = $db->query("SELECT player.id, life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND player.id != " . $_SESSION['id']);
	$data_target = $req_target->fetch();
	$target_life = (int)$data_target['life'] - 10;
	$db->exec("UPDATE player SET life = (" . $target_life . ") WHERE player.id = " . $data_target['id']);
}

?>