<?php 
session_start();
require("../php/function/db.php");

if(isset($_SESSION['idroom']) and $_SESSION['idroom'] != -1)
{
	$req_state = $db->query("SELECT state FROM room WHERE id = " . $_SESSION['idroom']);
	$data_state = $req_state->fetch();
	if($data_state['state'] == "running")
	{
		$req_target = $db->query("SELECT player.id, life, mana, shield FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND player.id != " . $_SESSION['id'] . " AND room.id = " . $_SESSION['idroom']);
		$req_myself = $db->query("SELECT player.id, life, mana, shield FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND player.id = " . $_SESSION['id'] . " AND room.id = " . $_SESSION['idroom']);
		$data_target = $req_target->fetch();
		$data_myself = $req_myself->fetch();
		switch($_GET['action'])
		{
			case "attaquer":
				$degats = 10;
				if($data_target['shield'] >= 0)
				{
					$target_shield = $data_target['shield'];
					if ($data_target['shield'] <= $degats)
					{
						$degats -= $data_target['shield'];
						$target_shield = 0;
					}
					else
					{
						$target_shield -= $degats;
						$degats = 0;
					}
					$db->exec("UPDATE player SET shield = " . $target_shield . " WHERE player.id = " . $data_target['id']);
				}
				$target_life = $data_target['life'] - $degats;
				$db->exec("UPDATE player SET life = (" . $target_life . ") WHERE player.id = " . $data_target['id']);
			break;
			case "bouclier":
				if($data_myself['mana'] <= 0) break;
				$my_mana = $data_myself['mana'] - 10;
				$my_shield = $data_myself['shield'] + 20;
				$db->exec("UPDATE player SET mana = " . $my_mana . ", shield = " . $my_shield . " WHERE player.id = " . $data_myself['id']);
			break;
			default:
				exit();
			break;

		}
		

		$db->exec("UPDATE room SET last_op = '" . date('Y-m-d H:i:s') . "', turn = " . $data_target['id'] . ", timer = 15  WHERE id = " . $_SESSION['idroom']);
	}

}



?>