<?php
function is_corect($pseudo)
{
	if(strlen($pseudo) < 4 OR strlen($pseudo) > 15) return false;

	for($i = 0; $i < strlen($pseudo); $i++)
	{
		$tmp = ord($pseudo[$i]);
		if($tmp > 47 and $tmp < 58) continue;
		else if($tmp > 64 and $tmp < 91) continue;
		else if($tmp > 96 and $tmp < 123) continue;
		else return false;
	}
	
	return true;
}

function pseudo_already_exist($pseudo, $db)
{
	$req_test_pseudo = $db->query("SELECT COUNT(*) as nb FROM player WHERE pseudo = '" . $pseudo . "'");
	$data_test_pseudo = $req_test_pseudo->fetch();
	if($data_test_pseudo['nb'] > 0) return true;
	else return false;
}
?>