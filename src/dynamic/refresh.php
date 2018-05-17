<?php
session_start();
require("../php/function/db.php");
require("../php/function/chat.php");

function isPresent($id, $db)
{
	$req_ping = $db->prepare("SELECT ping FROM player WHERE id = ?");
	$req_ping->execute(array($id));
	$data_ping = $req_ping->fetch();
	$current_date = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s'));
	date_sub($current_date, date_interval_create_from_date_string('10 seconds'));
	$ping_date = date_create_from_format('Y-m-d H:i:s', $data_ping['ping']);
	return  $ping_date > $current_date;
}

function finish_room($db)
{
	$db->query("UPDATE room SET state = 'finish', last_op = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $_SESSION['idroom']);
	//$db->query("DELETE FROM player WHERE id=" . $_SESSION['id']);
	if(isset($_SESSION['id_user'])){
        unset($_SESSION['id']);
        unset($_SESSION['capitulate']);
        unset($_SESSION['capitulated']);
        unset($_SESSION['findstep']);
        $_SESSION['idroom'] = -1;
        $_SESSION['findstep'] = 1;
    }
	else
	    session_destroy();
}

if(isset($_SESSION['id_user']) && !isset($_SESSION['id'])){
    $req = $db->query("INSERT INTO player (pseudo, ping, life, mana) 
      VALUES ('" . $_SESSION['pseudo'] . "', '" . date('Y-m-d H:i:s') . "', 100, 100)");
    $_SESSION['id'] = $db->lastInsertId();
}

if(isset($_SESSION['idroom']) and isset($_SESSION['id']))
{
    if(!isPresent($_SESSION['id'], $db))
    {
        $_SESSION['disconnect'] = true;
    }
    else
	    $db->query("UPDATE player SET ping = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $_SESSION['id']);

	//Find Room
	if(isset($_SESSION['findstep']) and $_SESSION['idroom'] == -1 and isset($_GET['type']))
	{

	    if(isset($_SESSION['disconnect']) && $_SESSION['disconnect']){
	        echo 'disconnect';
	        exit();
        }

		if($_GET['type'] == "search" and $_SESSION['findstep'] == 1)
		{
			$i = 0;
			$req_search = $db->query("SELECT id, player1 FROM room WHERE state = 'waiting' AND mdp = '' ORDER BY date_create ASC ");
			while($data_search = $req_search->fetch())
			{
				if(isPresent($data_search['player1'], $db))
				{
					$_SESSION['idroom'] = $data_search['id'];
					$turn = mt_rand(1, 2) == 1 ? $data_search['player1'] : $_SESSION['id'];

					$db->query("UPDATE room SET state = 'running', player2 = " . $_SESSION['id'] . ", turn = " . $turn .
                        ", turn_date = '" . date('Y-m-d H:i:s') . "'  WHERE id = " . $data_search['id']);
					echo "room";
					unset($_SESSION['findstep']);
					$i++;
                    if(isset($_SESSION['id_user']))
                        $req_increment = $db->query("UPDATE user 
                                SET nb_game = (SELECT nb_game FROM (SELECT * FROM user) AS a WHERE id = " . $_SESSION['id_user']. " ) + 1 
                                WHERE id = " . $_SESSION['id_user']);
					break;
				}
			}
			if($i == 0)
			{
				$_SESSION['findstep'] = 2;
				echo "noroom";
			}
		}

		if( $_GET['type'] == 'create' and $_SESSION['findstep'] == 2)
		{
			$date_create = date('Y-m-d H:i:s');
			if(isset($_SESSION['mdp_create_room'])){
                $req_create = $db->prepare("INSERT INTO room (state, date_create, player1, last_op, mdp)
                                          VALUES (:state, :date_create, :player1, :last_op, :mdp)");
                $res = $req_create->execute(array('state' =>'waiting', 'date_create' => $date_create,
                    'player1' => $_SESSION['id'], 'last_op' => $date_create, 'mdp' => $_SESSION['mdp_create_room'] ));
                unset($_SESSION['mdp_create_room']);
            }
            else{
                $req_create = $db->prepare("INSERT INTO room (state, date_create, player1, last_op)
                                                        VALUES (:state, :date_create, :player1, :last_op)");
                $res = $req_create->execute(array('state' =>'waiting', 'date_create' => $date_create,
                    'player1' => $_SESSION['id'], 'last_op' => $date_create));
            }
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
            //$db->query("UPDATE player SET ping = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $_SESSION['id']);

			if(!isset($_SESSION['lastPingInWaiting']))
			{
				$_SESSION['lastPingInWaiting'] = 1;
			}
			else
			{
				$_SESSION['lastPingInWaiting'] = $_SESSION['lastPingInWaiting'] + 1;
				if($_SESSION['lastPingInWaiting'] > 10)
				{
					$db->query("UPDATE room SET last_op = '" . date('Y-m-d H:i:s') . "' WHERE player1 = " . $_SESSION['id']);
					$_SESSION['lastPingInWaiting'] = 0;
				}
			}
			$req_test = $db->query("SELECT id, state FROM room WHERE player1 = " . $_SESSION['id'] . " OR player2 = " . $_SESSION['id']. " LIMIT 1");
			$data_test = $req_test->fetch();
			if($data_test['state'] == "waiting")
			    echo "notyet";
			else
			{
			    //echo $data_test['state'] . ' ! ' . $data_test['id'] ; exit();
                $_SESSION['findstep'] = 1;
				$_SESSION['idroom'] = $data_test['id'];
				if(isset($_SESSION['id_user']))
                    $req_increment = $db->query("UPDATE user 
                                SET nb_game = (SELECT nb_game FROM (SELECT * FROM user) AS a WHERE id = " . $_SESSION['id_user']. " ) + 1 
                                WHERE id = " . $_SESSION['id_user']);
				echo "ready";
			}
		}
	}
	else
	{
	    $_SESSION['findstep'] = 1;

	    // refresh history
	    if(isset($_POST['last_id_client'])){
	        $req_to_display = $db->query("SELECT * FROM history WHERE idroom=" . $_SESSION['idroom'] . " AND id > " . $_POST['last_id_client'] );
	        $result_history = array();
	        while($data_to_display = $req_to_display->fetch()){
	            $result_history[] = array('date_action' => $data_to_display['date_action'], 'pseudo' => $data_to_display['pseudo'],
                                            'description' => $data_to_display['description'], 'id' => $data_to_display['id'] );
            }
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: application/json');

            echo json_encode($result_history);
            exit();
        }

        if (isset($_POST['capitulate']) && $_POST['capitulate'])
        {
            $_SESSION['capitulated'] = true;
            exit();
        }



		// Refresh attributs
		
		$req_player1 = $db->query("SELECT player.id, pseudo, ping, life, mana, shield FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND player.id = " . $_SESSION['id'] . " AND room.id = " . $_SESSION['idroom']);
		$req_player2 = $db->query("SELECT player.id, pseudo, ping, life, mana, shield FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND player.id != " . $_SESSION['id'] . " AND room.id = " . $_SESSION['idroom']);
		$req_room = $db->query("SELECT turn, turn_date FROM room WHERE id =" . $_SESSION['idroom']);
		$data_player1 = $req_player1->fetch();
		$data_player2 = $req_player2->fetch();
		$data_room = $req_room->fetch();

		$player1 = new stdClass();
		$player1->pseudo = $data_player1['pseudo'];
		$player1->life = $data_player1['life'];
		$player1->mana = $data_player1['mana'];
		$player1->shield = $data_player1['shield'];
        $player2 = new stdClass();
        $player2->pseudo = $data_player2['pseudo'];
        $player2->life = $data_player2['life'];
        $player2->mana = $data_player2['mana'];
        $player2->shield = $data_player2['shield'];

		// Test Room

        if(isset($_SESSION['disconnect']) && $_SESSION['disconnect'])
        {
            finish_room($db);
            $system = "lose2";
        }
		 else if(!isPresent($data_player2['id'], $db))
		{
			finish_room($db);
            if(isset($_SESSION['id_user']))
                $req_increment = $db->query("UPDATE user 
                                SET score = (SELECT score FROM (SELECT * FROM user) AS a WHERE id = " . $_SESSION['id_user']. " ) + 1 
                                WHERE id = " . $_SESSION['id_user']);
			$system = "win2";
		}
		else if (isset($_SESSION['capitulate']) && $_SESSION['capitulate'])
        {
            finish_room($db);
            $system = "lose3";
        }
        else if (isset($_SESSION['capitulated']) && $_SESSION['capitulated'])
        {
            finish_room($db);
            if(isset($_SESSION['id_user']))
                $req_increment = $db->query("UPDATE user 
                                SET score = (SELECT score FROM (SELECT * FROM user) AS a WHERE id = " . $_SESSION['id_user']. " ) + 1 
                                WHERE id = " . $_SESSION['id_user']);
            $system = "win3";
        }
		else
		{
			$req_players = $db->query("SELECT player.id FROM player JOIN room WHERE (player.id = room.player1 OR player.id = room.player2) AND room.id = " . $_SESSION['idroom'] . " AND life <= 0");
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
                    if(isset($_SESSION['id_user']))
                        $req_increment = $db->query("UPDATE user 
                                SET score = (SELECT score FROM (SELECT * FROM user) AS a WHERE id = " . $_SESSION['id_user']. " ) + 1 
                                WHERE id = " . $_SESSION['id_user']);
					$system = "win1";
				}
			}
			else
			{
				$system = "continue";
			}
		}

		if($system != 'continue' && isset($_SESSION['id_user'])){
            $req_del = $db->prepare("DELETE FROM player WHERE id=?");
            $req_del->execute(array($_SESSION['id']));
            unset($_SESSION['id']);
            unset($_SESSION['capitulate']);
            unset($_SESSION['capitulated']);
            unset($_SESSION['findstep']);
            $_SESSION['idroom'] = -1;
            $_SESSION['findstep'] = 1;
        }

		$chat = chat($db);
		if($chat == false)
		{
			$chat = "nothing";
		}
        $req_turn = $db->prepare("SELECT pseudo FROM player WHERE id =?");
        $req_turn->execute(array($data_room['turn']));
        $data_turn = $req_turn->fetch();
        $turn = $data_turn['pseudo'];

        $current_date = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $timer = date_diff($current_date, date_create_from_format('Y-m-d H:i:s', $data_room['turn_date']));
        $timer = $timer->format(" %s");
        if($timer >= 30 && $turn != $_SESSION['pseudo']){
            $db->exec("UPDATE room SET last_op = '" . date('Y-m-d H:i:s') . "', turn = " . $data_player1['id']
                . ", turn_date = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $_SESSION['idroom']);
            $db->query("INSERT INTO history VALUES ( NULL, " . $_SESSION['idroom'] .", '"
                . date('Y-m-d H:i:s') ."', '" . $data_player2['pseudo'] . "', ' n''a pas agi dans le temps imparti' )");
            $turn = $_SESSION['pseudo'];
        }

        $req_last_id_history = $db->query("SELECT id FROM history WHERE idroom =" . $_SESSION['idroom'] . " ORDER BY id DESC LIMIT 0,1");
        if($data_last_id_history = $req_last_id_history->fetch())
            $last_id_history = $data_last_id_history['id'];
        else
            $last_id_history = 0;



        $data_refresh = new stdClass();
		$data_refresh->player1 = $player1;
		$data_refresh->player2 = $player2;
		$data_refresh->system = $system;
		$data_refresh->chat = $chat;
		$data_refresh->turn = $data_turn['pseudo'];
		$data_refresh->timer = $timer;
		$data_refresh->last_id_history = $last_id_history;

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

		echo json_encode($data_refresh);
	}
}
