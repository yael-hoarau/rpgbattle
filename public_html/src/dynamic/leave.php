<?php
	session_start();
	require("../php/function/db.php");
	if(isset($_SESSION['roomname']) and isset($_SESSION['pseudo']) and $_SESSION['roomname'] != "none")
	{
		$req_getid = $db->query("SELECT player1, player2 FROM room WHERE name = '" . $_SESSION['roomname'] . "'");
		$data_getid = $req_getid->fetch();
		$id1 = $data_getid['player1'];
		$id2 = $data_getid['player2'];
		$del_room = $db->query("DELETE FROM room WHERE name = '" . $_SESSION['roomname'] . "'");
		$del_player = $db->query("DELETE FROM player WHERE id = " . $id1 . " OR id = " . $id2);
		session_destroy();
	}
?>
