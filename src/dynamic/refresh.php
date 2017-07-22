<?php
session_start();
require("../php/function/db.php");





if(isset($_SESSION['roomname']) and isset($_SESSION['pseudo']))
{
	$db->exec("UPDATE player SET ping = '" .  date('Y-m-d H:i:s') ."' WHERE pseudo = '" . $_SESSION['pseudo'] .  "' ");

	//Find Room
	if(isset($_SESSION['findstep']) and $_SESSION['roomname'] == "none" and isset($_GET['type']))
	{

		if($_GET['type'] == "search" and $_SESSION['findstep'] == 1)
		{
			$i = 0;
			$req_search = $db->query("SELECT name FROM room WHERE state = 'waiting' ORDER BY date_create ASC LIMIT 1");
			while($data_search = $req_search->fetch())
			{
				$_SESSION['roomname'] = $data_search['name'];
				$db->query("UPDATE room SET state = 'running', player2 = (SELECT id FROM player WHERE pseudo = '" . $_SESSION['pseudo'] . "') WHERE name = '" . $data_search['name'] . "'");
				echo "room";
				unset($_SESSION['findstep']);
				$i++;
			}
			if($i == 0)
			{
				$_SESSION['findstep'] = 2;
				echo "noroom";
			}
		}


		if($_GET['type'] == "create" and $_SESSION['findstep'] == 2)
		{
			$date_create = "" . date('Y') . date('m') . date('d') . date('H') . date('i');
			$roomname = "";
			for($i = 0;$i<rand(15, 20);$i++)
			{
				$j = rand(1 , 3);
				if($j == 1) $roomname = $roomname . chr(rand(48, 57));
				if($j == 2) $roomname = $roomname . chr(rand(65, 90));
				if($j == 3) $roomname = $roomname . chr(rand(97, 122));
			}
			$req_create = $db->prepare("INSERT INTO room (date_create, name, player1) VALUES (:date_create, :name, (SELECT id FROM player WHERE pseudo = :pseudo))");
			$res = $req_create->execute(array('date_create' => $date_create, 'name' => $roomname, 'pseudo' => $_SESSION['pseudo']));
			if($res == false)
			{
				$del_onfail = $db->query("DELETE FROM player WHERE pseudo = '" . $_SESSION['pseudo'] . "'");
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
			$req_getid = $db->query("SELECT id FROM player WHERE pseudo = '" . $_SESSION['pseudo'] . "' LIMIT 1");
			$data_getid = $req_getid->fetch();
			$id_pseudo = $data_getid['id'];
			$req_test = $db->query("SELECT name, state FROM room WHERE player1 = " . $id_pseudo . " OR player2 = " . $id_pseudo . " LIMIT 1");
			$data_test = $req_test->fetch();
			if($data_test['state'] == "waiting") echo "notyet";
			else
			{
				$_SESSION['roomname'] = $data_test['name'];
				echo "ready";
			} 
		}
	}



	else if($_SESSION['roomname'] != "none")
	{

		// Refresh attributs
		
		$req_player1 = $db->query("SELECT life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) 
			AND player.pseudo = '" . $_SESSION['pseudo'] . "' ");
		$req_player2 = $db->query("SELECT life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2)
			AND player.pseudo != '" . $_SESSION['pseudo'] . "' ");
		$data_player1 = $req_player1->fetch();
		$data_player2 = $req_player2->fetch();

		$player1 = array( "life" => $data_player1['life']);
		$player2 = array( "life" => $data_player2['life']);




		// Test Room

		$req_ping = $db->query("SELECT ping FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2)
			AND player.pseudo != '" . $_SESSION['pseudo'] . "' ");
		$data_ping = $req_ping->fetch();
		$current_date = date('Y-m-d H:i:s');
		date_sub($current_date, date_interval_create_from_date_string('2 seconds'));
		if($data_ping['ping'] < $current_date ) 
		{
			session_destroy();
			$systeme = "win 2";
		}
		else 
		{
			$req_players = $db->query("SELECT pseudo,life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) ");
			while($data_players = $req_players->fetch())
			{
				$systeme = test_life($data_players['pseudo'], $data_players['life']);
			}
		}

		$data_refresh = array ("player1" => $player1,
								"player2" => $player2,
								"systeme" => $systeme);

		echo json_encode($data_refresh);

	}
}


 






function test_life($pseudo, $life)
{
	if($life <= 0 )
	{
		if($pseudo == $_SESSION['pseudo'])
			return "lose";
		else
			return "win1";
	
	}
	return "continue";
}


?>