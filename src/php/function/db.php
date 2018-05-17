<?php
try
{
	if($_SERVER['SERVER_NAME'] == "rpgbattle.esy.es")
	{
		$db = new PDO('mysql:host=mysql.hostinger.fr;dbname=u612291436_rpg', 'u612291436_rpg', 'antidot1');
	}
	else
	{
        $db = new PDO('mysql:host=mysql-test-jdbc-yael.alwaysdata.net;dbname=test-jdbc-yael_rpgbattle',
            '144519', 'yael');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
}
catch(PDOexeption $e)
{
	echo "Site en maintenance ...";exit();
}
?>