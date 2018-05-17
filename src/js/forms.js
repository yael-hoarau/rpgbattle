( function () {
        "use strict";

        $(document).ready(function () {
            isConnected();

            let erreurCritique = function(){
                $('#error_indication').html('Erreur critique');
            };
            $('#form_guest').submit(function () {
                $.ajax({
                    'url' : $(this).attr('action'),
                    'method' : $(this).attr('method'),
                    'data' : $(this).serialize()
                })
                    .done(function (data) {
                        if(data !== "guest_connected"){
                            $('#error_indication').html(data);
                        } else {
                            search();
                        }
                    })
                    .fail(erreurCritique);
                return false;
            });

            $('#form_connection').submit(function () {
                $.ajax({
                    'url' : $(this).attr('action'),
                    'method' : $(this).attr('method'),
                    'data' : $(this).serialize()
                })
                    .done(function (data) {
                        if(data.info !== "user_connected"){
                            $('#error_indication').html(data.info);
                        } else {
                            display_infos(data.mail, data.pseudo, data.score, data.date_signin, data.nb_game);
                            $('#is_connected').html('true');
                            actionUser();
                            $('#info_not_user').empty();
                            stats();
                            $('#info_stats').empty().css({'border' : 'none'});
                            $('#info_stats')
                                .append($('<table />')
                                    .attr('id', 'tab_wait')
                                    .append($('<tr />')))
                                .append($('<table />')
                                    .attr('id', 'tab_run')
                                    .append($('<tr />')));
                        }
                    })
                    .fail(erreurCritique);
                return false;
            });

            $('#form_inscription').submit(function () {
                $.ajax({
                    'url' : $(this).attr('action'),
                    'method' : $(this).attr('method'),
                    'data' : $(this).serialize()
                })
                    .done(function (data) {
                        if(data !== "user_signed"){
                            $('#error_indication').html(data);
                        } else {
                            $('#error_indication').html('Correctement enregistré');
                            change_form();
                        }
                    })
                    .fail(erreurCritique);
                return false;
            });

            $('#form_create_room').submit(function () {
                $.ajax({
                    'url' : $(this).attr('action'),
                    'method' : $(this).attr('method'),
                    'data' : $(this).serialize()
                })
                    .done(function (data) {
                        if(data !== "ok"){
                            $('#error_indication').html(data);
                        } else {
                            create();
                        }
                    })
                    .fail(erreurCritique);
                return false;
            });

        });
    } ()
);

function change_form() {
    let toFadeIn = $('#form_connection').is(':visible') ? $('#form_inscription') : $('#form_connection');
    let toFadeOut = $('#form_inscription').is(':visible')  ? $('#form_inscription') : $('#form_connection');
    toFadeOut.fadeOut(function () {
        toFadeIn.fadeIn();
    });
    $('#change_form').html( $('#change_form').html() == 'Inscription' ? 'Connexion' : 'Inscription'  )
}

function display_infos( mail, pseudo, score, date_signin, nb_game) {
    let _mail = $('<span />').html('Email : ' + mail + '<br>');
    let _pseudo = $('<span />').html('Pseudo : ' + pseudo+ '<br>');
    let ratio = Math.round(parseInt(score) / (parseInt(nb_game) !== 0 ? parseInt(nb_game) : 1) *100);
    let _score = $('<span />').html('Score : ' + score + ' / ' + nb_game +
        ' | Pourcentage de victoire : ' +  ratio  + '% <br>');
    let _date_signin = $('<span />').html('Date d\'inscription : ' + date_signin + '<br>');
    let _disconnect = $('<button/>').attr("onclick", "window.location= \"destroy.php?user=true\"").html("Deconnexion");
    $('#infos_user').append(_mail, _pseudo, _score, _date_signin, _disconnect);

    $('#forms').fadeOut(function () {
        $('#connected').fadeIn();
    })


}

