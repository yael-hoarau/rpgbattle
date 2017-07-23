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

			document.getElementById("life_me").innerHTML = data.player1.life;
			document.getElementById("life_him").innerHTML = data.player2.life;




			if(data.system != "continue")
			{
				switch(data.system)
				{
					case "win1":
						//animation win
						document.getElementById("test_room").innerHTML = "Vous avez anéanti le joueur adverse !";
					break;

					case "win2":
						//animation win
						document.getElementById("test_room").innerHTML = "Le joueur adverse à pris la fuite !";
					break;

					case "lose":
						// animation lose
						document.getElementById("test_room").innerHTML = "Le joueur adverse à été meilleur cette fois !";
					break;
				}
			}
			else
			{
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

function newchat(chat)
{
	// traitement du message
	// envoie du message json
	// pas de reception c'est le refresh qui s'en occupe
}

function keypress(event)
{
	if(event.keyCode == 13)
	{
		var input = document.getElementById("chat_input");
		if(input.value == "")
		{
			if(input == document.activeElement)
			{
				input.style.opacity = "0.3";
				input.blur();
			}
			else
			{
				input.style.opacity = "1";
				input.focus();
			}
		}
		else
		{
			newchat(input.value);
			input.style.opacity = "0.3";
			input.value = "";
			input.blur();
		}
	}
}

function click_newchat()
{
	var input = document.getElementById("chat_input");
	if(input.value != "")
	{
		newchat(input.value);
	}
	input.style.opacity = "0.3";
	input.value = "";
	input.blur();
}
