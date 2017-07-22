<?php 
session_start();
require("src/php/function/db.php");
require("src/php/function/test_pseudo.php");

if(!isset($_POST['pseudo']))
{
	header('Location: ./index.php');
	exit();
}

if(!is_corect($_POST['pseudo']))
{
	header('Location: ./index.php?eror=2');
	exit();
}

$pseudo = $_POST['pseudo'];

if(pseudo_already_exist($pseudo, $db))
{
	header('Location: ./index.php?eror=3');
	exit();
}

$req = $db->query("INSERT INTO player (pseudo) VALUES ('" . $pseudo . "')");

$_SESSION['pseudo'] = $pseudo;
$_SESSION['roomname'] = "none";
$_SESSION['findstep'] = 1;
?>
<!DOCTYPE html>
<html>
	<head>
	</head>

	<body>
<img src="src/img/loading.gif">
<h1 id="info"></h1>

	<script>
		var info = document.getElementById("info");

		function req(type)
		{
			var xhr = new XMLHttpRequest();
			xhr.open('GET', 'src/dynamic/findroom.php?type=' + type);

			xhr.addEventListener('readystatechange', function()
			{
	    		if(xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) 
	    		{
	    			var content = xhr.responseText;
	    			if(content == "noroom")
	    			{
	    				setTimeout("create()", 500);
	    			}
	    			if(content == "room")
	    			{
	    				window.location = "room.php";
	    			}
	    			if(content == "createok")
	    			{	
	    				waitfor();
	    			}
	    			if(content == "erorcreate")
	    			{
	    				window.location = "index.php?eror=4";
	    			}
	    			if(content == "notyet")
	    			{
	    				waitfor();
	    			}
	    			if(content == "ready")
	    			{
	    				window.location = "room.php";
	    			}
				}
			});
			xhr.send(null);
		}

		function search()
		{
			info.innerHTML = "Recherche de salle";
			setTimeout("req('search')", 500);
		}

		function create()
		{
			info.innerHTML = "Création de la salle";
			setTimeout("req('create')", 500);
		}

		function waitfor()
		{
			info.innerHTML = "Attente de joueur...";
			setTimeout("req('ready')", 500);
		}

		
		search();
	</script>
	</body>
</html>