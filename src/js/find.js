let info = $('#info_find');
let erreurCritique = function(){
    $('body').html('Erreur critique');
};

function req(type)
{
    $.ajax({
        'url' : 'src/dynamic/refresh.php?type=' + type
    })
        .done(function(content){
            switch (content){
                case "noroom" :
                    setTimeout("create('')", 500);
                    break;
                case "room" :
                    window.location = "room.php";
                    break;
                case "createok" :
                    waitfor();
                    break;
                case "errorcreate" :
                    $('#error_indication').html('Ereur lors de la création de la salle');
                    break;
                case "notyet" :
                    waitfor();
                    break;
                case "ready" :
                    window.location = "room.php";
                    break;
                case "disconnect" :
                    info.html("Vous avez été déconnecté, redirection en cours");
                    setTimeout(1000);
                    window.location = "destroy.php";
            }
        })
        .fail(erreurCritique);
}

function search()
{
    $('#img_gif').fadeIn();
    info.html("Recherche de salle");
    setTimeout("req('search')", 500);
}

function create()
{
    info.html("Création de la salle");
    setTimeout(req('create'), 500);
}

function waitfor()
{
    info.html("Attente de joueur...");
    if($('#img_gif:visible').length == 0)
        $('#img_gif').fadeIn();
    $('#stop_wait')
        .attr('onclick', 'stopWait()')
        .html('Annuler')
        .fadeIn();
    setTimeout("req('ready')", 500);
}


