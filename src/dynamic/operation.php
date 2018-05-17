<?php 
session_start();
require("../php/function/db.php");

if(isset($_SESSION['idroom']) and $_SESSION['idroom'] != -1)
{
	$req_room = $db->query("SELECT state, turn FROM room WHERE id = " . $_SESSION['idroom']);
	$data_room = $req_room->fetch();

	if($data_room['state'] == "running")
	{
		if($data_room['turn'] != $_SESSION['id'] && $_GET['action'] != "capituler" )
		{
			echo "not your turn";
			exit();
		}
        $description = ' a effectué une action qui ne comporte pas encore de description';
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
                $description = ' a attaqué son adversaire pour lui enlever ';
                if($data_target['life'] > $target_life)
                    $description .= $data_target['life'] - $target_life  . ' points de vie';
                if( $data_target['shield'] > $target_shield ){
                    if($data_target['life'] > $target_life)
                        $description .= ' et';
                    $description .= $data_target['shield'] - $target_shield . ' points de bouclier';
                }

			break;
			case "bouclier":
				if($data_myself['mana'] < 10)
				{
					echo "not enough mana";
					exit();
				}
				$my_mana = $data_myself['mana'] - 10;
				$my_shield = $data_myself['shield'] + 20;
				$db->exec("UPDATE player SET mana = " . $my_mana . ", shield = " . $my_shield . " WHERE player.id = " . $data_myself['id']);
                $description = ' s\'est ajouté 20 points de bouclier pour 10 de mana ';
			break;
            case "capituler":
                $_SESSION['capitulate'] = true;
                $description = ' a capitulé ';
                break;
            case "passer":
                $description = ' a passé son tour ';
                break;
			default:
				echo "not an action";
				exit();
			break;
		}

        //$req_history = $db->prepare("INSERT INTO history (idroom, date_action, pseudo, description) VALUES (:idroom, :date_action, :pseudo, :description)");
        //$res = $req_history->execute(array('idroom' =>$_SESSION['idrooom'], 'date_action' => date('Y-m-d H:i:s'), 'pseudo' => $_SESSION['pseudo'], 'description' => $description));

        $description = str_replace("'", "''",$description);
		$db->query("INSERT INTO history VALUES ( NULL, " . $_SESSION['idroom'] .", '"
            . date('Y-m-d H:i:s') ."', '" . $_SESSION['pseudo'] . "', '" . $description ."' )");
		$db->exec("UPDATE room SET last_op = '" . date('Y-m-d H:i:s') . "', turn = " . $data_target['id'] . ", turn_date = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $_SESSION['idroom']);
	}
}
