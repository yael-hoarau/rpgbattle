<?php

function chat($db)
{
	$req_messages = $db->prepare("SELECT chat.id, pseudo, content FROM chat JOIN player WHERE chat.player = player.id AND room = ? AND player != ? AND seen = 0 ORDER BY date_create ASC ");
	$req_messages->execute(array($_SESSION['idroom'], $_SESSION['id'] ));
	$messages = new stdClass();
	$messages->pseudo = array();
	$messages->conctent = array();
	//$messages = array("pseudo" => array(), "content" => array());
	$test = false;
	for($i = 0;$data_messages = $req_messages->fetch(); $i++)
	{
		$test = true;
		$messages->pseudo[$i] = $data_messages['pseudo'];
		$messages->content[$i] = htmlentities($data_messages['content']);
		$db->query("UPDATE chat SET seen = 1 WHERE id = " . $data_messages['id']);
	}
	if(!$test)
	{
		return false;
	}
	else
	{
		return $messages;
	}
}
