// fonction de traitement

function keypress(event)
{
	if(event.keyCode == 13)
	{
		let input = $("#chat_input");
		if(input.val() == "")
		{
			if(input == $(':focus'))
			{
				input.css({'opacity' : "0.3"});
				input.blur();
			}
			else
			{
                input.css({'opacity' : "1"});
				input.focus();
			}
		}
		else
		{
			newchat(input.val());
			input.val("");
		}
	}
}

function click_newchat()
{
	let input = $("#chat_input");
	if(input.val() != "")
	{
		newchat(input.val());
	}
	input.css({'opacity' : "0.3"});
	input.val("");
	input.blur();
}

function insert_chat(tab)
{

    let chat = $("#chat_content");
	for(var i = 0;i<tab.pseudo.length;i++)
	{
	    let message = $('<p />')
            .attr('class', 'talk');
	    message.append($('<span />')
            .attr('style', 'color:' + (tab.pseudo[i] == $('#my_pseudo').html() ? 'blue' : 'red'))
            .html(tab.pseudo[i]))
            .append(' : ' + tab.content[i]);
	    chat.append( message.css({'background-color': $('#background_color_turn').html() %2 == 0 ? '' : 'wheat'}));
        $('#background_color_turn').html(parseInt($('#background_color_turn').html()) + 1);
	}
    chat.scrollTop(chat[0].scrollHeight);
}

// fonction d'envoie
function refresh()
{
    $.ajax({
        'url' : 'src/dynamic/refresh.php'
    })
		.done(function(data){
            if(data.chat != "nothing") insert_chat(data.chat);

            if(parseInt($('#last_id_history').html()) < data.last_id_history){
                $.ajax({
                    'url' : 'src/dynamic/refresh.php',
                    'method' : 'post',
                    'data' : 'last_id_client=' + $('#last_id_history').html()
                })
                    .done(function (return_history) {
                        //let last_id = $('#last_id_history').html();
                        for(let action in return_history){
                            if(return_history[action].description == ' a capitulé '){
                                $.ajax({
                                    'url' : 'src/dynamic/refresh.php',
                                    'method' : 'post',
                                    'data' : 'capitulate=true'
                                })
                            }
                            let message = $('<p />').addClass('history_action')
                                .html(return_history[action].date_action.substring(11, 20) + ' : ');
                            message.append($('<span />')
                                .attr('style', 'color:' + (return_history[action].pseudo == $('#my_pseudo').html() ? 'blue' : 'red'))
                                .html(return_history[action].pseudo))
                                .append(return_history[action].description);
                            $('#history').append(message.css({'background-color': $('#history_count').html() %2 == 0 ? '' : 'beige'}));
                            $('#history_count').html(parseInt($('#history_count').html()) + 1);
                            last_id = return_history[action].id;
                        }
                        $('#last_id_history').html(last_id);
                        $('#history').scrollTop($('#history')[0].scrollHeight);
                    })
            }

            if(data.turn != data.player1.pseudo ){
                $('#actions').fadeOut(function () {
                    $('#indication_turn').fadeIn();
                });
            }
            else {
                $('#indication_turn').fadeOut(function () {
                    $('#actions').fadeIn();
                });

            }

            $('#turn').html(data.turn);
            $('#prec').html(data.timer);
            $('#my_pseudo').html(data.player1.pseudo);
            $('#his_pseudo').html(data.player2.pseudo);

            apply_visual_effects(data);

            $('#my_life').html(data.player1.life);
            $('#his_life').html(data.player2.life);
            $('#my_mana').html(data.player1.mana);
            $('#his_mana').html(data.player2.mana);
            $('#my_shield').html(data.player1.shield);
            $('#his_shield').html(data.player2.shield);

            if(data.system != "continue")
            {
                let aff_global = $("#aff_global");
                $('#actions').css({"display" : "none"});
                $('#capituler').css({"display" : "none"});
                $('#indication_turn').css({"display" : "none"});
                $("#redirect_button").css({"display" : "inline-block"});
                switch(data.system)
                {
                    case "win1":
                        //animation win
                        aff_global.html("Vous avez anéanti le joueur adverse !");
                        break;

                    case "win2":
                        //animation win
                        aff_global.html("Le joueur adverse a pris la fuite !");
                        break;

                    case "win3":
                        //animation win
                        aff_global.html("Le joueur adverse a capitulé !");
                        break;

                    case "lose":
                        // animation lose
                        aff_global.html("Le joueur adverse à été meilleur cette fois !");
                        break;

                    case "lose2":
                        aff_global.html("Vous avez été déconnecté");
                        break;

                    case "lose3":
                        aff_global.html("Vous avez capitulé");
                        break;
                }
            }
            else
            {
                setTimeout("refresh()", 1000);
            }
		})
}

function operation(action)
{
    if($('#my_pseudo').html() == $('#turn').html() || action == 'capituler'){
        $.ajax({
            'url' : 'src/dynamic/operation.php?action=' + action
        });
    }
    else{
        $('#indication').html("Ce n'est pas votre tour");
        $('#indication').fadeIn(1000);
        $('#indication').fadeOut(1000);
        //$('#indication').html("");
    }


}

function newchat(chat)
{
    $.ajax({
        'url' : 'src/dynamic/chatbox.php',
		'method' : 'POST',
		'data' : "content=" + chat
    })
		.done(function (data) {
            if(data == "eror1")
            {
                alert("Caractère invalide !");
            }
            else if(data == "eror2")
            {
                alert("Erreur lors de l'envoie du message !");
            }
            else if(data == "nothing")
            {
                alert("nothing");
            }
            else
            {
                insert_chat(data.chat);
            }
        })
}

