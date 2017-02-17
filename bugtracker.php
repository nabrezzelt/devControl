<?php
    require_once("includes/connect.inc.php");
    require_once("includes/Autoloader.Class.php");
    require_once("handler/bugtracker.handler.php");
    Autoloader::load();

    if (!isset($_SESSION['user']) || !unserialize($_SESSION['user'])->isOnline()) 
    {        
         //User is not LoggedIn
         echo "<meta http-equiv=\"refresh\" content=\"0; URL=/devControl/index.php?error=Please%20login%20first%21&color=warning\">";
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>DevControl > Bugtracker</title>

    <meta charset="ISO-8859-1" />
    <meta name="description" content="" />
    <meta name="author" content="Nabrezzelt" />
    <meta name="keywords" content="" />

    <link href="/devControl/styles/style.css" type="text/css" rel="stylesheet" />    
    <link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/slate/bootstrap.min.css" />
    <link rel="stylesheet" href="https://www.tutorialspoint.com/jquery/src/alertify/alertify.core.css" />
    <link rel="stylesheet" href="https://www.tutorialspoint.com/jquery/src/alertify/alertify.default.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" />
    
    <script type="text/javascript" src="https://www.tutorialspoint.com/jquery/src/alertify/alertify.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>    
    <script type="text/javascript" src="/devControl/js/select2.min.js"></script> 
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
                    <li class="active"><a href="/devControl/bugtracker">Bugtracker</a></li>
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
                        <p class="navbar-text logged_in_as" style="padding-left: 10px;">Logged in as <a href="/devControl/account-panel"><?php echo unserialize($_SESSION['user'])->getUsername(); ?></a></p>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Menu <span class="caret"></span></a>
                            <ul class="dropdown-menu">                                
                                <li><a href="/devControl/account-panel"><span class="glyphicon glyphicon-user"></span> Accountpanel</a></li>
                                <li><a href="/devControl/admin-panel"><span class="glyphicon glyphicon-cog"></span> Adminpanel</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="logout"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

    <div class="container-fluid" style="margin-top: 60px;">
        <div class="row">
            <div class="col-sm-3 col-md-2" id="projectList">
            </div>
            <div class="col-sm-9 com-md-10">
                <div class="row"  style="margin-bottom:2px;">
                    <div class="col-sm-6">
                        <?php 
                            if(isset($_GET['categoryID'])) 
                            $hasProjectAccess = Project::hasAccess(Category::getCategoryByID($_GET['categoryID'])->getProjectID(), unserialize($_SESSION['user'])->getID());

                            if(isset($_GET['categoryID']) && $hasProjectAccess) 
                            {
                                echo "<h3 style=\"padding-bottom:1px;\"><u>" . Category::getCategoryByID($_GET['categoryID'])->getName() . "</u></h3>";                                
                            } 
                        ?>
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php 
                            if(isset($_GET['categoryID']) && $_GET['act'] != "category-new-report" && $hasProjectAccess && Permission::hasPermission(Permission::BUG_CREATE, unserialize($_SESSION['user'])->getID())) 
                            {
                                echo "<a style=\"margin-top: 12px;\" class=\"btn btn-warning\" href=\"/devControl/bugtracker/category/" . $_GET['categoryID'] . "/new-report\"><span class=\"glyphicon glyphicon-plus-sign\"></span> Report new Bug</a>";
                            } 
                        ?>
                    </div>
                </div>                             
                <?php
                    echo handler();                                       
                ?>
            </div>
        </div>
    </div>





    <!-- Datei hochladen -->
    <div class="modal fade" id="file_upload_modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal Content -->
            <!-- Header -->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4>Datei hochladen</h4>
                </div>
            </div>
            <!-- Modal Body -->
            <div class="modal-body" style="background-color: #303030;">
                <form role="form" action="<?php if(isset($_GET['categoryID']) && isset($_GET['bugID'])) { echo "/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/file-upload"; } else { echo "#"; } ?>" method="POST" enctype="multipart/form-data">                                
                <div class="form-group">
                    <label for="cat_name">File:</label>
                    <input type="file" name="uploadedFile" />
                </div>
                    <button id="cat_name_btn" type="submit" class="btn btn-success btn-block"><span class="glyphicon glyphicon-upload"></span> Upload</button>
                </form>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer" style="background-color: #303030;">
                <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
            </div>
        </div>
    </div>

    <script>
        var tree = [
            <?php
                $projects = Project::getAllProjects();
                $JSON = "";
                
                for ($projects->rewind(); $projects->valid(); $projects->next())
                {
                    if (Project::hasAccess($projects->current()->getID(), unserialize($_SESSION['user'])->getID())) 
                    {
                        $JSON .= Category::getAllCategorysInJSONByProjectID($projects->current()->getID());
                        $JSON .= ",";
                    }                    
                }

                echo substr($JSON, 0, -1);
            ?>
        ];             

    $('#projectList').treeview({
        data: tree,
        expandIcon: 'glyphicon glyphicon-chevron-right',
        collapseIcon: 'glyphicon glyphicon-chevron-down',
        showTags: true,
        enableLinks: true
    });      

    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });  

    $(document).ready(function(){
        $('.buglist').DataTable({  
            "order": [[ 0, "desc" ]],
            searching: false,
            "pageLength": 50                     
        });
    });

    function showBug(catID, bugID) {
        window.location = "/devControl/bugtracker/category/" + catID + "/bug/" + bugID + "/show";            
    }

        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        })

        $(document).ready(function(){
            $("#btn_file_upload").click(function(){
                $("#file_upload_modal").modal();
            });
        }); 
        
        $('#assignedTo').select2();
        $('#priority').select2();
        $('#status').select2();

    </script>
</body>