function fillForms(){
    $('#form_guest')
        .append($('<label />')
            .html(' Pseudo '))
        .append($('<input />')
            .attr('type', 'text')
            .attr('name', 'pseudo'))
        .append($('<input />')
            .attr('type', 'hidden')
            .attr('name', 'form')
            .attr('value', 'guest'))
        .append($('<button />')
            .html('Jouer sans se connecter'));

    $('#form_connection')
        .append($('<label />')
            .html('Mail ou pseudo : '))
        .append($('<input />')
            .attr('type', 'text')
            .attr('name', 'pseudo_mail'))
        .append($('<label />')
            .html(' Mot de passe : '))
        .append($('<input />')
            .attr('type', 'password')
            .attr('name', 'mdp'))
        .append($('<input />')
            .attr('type', 'hidden')
            .attr('name', 'form')
            .attr('value', 'connection'))
        .append($('<button />')
            .html('Se connecter'));

    $('#form_inscription')
        .append($('<label />')
            .html('Pseudo : '))
        .append($('<input />')
            .attr('type', 'text')
            .attr('name', 'pseudo'))
        .append($('<label />')
            .html(' Mail : '))
        .append($('<input />')
            .attr('type', 'text')
            .attr('name', 'mail'))
        .append($('<label />')
            .html(' Mot de passe : '))
        .append($('<input />')
            .attr('type', 'password')
            .attr('name', 'mdp'))
        .append($('<label />')
            .html(' Confirmer mot de passe : '))
        .append($('<input />')
            .attr('type', 'password')
            .attr('name', 'mdp2'))
        .append($('<input />')
            .attr('type', 'hidden')
            .attr('name', 'form')
            .attr('value', 'inscription'))
        .append($('<button />')
            .html('S\'inscrire'))

    $('#forms')
        .append($('<button />')
            .attr('id', 'change_form')
            .attr('onclick', 'change_form()' )
            .html('Inscription'))
}

function actionUser(){
    $('#connected')
        .append($('<button />')
            .attr('onclick', 'search()')
            .html('Lancer une rencontre aléatoire'))
        .append('<br>')
        .append($('<button />')
            .attr('onclick', '$(\'#form_create_room\').fadeIn()')
            .html('Créer une salle'))
        .append($('#form_create_room')
            .attr('action', 'createroom.php')
            .attr('method', 'POST')
            .attr('style', 'display: none')
            .append($('<label />')
                .html(' Entrez le mot de passe de la room '))
            .append($('<input />')
                .attr('type', 'text')
                .attr('name', 'mdp'))
            .append($('<input />')
                .attr('type', 'submit')
                .attr('value', 'Créer')))
}
/*<form id="form_create_room" action="createroom.php" method="POST" style="display: none">
    <input type="text" name="mdp">
    <input type="submit" value="Créer">
    </form>*/
function isConnected(){
    $.ajax({
        'url' : 'isConnected.php'
    })
        .done(function(data) {
            if(data.info == 'user_connected'){
                display_infos(data.mail, data.pseudo, data.score, data.date_signin, data.nb_game);
                $('#is_connected').html('true');
                actionUser();

            }else{
                fillForms();
                $('#forms').fadeIn();
            }
            stats();
        })
}

