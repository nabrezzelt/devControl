<?php
    require_once("includes/connect.inc.php");    
    require_once("includes/Autoloader.Class.php");
    Autoloader::Load(); 

    //Online auf Offline setzten
    User::Logout(unserialize($_SESSION['user']));    
    $_SESSION['user'] = "";

    session_destroy();   
    echo "<meta http-equiv='refresh' content='0; URL=/devControl/'>";
?>