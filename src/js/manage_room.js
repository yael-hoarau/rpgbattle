function refresh()
{
	var xhr3 = new XMLHttpRequest();
	xhr3.open('GET', 'src/dynamic/refresh.php');

	xhr3.addEventListener('readystatechange', function() 
	{
		if(xhr3.readyState == XMLHttpRequest.DONE && xhr3.status == 200) 
		{
			var content = xhr3.responseText;
			var data = JSON.parse(content);

			// Traitement des valeurs à afficher




			if(/* system = "win" or "lose"*/)
			{
				//affichage de l'animation de victoire ou de defaite
				// si defaite -> affichage de la raison
			}
			else
			{
				//sinon timeout
				setTimeout("refresh()", 1000);
			}	
		}
	});
	xhr3.send(null);
}

function operation(action)
{
	var xhr4 = new XMLHttpRequest();
	xhr4.open('GET', 'src/dynamic/operation.php?action=' + action);
	xhr4.send(null);
}