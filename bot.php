<?php
if($_SERVER['REMOTE_ADDR'] != "::1")
{
	header("Location: ./index.php");
	exit();
}
require("src/php/function/db.php"); // connexion base de données

$date_actu = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s'));

$oneminago = date_format(date_sub($date_actu, date_interval_create_from_date_string('60 seconds')), 'Y-m-d H:i:s');

$threeminago = date_format(date_sub($date_actu, date_interval_create_from_date_string('180 seconds')), 'Y-m-d H:i:s');

$thirtyminago = date_format(date_sub($date_actu, date_interval_create_from_date_string('1800 seconds')), 'Y-m-d H:i:s');


$db->query("DELETE FROM chat WHERE room IN (SELECT id FROM room WHERE state = 'finish' AND last_op < '" . $oneminago . "') OR date_create < '" . $oneminago . "'");
$db->query("DELETE FROM player WHERE id IN (SELECT player1 as id FROM room WHERE state = 'finish' AND last_op < '" . $oneminago . "') OR id IN (SELECT player2 as id FROM room WHERE state = 'finish' AND last_op < '" . $oneminago . "') OR ping < '" . $oneminago . "'");
$db->query("DELETE FROM room WHERE (state = 'finish' AND last_op < '" . $oneminago . "') OR last_op < '" . $threeminago . "' OR date_create < '" . $thirtyminago . "'");

?>