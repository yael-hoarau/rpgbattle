<?php

session_start();
require("../php/function/db.php");
require("../php/function/chat.php");

if(isset($_SESSION['idroom']) and isset($_SESSION['id']))
{
	$db->exec("INSERT INTO chat(room, player, content, date_create) VALUES ( " . $_SESSION['idroom'] . ", " . $_SESSION['id'] . ", " . $_POST['content'] . ", " . date('Y-m-d H:i:s') . " )");
	$messages = chat();
	$mymessage = array("pseudo" => $_SESSION['pseudo'], "content" => $_POST['content']);
	$messages[count($messages)] = $mymessage;

	echo json_encode($messages);
}

?>