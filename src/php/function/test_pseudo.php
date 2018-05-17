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

function is_taken_by_user($pseudo, $db)
{
    $req_pseudo = $db->query("SELECT pseudo FROM user WHERE pseudo ='" . $pseudo . "'" );
    if($req_pseudo->fetch())
        return true;
    return false;
}
?>