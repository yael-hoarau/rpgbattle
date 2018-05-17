<?php

session_start();
if(isset($_SESSION['id_user']) && $_GET['user'] != 'true'){
    unset($_SESSION['id']);
    unset($_SESSION['capitulate']);
    unset($_SESSION['capitulated']);
    $_SESSION['idroom'] = -1;
    $_SESSION['findstep'] = 1;
}
else
    session_destroy();

header('Location: index.php');

?>