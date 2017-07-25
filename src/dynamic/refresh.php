<?php
session_start();
require("../php/function/db.php");
require("../php/function/chat.php");


function isPresent($id, $db)
{
	$req_ping = $db->query("SELECT ping FROM player WHERE id = " . $id);
	$data_ping = $req_ping->fetch();
	$current_date = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s'));
	date_sub($current_date, date_interval_create_from_date_string('3 seconds'));
	$ping_date = date_create_from_format('Y-m-d H:i:s', $data_ping['ping']);
	return  $ping_date > $current_date;
}

function finish_room($db)
{
	$db->query("UPDATE room SET state = 'finish', last_op = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $_SESSION['idroom']);
	session_destroy();
}



if(isset($_SESSION['idroom']) and isset($_SESSION['id']))
{
	$db->exec("UPDATE player SET ping = '" .  date('Y-m-d H:i:s') ."' WHERE id = " . $_SESSION['id']);

	//Find Room
	if(isset($_SESSION['findstep']) and $_SESSION['idroom'] == -1 and isset($_GET['type']))
	{

		if($_GET['type'] == "search" and $_SESSION['findstep'] == 1)
		{
			$i = 0;
			$req_search = $db->query("SELECT id, player1 FROM room WHERE state = 'waiting' ORDER BY date_create ASC ");
			while($data_search = $req_search->fetch())
			{
				if(isPresent($data_search['player1'], $db))
				{
					$_SESSION['idroom'] = $data_search['id'];
					$db->query("UPDATE room SET state = 'running', player2 = " . $_SESSION['id'] . " WHERE id = " . $data_search['id']);
					echo "room";
					unset($_SESSION['findstep']);
					$i++;
					break;
				}
			}
			if($i == 0)
			{
				$_SESSION['findstep'] = 2;
				echo "noroom";
			}
		}


		if($_GET['type'] == "create" and $_SESSION['findstep'] == 2)
		{
			$date_create = date('Y-m-d H:i:s');
			$req_create = $db->prepare("INSERT INTO room (date_create, player1, last_op) VALUES (:date_create, :player1, :last_op)");
			$res = $req_create->execute(array('date_create' => $date_create, 'player1' => $_SESSION['id'], 'last_op' => $date_create));
			if($res == false)
			{
				$del_onfail = $db->query("DELETE FROM player WHERE id = " . $_SESSION['id']);
				session_destroy();
				echo "erorcreate";
			}
			else
			{
				$_SESSION['findstep'] = 3;
				echo "createok";
			}
		}


		if($_GET['type'] == "ready" and $_SESSION['findstep'] == 3)
		{
			$req_test = $db->query("SELECT id, state FROM room WHERE player1 = " . $_SESSION['id'] . " OR player2 = " . $_SESSION['id']. " LIMIT 1");
			$data_test = $req_test->fetch();
			if($data_test['state'] == "waiting") echo "notyet";
			else
			{
				$_SESSION['idroom'] = $data_test['id'];
				echo "ready";
			}
		}
	}

	else
	{

		// Refresh attributs
		
		$req_player1 = $db->query("SELECT player.id, ping, life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND player.id = " . $_SESSION['id']);
		$req_player2 = $db->query("SELECT player.id, ping, life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND player.id != " . $_SESSION['id']);
		$data_player1 = $req_player1->fetch();
		$data_player2 = $req_player2->fetch();

		$player1 = array( "life" => $data_player1['life']);
		$player2 = array( "life" => $data_player2['life']);




		// Test Room

		
		if(!isPresent($data_player2['id'], $db))
		{
			finish_room($db);
			$system = "win2";
		}
		else
		{
			$req_players = $db->query("SELECT player.id FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND life <= 0");
			if($data_players = $req_players->fetch())
			{
				if($data_players['id'] == $_SESSION['id'])
				{
					finish_room($db);
					$system = "lose";
				}
				else
				{
					finish_room($db);
					$system = "win1";
				}
			}
			else
			{
				$system = "continue";
			}
		}

		$chat = chat($db);
		if($chat == false)
		{
			$chat = "nothing";
		}

		$data_refresh = array ("player1" => $player1,
								"player2" => $player2,
								"system" => $system,
								"chat" => $chat);

		echo json_encode($data_refresh);
	}
}


?>