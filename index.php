<?php   
    require_once("includes/connect.inc.php");    
    require_once("includes/Autoloader.Class.php");
    Autoloader::Load();    

    if (isset($_SESSION['user']) && unserialize($_SESSION['user'])->isOnline()) 
    {        
         //User is LoggedIn
         echo "<meta http-equiv=\"refresh\" content=\"0; URL=/devControl/bugtracker\">";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>DevControl > Login</title>

    <meta charset="ISO-8859-1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="keywords" content="" />

    <link href="styles/style.css" type="text/css" rel="stylesheet" />
    <link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/slate/bootstrap.min.css" />
</head>

<body>
    <div class="container-fluid">
        <?php
            if (isset($_GET['error']) && isset($_GET['color'])) {
                echo "<div style=\"margin-top: 10px;\" class=\"alert alert-dismissible alert-" . $_GET['color'] . "\">
                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
                        " . $_GET['error'] . "<br/>
                      </div>";                    
            }
        ?>
        <div class="panel panel-default login-frame">
            <div class="panel-heading text-center"><h4></span> Login</h4></div>
            <div class="panel-body">
                 <form role="form" action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="username" class="form-control" name="username" />                        
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" name="password" />
                    </div>                    
                    <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-log-in"></span> Login</button>
                </form>
            
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>    
</body>
</html>
