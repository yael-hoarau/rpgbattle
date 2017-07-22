function leave(type)
{
	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'src/dynamic/leave.php');
	xhr.send(null);
	window.location = "index.php";
}

function test_room()
{
	var xhr2 = new XMLHttpRequest();
	xhr2.open('GET', 'src/dynamic/test_room.php');

	xhr2.addEventListener('readystatechange', function() 
	{
		if(xhr2.readyState == XMLHttpRequest.DONE && xhr2.status == 200) 
		{
			var content = xhr2.responseText;
			if(content == "leave")
			{
				window.location = "index.php?gg=1";
			}
			else if(content == "win")
			{
				window.location = "index.php?gg=2";
			}
			else if(content == "lose")
			{
				window.location = "index.php?gg=9";
			}
			else
			{
				setTimeout("test_room()", 5000);
			}
		}
	});
	xhr2.send(null);
}


function set_elements(tab)
{
	var elements = ['life_me', 'life_him'];
	for(var i = 0;i<elements.length;i++)
	{
		document.getElementById(elements[i]).innerHTML = tab[i];
	}
}

function refresh_attributs()
{
	var xhr3 = new XMLHttpRequest();
	xhr3.open('GET', 'src/dynamic/refresh_attributs.php');

	xhr3.addEventListener('readystatechange', function() 
	{
		if(xhr3.readyState == XMLHttpRequest.DONE && xhr3.status == 200) 
		{
			var content = xhr3.responseText;

			var chaines = content.split('|');
			
			var data_tab = [];

			var i = 0;

			for(var j = 0; j < chaines.length; j++)
			{
				start_pos = 0;
				lenght = 0;
				while(i < content.length)
				{
					lenght = chaines[j].indexOf(';', start_pos);
					if(lenght == -1) break;
					data_tab[i] = chaines[j].substr(start_pos, lenght);
					start_pos = start_pos + lenght + 1;
					i++;
				}
			}

			set_elements(data_tab);
			setTimeout("refresh_attributs()", 3000);
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