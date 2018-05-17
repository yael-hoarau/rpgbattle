function less_life(player) {
    let life = '#' + player + '_life';
    let character = '#' + player + '_character';
    $( life ).effect( 'pulsate', {}, 1000);
    //$( character ).effect( 'shake', {}, 500);
    $( character ).effect( 'highlight', {color: 'red'}, 1000);
}

function less_shield(player) {
    let character = '#' + player + '_character';
    let shield = '#' + player + '_shield';
    $( character ).animate({'background-color': "blue"}, 100);
    $( character ).effect( 'shake', {}, 300);
    $( character ).animate({ 'background-color': "rgba(255,255,255,0.2)"}, 100);


}

function more_shield(player){
    //let shield = '#' + player + '_shield';
    let character = '#' + player + '_character';
    //$( shield ).effect( 'bounce', {}, 500);
    $( character ).effect( 'highlight', {color: 'blue'}, 1000);
}

function show_turn(pseudo) {
    let player = $('#my_pseudo').html() == pseudo ?  $('#my_pseudo') : $('#his_pseudo');
    let character = $('#my_pseudo').html() == pseudo ? $('#my_character') : $('#his_character');
    let character2 = $('#my_pseudo').html() != pseudo ? $('#my_character') : $('#his_character');
    //character.css({outline: '0 solid transparent'}).animate({outlineWidth: 4, outlineColor: '#f37736'}, 500);
    //character.css({outline: '0 solid transparent', }).animate({outlineWidth: 0, outlineColor: '#f37736'}, 500);
    //character/*.css({'box-shadow': '0px 0px 20px 0px green' })*/.animate({'boxShadowBlur': '20px'}, 500);
    //character2/*.css({'box-shadow': '0px 0px 0px 0px green' })*/.animate({'boxShadowBlur': '0px'}, 500);
    character2.removeClass('shadow-pulse');
    character.addClass('shadow-pulse');
    //character.on('cssanimationend', function(){
        //character.removeClass('shadow-pulse');
        // do something else...
   // });
    //player.animate({color: "green"}, 500);
    //player.animate({ color: "black"}, 500);

}


function apply_visual_effects(data){

    if( $('#his_life').html() > data.player2.life )
        less_life('his');
    if( $('#my_life').html() > data.player1.life )
        less_life('my');

    if( $('#my_shield').html() > data.player1.shield )
        less_shield('my');
    if( $('#his_shield').html() > data.player2.shield )
        less_shield('his');

    if($('#my_shield').html() != "" && $('#my_shield').html()  < data.player1.shield )
        more_shield('my');
    if($('#his_shield').html() != "" && $('#his_shield').html()  < data.player2.shield )
        more_shield('his');

    drawSector();
    show_turn(data.turn);
}

function drawSector(){
    var activeBorder = $("#activeBorder");
    var prec = activeBorder.children().children().text();
    if (prec > 30)
        prec = 30;
    var deg = prec*12;
    if (deg <= 180){
        activeBorder.css('background-image','linear-gradient(' + (90+deg) + 'deg, transparent 50%, #A2ECFB 50%),linear-gradient(90deg, #A2ECFB 50%, transparent 50%)');
    }
    else{
        activeBorder.css('background-image','linear-gradient(' + (deg-90) + 'deg, transparent 50%, #39B4CC 50%),linear-gradient(90deg, #A2ECFB 50%, transparent 50%)');
    }

    var startDeg = $("#startDeg").attr("class");
    activeBorder.css('transform','rotate(' + startDeg + 'deg)');
    $("#circle").css('transform','rotate(' + (-startDeg) + 'deg)');
}