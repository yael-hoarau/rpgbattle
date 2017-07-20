<?php
	session_start();
	require("../php/function/db.php");
	if(isset($_SESSION['roomname']) and isset($_SESSION['pseudo']) and $_SESSION['roomname'] != "none")
	{
		$test_req = $db->query("SELECT COUNT(*) as nb FROM room WHERE name = '" . $_SESSION['roomname'] . "'");
		$data_test = $test_req->fetch();
		if($data_test['nb'] == 0) 
		{
			session_destroy();
			echo "leave";
		}
		else 
		{
			$req_players = $db->query("SELECT pseudo,life FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) ");
			while($data_players = $req_players->fetch())
			{
				test_life($data_players['pseudo'], $data_players['life']);
			}
			
			
		}
	}

	function test_life($pseudo, $life)
	{
		if($life <= 0 )
		{
			if($pseudo == $_SESSION['pseudo'])
				echo "loose";
			else
				echo "win";
			$db->query("CALL del_room('" . $_SESSION['roomname'] . "')");
		}
	}
?>
