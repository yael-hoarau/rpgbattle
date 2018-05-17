<?php
session_start();
require("src/php/function/db.php");

$req_rooms_wait = $db->query("SELECT * FROM room WHERE state='waiting'");
$req_rooms_run = $db->query("SELECT * FROM room WHERE state ='running'");

$num_wait = 0;
$players_wait = array();
$date_wait = array();
$id_room_wait = array();
$wait_with_psw = 0;

$num_run = 0;
$players_run = array();
$id_room_run = array();
$date_run = array();


$date_actu = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s'));
$tensecondsago = date_format(date_sub($date_actu, date_interval_create_from_date_string('10 seconds')), 'Y-m-d H:i:s');
while($data_rooms_wait = $req_rooms_wait->fetch()){
    if($data_rooms_wait['last_op'] < $tensecondsago)
        continue;
    if($data_rooms_wait['mdp'] != '')
        $wait_with_psw++;
    $req_player_wait = $db->query("SELECT pseudo FROM player WHERE id=" . $data_rooms_wait['player1']);
    $data_player_wait = $req_player_wait->fetch();
    $players_wait[] = $data_player_wait['pseudo'] ;
    $id_room_wait[] = $data_rooms_wait['id'];
    $date_wait[] = $data_rooms_wait['date_create'];
    $num_wait++;
}

while($data_rooms_run = $req_rooms_run->fetch()){
    $req_player1_run = $db->query("SELECT pseudo FROM player WHERE id=" . $data_rooms_run['player1']);
    $data_player1_run = $req_player1_run->fetch();
    $req_player2_run = $db->query("SELECT pseudo FROM player WHERE id=" . $data_rooms_run['player2']);
    $data_player2_run = $req_player2_run->fetch();
    $players_run1[] = $data_player1_run['pseudo'];
    $players_run2[] = $data_player2_run['pseudo'];
    $id_room_run[] = $data_rooms_run['id'];
    $date_run[] = $data_rooms_run['date_create'];
    $num_run ++;
}

$req_players_score = $db->query("SELECT * FROM user ORDER BY score DESC ");

$limit_score = 10;
$best_players_score = array();
$best_players_victory = array();
while ($data_players_score = $req_players_score->fetch()){
    if(--$limit_score > 0){
        $best_players_score[] = array('pseudo' => $data_players_score['pseudo'], 'score' => $data_players_score['score']);
    }
    if((int) $data_players_score['nb_game'] == 0)
        $ratio = 0;
    else
        $ratio = ((int) $data_players_score['score']) / ((int) $data_players_score['nb_game']);
    $ratio = round($ratio*100);
    $best_players_victory[] = array('pseudo' => [$data_players_score['pseudo']], 'ratio' =>  $ratio . '%') ;
}

function cmp($a, $b)
{
    //echo $b['ratio'];
    return $b['ratio'] - $a['ratio'];
}
usort($best_players_victory, 'cmp');
$best_players_victory = array_slice($best_players_victory,0,9);


$data_rooms = new stdClass();
$data_rooms->wait = array('id' => $id_room_wait, 'num' => $num_wait, 'players' => $players_wait, 'date' => $date_wait, 'mdp' => $wait_with_psw);
$data_rooms->run = array('id' => $id_room_run,'num' => $num_run, 'players1' => $players_run1, 'players2' => $players_run2, 'date' => $date_run);
$data_rooms->best_players = array( 'score' => $best_players_score, 'victory' => $best_players_victory);

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

echo json_encode($data_rooms);
