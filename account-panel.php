<?php
    require_once("includes/connect.inc.php");
    require_once("includes/Autoloader.Class.php");
    require_once("handler/account-panel.handler.php");
    Autoloader::load();

    if (!isset($_SESSION['user']) || !unserialize($_SESSION['user'])->isOnline()) 
    {        
         //User is not LoggedIn
         echo "<meta http-equiv=\"refresh\" content=\"0; URL=/devControl/?error=Please%20login%20first%21&color=warning\">";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accountpanel</title>

    <meta charset="ISO-8859-1" />
    <meta name="description" content="" />
    <meta name="author" content="Nabrezzelt" />
    <meta name="keywords" content="" />

    <link href="styles/style.css" type="text/css" rel="stylesheet" />    
    <link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/slate/bootstrap.min.css" />
    <link rel="stylesheet" href="https://www.tutorialspoint.com/jquery/src/alertify/alertify.core.css" />
    <link rel="stylesheet" href="https://www.tutorialspoint.com/jquery/src/alertify/alertify.default.css" />

    <script type="text/javascript" src="https://www.tutorialspoint.com/jquery/src/alertify/alertify.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>    
    <script type="text/javascript" src="/devControl/js/bootstrap-treeview.js"></script>
    <script type="text/javascript" src="/devControl/js/notify.js"></script>
    
</head>

<body>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Titel und Schalter werden fuer eine bessere mobile Ansicht zusammengefasst -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Navigation ein-/ausblenden</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><span class="glyphicon glyphicon-equalizer"></span> DevControl</a>
            </div>

            <!-- Alle Navigationslinks, Formulare und anderer Inhalt werden hier zusammengefasst und koennen dann ein- und ausgeblendet werden -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="/devControl/bugtracker">Bugtracker</a></li>
                    <li><a href="/devControl/wiki">Wiki</a></li>
                    <li><a href="/devControl/changelog">Changelog</a></li>                            
                </ul>

                <ul class="nav navbar-nav navbar-right">                                
                        <form class="navbar-form navbar-left" role="search">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search">
                            </div>
                            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
                        </form>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-bell"></span> <span class="badge">6</span></a>
                            <ul class="dropdown-menu pre-scrollable" style='max-height: 150px;'>                                
                                <li><a href="/messages/message4654">Notification 1</a></li>
                                <li><a href="/devControl/bugtracker">User x Assigned Bug y to You</a></li>
                                <li><a href="#">Notification 1</a></li>
                                <li><a href="#">Notification 0</a></li>
                                <li><a href="#">Notification 1</a></li>
                                <li><a href="#">Notification 2</a></li>
                                <li><a href="#">Notification 3</a></li>
                                <li><a href="#">Notification 4</a></li>
                                <li><a href="#">Notification 3</a></li>
                                <li><a href="#">Notification 2</a></li>
                                <li><a href="#">Notification 1</a></li>
                                <li><a href="#">Notification 0</a></li>
                            </ul>
                        </li>
                        <p class="navbar-text logged_in_as" style="padding-left: 10px;">Logged in as <a href="/devControl/account-panel"><?php echo unserialize($_SESSION['user'])->getUsername(); ?></a></p>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Menu <span class="caret"></span></a>
                            <ul class="dropdown-menu">                                
                                <li><a href="/devControl/account-panel"><span class="glyphicon glyphicon-user"></span> Accountpanel</a></li>
                                <li><a href="/devControl/admin-panel"><span class="glyphicon glyphicon-cog"></span> Adminpanel</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="/devControl/logout"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

    <div class="container-fluid" style="margin-top: 60px;">
        <div class="row">
            <div class="col-sm-3 col-md-2">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <h3>Accountpanel</h3>
                    </div>
                    <div class="panel-body text-center">
                        <img class="img-rounded" src="images/profilePictures/default.png" alt="defaultProfilePicture">
                        <h4>Username: <small><?php echo unserialize($_SESSION['user'])->getUsername(); ?></small></h4>
                        <h4>Email: <small><?php echo unserialize($_SESSION['user'])->getEmail(); ?></small></h4>
                        <h4>Rank: <small><?php echo unserialize($_SESSION['user'])->getRankname(); ?></small></h4>
                        <a class='btn btn-default btn-xs' href="#"><span class='glyphicon glyphicon-lock'></span>Change Password</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-7 col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Mir zugewiesene Bugs</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo listAssignedBugs(); ?>       
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Von mir eröffnete Bugs</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo listOpenedBugs(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showBug(catID, bugID) {
            window.location = "/devControl/bugtracker/category/" + catID + "/bug/" + bugID + "/show";            
        }

        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });

        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });

        $(document).ready(function(){
            $('.buglist').DataTable({                  
                searching: false,
                "pageLength": 10                     
            });
        });
    </script>   
</body>