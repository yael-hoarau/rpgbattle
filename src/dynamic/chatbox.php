<?php
session_start();
require("../php/function/db.php");
require("../php/function/security.php");
require("../php/function/chat.php");

if(isset($_SESSION['idroom']) and isset($_SESSION['id']) and $_SESSION['idroom'] != -1 and $_POST['content'])
{
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
	if(!control_chat($_POST['content']))
	{
		$req_new_chat = $db->prepare("INSERT INTO chat(room, player, content, date_create) VALUES (:room, :player, :content, :date_create)");
		$req_new_chat->execute(array(":room" => $_SESSION['idroom'], ":player" => $_SESSION['id'], ":content" => $_POST['content'], ":date_create" => date('Y-m-d H:i:s')));
		$messages = chat($db);

		if($messages == false)
		{
			echo json_encode(array("chat" => array("pseudo" => array($_SESSION['pseudo']), "content" => array(htmlentities($_POST['content'])))));
		}
		else
		{
			$messages['pseudo'][count($messages['pseudo'])] = $_SESSION['pseudo'];
			$messages['content'][count($messages['content'])] = htmlentities($_POST['content']);

			echo json_encode(array("chat" => $messages));
		}
	}
	else
	{
		if(isset($_SESSION['hacker']))
		{
			auto_ban($_SERVER['REMOTE_ADDR'], 1, $db);
			session_destroy();
		} 
		else $_SESSION['hacker'] = 1;
	}
}

?>