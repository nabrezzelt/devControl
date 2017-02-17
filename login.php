<?php
     include_once("includes/connect.inc.php");
     include_once("includes/Autoloader.Class.php");
     Autoloader::load();

     $user = User::Login(mysql_real_escape_string($_POST['username']), mysql_real_escape_string($_POST['password']));
     
     if ($user != null) 
     {
        if($user->isBanned())
        {
            Helper::redirectTO("index.php?error=Your%20account%20is%20permanenty%20banned!%21&color=danger");
        }

        $_SESSION['user'] = serialize($user); 
        Helper::redirectTO("account-panel.php");  
     }
     else
     {
         Helper::redirectTo("account-panel.phpindex.php?error=Login%20failed%21&color=warning");
     }
  ?>