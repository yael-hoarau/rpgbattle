<?php

function chat($db)
{
	$req_messages = $db->query("SELECT chat.id, pseudo, content FROM chat JOIN player WHERE chat.player = player.id AND room = '" . $_SESSION['idroom'] . "' AND player != " . $_SESSION['id'] . " AND seen = 0 ORDER BY date_create ASC ");
	$messages = array();
	for($i = 0;$data_messages = $req_messages->fetch(); $i++)
	{
		$messages[$i] = $data_messages;
		$db->exec("UPDATE chat SET seen = 1 WHERE id = " . $data_messages['id'] );
	}
	return $messages;
}

?>