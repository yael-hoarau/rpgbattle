<?php
	session_start();
	require("../php/function/db.php");
	if(isset($_SESSION['pseudo']) and isset($_SESSION['roomname']) and isset($_SESSION['findstep']) and $_SESSION['roomname'] == "none" and isset($_GET['type']))
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
	
?>
