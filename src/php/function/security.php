<?php
	$security_list = array(
	"script>",
	"<img",
	"frame>",
	"style>",
	"frameset>",
	"document.cookie",
	"%00",
	"\\0",
	"php://",
	"object>");

function control_chat($suspect)
{
	global $security_list;
	for($i = 0;$i < count($security_list);$i++)
	{
		if(stripos($suspect, $security_list[$i]) !== FALSE) return true;
	}
	return false;
}

function auto_ban($ip, $reason, $db)
{
	$db->query("INSERT INTO ban (ip, reason, date_start) VALUES ('" . $ip . "', " . $reason . ", '" . date('Y-m-d H:i:s') . "')");
}

function test_ban($ip, $db)
{
	$req_test_ban = $db->query("SELECT COUNT(*) as nb FROM ban WHERE ip = '" . $ip . "'");
	$data_test_ban = $req_test_ban->fetch();
	if($data_test_ban['nb'] > 0) return true;
	else return false;
}
?>