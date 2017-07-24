// fonction de traitement
function insert_chat(tab)
{
	var chat = document.getElementById("chat_content");
	for(var i = 0;i<count(tab);i++)
	{
		chat.innerHTML += "<p class=\"talk\">" + tab.pseudo[i] . " : " . tab.content[i] . "</p>";
	}
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

function insert_chat(tab)
{
	var chat = document.getElementById("chat_content");
	for(var i = 0;i<count(tab);i++)
	{
		chat.innerHTML += "<p class=\"talk\">" + tab.pseudo[i] . " : " . tab.content[i] . "</p>";
	}
}

// fonction d'envoie
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

			insert_chat(data.chat);

			document.getElementById("life_me").innerHTML = data.player1.life;
			document.getElementById("life_him").innerHTML = data.player2.life;

			if(data.system != "continue")
			{
				var aff_global = document.getElementById("aff_global");
				document.getElementById("redirect_button").style.display = "inline-block";
				switch(data.system)
				{
					case "win1":
						//animation win
						aff_global.innerHTML = "Vous avez anéanti le joueur adverse !";
					break;

					case "win2":
						//animation win
						aff_global.innerHTML = "Le joueur adverse à pris la fuite !";
					break;

					case "lose":
						// animation lose
						aff_global.innerHTML = "Le joueur adverse à été meilleur cette fois !";
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
	var xhr5 = new XMLHttpRequest();
	xhr5.open('GET', 'src/dynamic/chat.php');

	xhr5.addEventListener('readystatechange', function()
	{
		if(xhr5.readyState == XMLHttpRequest.DONE && xhr5.status == 200)
		{
			var content = xhr5.responseText;
			if(content == "eror1")
			{
				alert("Caractère invalide !");
			}
			else if(content == "eror2")
			{
				alert("Ereur lors de l'envoie du message !");
			}
			else
			{
				var data = JSON.parse(content);
				insert_chat(data.chat);
			}
		}
	});
	xhr5.send("content=" + chat);
}