function joinPlayer(id_room, confirm, mdp) {
    if(mdp == undefined)
        mdp = '';
    $.ajax({
        url : "joinPlayer.php",
        method : "POST",
        data: 'id_room=' + id_room + '&mdp=' + mdp + '&confirm=' + confirm
    })
        .done(function (data) {
            if(data == "joinok"){
                window.location.href = "room.php";
            }
            else if(data == 'needmdp') {
                if (!$('#' + id_room).has('input').length) {
                    $('#' + id_room)
                        .append($('<input />')
                            .attr('type', 'text')
                            .attr('id', 'mdp_' + id_room)
                            .attr('placeholder', 'Mot de passe requis')
                            .blur(function () {
                                $(this).remove();
                            }))
                        .keypress(function (event) {
                            if (event.which == 13) {
                                event.preventDefault();
                                joinPlayer(id_room, true, $('#mdp_' + id_room ).val());
                            }
                        });
                    $('#mdp_' + id_room).focus();
                }
            }
            else if(data == 'confirm'){
                if (!$('#' + id_room).has('button').length){
                    $('#' + id_room)
                        .append($('<button />')
                            .attr('id', 'btn_' + id_room)
                            .attr('onclick', 'joinPlayer(' + id_room +', true)')
                            .html('Confirmer'));
                    $('#btn_' + id_room).focus();
                }
            }
            else

            if($('#error_indication:visible').length == 0){
                $('#error_indication').html(data);
                $('#error_indication').fadeIn();
            }
            else{
                $('#error_indication').fadeOut(function () {
                    $('#error_indication').html(data);
                    $('#error_indication').fadeIn();
                });
            }
        })
}

function stats(){
    $.ajax({
        'url' : 'stats.php'
    })
        .done(function (data) {
            let num_wait = $('<span />').html('Nombre de joueurs attendant une partie : <span id="num_wait"> '
                + data.wait.num + '</span>, dont <span id="num_wait_mdp">' + data.wait.mdp + ' </span> avec un mot de passe <br>');
            let num_run = $('<span />').html('Nombre de joueurs jouants actuellement une partie : <span id="num_run"> '
                + parseInt(data.run.num)*2 + '</span><br>');
            if($('#is_connected').html() == 'true'){
                $('#tab_wait')
                    .append($('<tr />')
                        .attr('class', 'table_title')
                        .append($('<th />').html('Joueur attendant dans une salle '),
                            $('<th />').html('Date de création de la salle ')))
                    .append($('<tr />'));

                $('#tab_run')
                    .append($('<tr />')
                        .attr('class', 'table_title')
                        .append($('<th />').html('Joueurs jouant dans une salle '),
                            $('<th />').html('Date de création de la salle')))
                    .append($('<tr />'));
                for(let i= 1; i <= parseInt(data.wait.num); i++){
                    $('#tab_wait')
                        .append( $('<tr />')
                            .css({'text-align' : 'center'})
                            .attr('id', data.wait.id[i-1] )
                            .append($('<td />').html(data.wait.players[i-1]),
                                $('<td />').html(data.wait.date[i-1]))
                            .css('cursor', 'pointer')
                            .click(function () {
                                joinPlayer(data.wait.id[i-1], false);
                            }));
                }
                for(let i= 1; i <= parseInt(data.run.num); i++){
                    $('#tab_run')
                        .append( $('<tr />')
                            .css({'text-align' : 'center'})
                            .attr('id', data.run.id[i-1])
                            .append($('<td />').html(data.run.players1[i-1] + '<br>' + data.run.players2[i-1] ),
                                $('<td />').html(data.run.date[i-1])));
                }
                $('#tab_run').fadeIn();
                $('#tab_wait').fadeIn();

            }
            else{
                $('#info_stats').append($('<p/>')
                    .attr('id', 'info_not_user')
                    .html( 'Connectez vous pour voir plus d\'informations' +
                        ' sur les parties en attente et en cours et pour pouvoir rejoindre les parties protégées' +
                        ' par un mot passe <br>'));
                if(!$('#info_stats').has('span').length)
                    $('#info_stats').append(num_wait, num_run).css({'border' : '1px black solid'});
            }
            refreshTables();

            if(!$('#leaderboard').has('input').length){
                for(let i = 0; i < 10; i++){
                    if(data.best_players.score[i] == undefined) break;
                    $('#leaderboard')
                        .append($('<input />')
                            .attr('type', 'hidden'))
                        .append($('<tr />')
                            .attr('class', 'score')
                            .append($('<td />')
                                .css({'text-align' : 'center'})
                                .html(i+1))
                            .append($('<td />')
                                .css({'text-align' : 'center'})
                                .html(data.best_players.score[i].pseudo))
                            .append($('<td />')
                                .css({'text-align' : 'center'})
                                .html(data.best_players.score[i].score)))
                        .append($('<tr />')
                            .attr('class', 'victory')
                            .css({'display' : 'none'})
                            .append($('<td />')
                                .css({'text-align' : 'center'})
                                .html(i+1))
                            .append($('<td />')
                                .css({'text-align' : 'center'})
                                .html(data.best_players.victory[i].pseudo))
                            .append($('<td />')
                                .css({'text-align' : 'center'})
                                .html(data.best_players.victory[i].ratio)))
                }
            }

        })

}

