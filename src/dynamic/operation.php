<?php 
session_start();
require("../php/function/db.php");

if(isset($_SESSION['idroom']) and $_SESSION['idroom'] != -1)
{
	$req_state = $db->query("SELECT state FROM room WHERE id = " . $_SESSION['idroom']);
	$data_state = $req_state->fetch();
	if($data_state['state'] == "running")
	{
		$req_target = $db->query("SELECT player.id, life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND player.id != " . $_SESSION['id']);
		$data_target = $req_target->fetch();
		switch($_GET['action'])
		{
			case "attaquer":
				$target_life = (int)$data_target['life'] - 10;
				$db->exec("UPDATE player SET life = (" . $target_life . ") WHERE player.id = " . $data_target['id']);
			break;
			default:
				exit();
			break;

		}
		

		$db->exec("UPDATE room SET last_op = '" . date('Y-m-d H:i:s') . "', turn = " . $data_target['id'] . ", timer = 15  WHERE id = " . $_SESSION['idroom']);
	}

}



?>