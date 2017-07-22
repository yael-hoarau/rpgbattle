<?php
try
{
	if($_SERVER['SERVER_NAME'] == "rpgbattle.esy.es")
	{
		$db = new PDO('mysql:host=mysql.hostinger.fr;dbname=u612291436_rpg', 'u612291436_rpg', 'antidot1');
	}
	else
	{
		$db = new PDO('mysql:host=localhost;dbname=rpgbattle', 'rpgbattle', 'antidot1');
	}
	
}
catch(exeption $e)
{
	echo "Site en maintenance ...";
}
?>