function change_leaderboard(type) {
    if(type == 'score'){
        if($('#type_leaderboard').html() == 'score')
            return;
        $('.victory').fadeOut(function () {
            $('.score').fadeIn();
        })
        $('#type_leaderboard').html('score');
        $('#change_table_score').css({'border-style':'inset', 'background': 'grey'});
        $('#change_table_victory').css({'border-style':'outset', 'background': 'none'});
    }
    if(type == 'victory'){
        if($('#type_leaderboard').html() == 'victory')
            return;
        $('.score').fadeOut(function () {
            $('.victory').fadeIn();
        })
        $('#type_leaderboard').html('victory');
        $('#change_table_victory').css({'border-style':'inset', 'background': 'grey'});
        $('#change_table_score').css({'border-style':'outset', 'background': 'none'});
    }
}


function stopWait(){
    $.ajax({
        url : "createroom.php?stop=true"
    })
        .done(function (data) {
            if(data == 'stopok')
                window.location.reload();
        })
}

function refreshTables(){
    $.ajax({
        url : 'stats.php'
    })
        .done(function (data) {

            $('#tab_wait').find('tr').each(function () {
                if ($(this).has('td').length != 0 && $.inArray($(this).attr('id'), data.wait.id) === -1) {
                    $(this).remove();
                }
            });

            $('#tab_run').find('tr').each(function () {
                if ($(this).has('td').length != 0 && $.inArray($(this).attr('id'), data.run.id) === -1) {
                    $(this).remove();
                }
            });

            for(let i= 1; i <= parseInt(data.wait.num); i++){
                let isAlreadyDisplayed = false;
                $('#tab_wait').find('tr').each(function () {
                    if($(this).is('#'+ data.wait.id[i-1])){
                        isAlreadyDisplayed = true;
                        return false;
                    }

                });
                if(isAlreadyDisplayed) continue;

                $('#tab_wait')
                    .append( $('<tr />')
                        .css({'text-align' : 'center'})
                        .attr('id', data.wait.id[i-1])
                        .append($('<td />').html(data.wait.players[i-1]),
                            $('<td />').html(data.wait.date[i-1]))
                        .css('cursor', 'pointer')
                        .click(function () {
                            joinPlayer(data.wait.id[i-1], false);
                        }));
            }

            for(let i= 1; i <= parseInt(data.run.num); i++) {
                let isAlreadyDisplayed = false;
                $('#tab_run').find('tr').each(function () {
                    if($(this).is('#'+ data.run.id[i-1])){
                        isAlreadyDisplayed = true;
                        return false;
                    }

                });
                if(isAlreadyDisplayed) continue;

                $('#tab_run')
                    .append($('<tr />')
                        .css({'text-align' : 'center'})
                        .attr('id', data.run.id[i - 1])
                        .append($('<td />').html(data.run.players1[i - 1] + '<br>' + data.run.players2[i - 1]),
                            $('<td />').html(data.run.date[i - 1])));
            }
            $('#num_wait').html(data.wait.num);
            $('#num_wait_mdp').html(data.wait.mdp);
            $('#num_run').html(parseInt( data.run.num)*2);
            setTimeout("refreshTables()", 1000);
        })
}