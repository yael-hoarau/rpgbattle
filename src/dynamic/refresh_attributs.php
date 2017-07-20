<?php
session_start();
require("../php/function/db.php");


if(isset($_SESSION['roomname']) and isset($_SESSION['pseudo']) and $_SESSION['roomname'] != "none")
	{
		
		$req_player1 = $db->query("SELECT life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) 
			AND player.pseudo = '" . $_SESSION['pseudo'] . "' ");
		$req_player2 = $db->query("SELECT life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2)
			AND player.pseudo != '" . $_SESSION['pseudo'] . "' ");
		$data_player1 = $req_player1->fetch();
		$data_player2 = $req_player2->fetch();
		$separator = ';';
		$player1 = $data_player1['life'] . $separator;
		$players = $player1 . '|' . $data_player2['life'] . $separator;
		
		
		echo $players;

	}
?>