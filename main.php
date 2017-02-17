<!DOCTYPE html>
<html>

<head>
    <title>Buglist</title>

    <meta charset="ISO-8859-1" />
    <meta name="description" content="" />
    <meta name="author" content="Nabrezzelt" />
    <meta name="keywords" content="" />

    <link href="styles/style.css" type="text/css" rel="stylesheet" />    
    <link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/slate/bootstrap.min.css" />
    <link rel="stylesheet" href="https://www.tutorialspoint.com/jquery/src/alertify/alertify.core.css" />
    <link rel="stylesheet" href="https://www.tutorialspoint.com/jquery/src/alertify/alertify.default.css" />

    <script src="https://www.tutorialspoint.com/jquery/src/alertify/alertify.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://malsup.github.io/jquery.form.js"></script>
    
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
                        <a class="navbar-brand" href="main.php">Bugtracker</a>
                    </div>

                    <!-- Alle Navigationslinks, Formulare und anderer Inhalt werden hier zusammengefasst und koennen dann ein- und ausgeblendet werden -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li><a href="main.php">Bugs</a></li>
                            <li><a href="adminpanel.php">Administration</a></li>                            
                        </ul>

                        <ul class="nav navbar-nav navbar-right">                                
                                 <form class="navbar-form navbar-left" role="search">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Search">
                                    </div>
                                    <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
                                </form>
                                <p class="navbar-text logged_in_as" style="padding-left: 10px;">Logged in as <a href="/devControl/account-panel">UserXYZ</a></p>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Menu <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/devControl/account-panel">Accountpanel</a></li>
                                        <li><a href="/devControl/logout">Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
    
    <div id="wrapper">
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav" id="userList">
                <li class="sidebar-brand text-uppercase">                    
                    Projectlist                   
                </li>                                               
            </ul>
            <div id="projectList">

            </div>                        
        </div>       
    <div class="container-fluid" id="app">        
        <button type="button" id="btnShowProjectList" class="button js-trigger btnPosition">Projects</button>     
            <div class="container" id="page-content-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">                                
                                <div>
                                    <table class="table table-striped" id="buglist" class="display">
                                        <thead>
                                            <tr>
                                                <th>Priorität</th>
                                                <th>ID</th>
                                                <th>Auswirkung</th>
                                                <th>Status</th>
                                                <th>Aktualisiert</th>
                                                <th>Zusammenfassung</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                                                                    
                                            <tr>
                                                <td><span class="hiddenSort">4</span><span class="label label-warning">Hoch</span></td>
                                                <td>1</td>
                                                <td>Laggs</td>
                                                <td>Zugewiesen (<a href="#">Nabrezzelt</a>)</td>
                                                <td>Heute</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">5</span><span class="label label-danger">Sehr Hoch</span></td>
                                                <td>2</td>
                                                <td>Laggs</td>
                                                <td>Geschlossen</td>
                                                <td>Heute</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">5</span><span class="label label-danger">Sehr Hoch</span></td>
                                                <td>3</td>
                                                <td>Absturz</td>
                                                <td>Bestätigt</td>
                                                <td>vor 10min</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">5</span><span class="label label-danger">Sehr Hoch</span></td>
                                                <td>4</td>
                                                <td>Absturz</td>
                                                <td>Bestätigt</td>
                                                <td>vor 1h 36min</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">2</span><span class="label label-primary">Niedrig</span></td>
                                                <td>5</td>
                                                <td>Designtechnisch</td>
                                                <td>Neu</td>
                                                <td>vor 5sec</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">1</span><span class="label label-info">Sehr Niedrig</span></td>
                                                <td>6</td>
                                                <td>Formatfehler</td>
                                                <td>Neu</td>
                                                <td>vor 2d</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">5</span><span class="label label-danger">Sehr Hoch</span></td>
                                                <td>7</td>
                                                <td>Absturz</td>
                                                <td>Neu</td>
                                                <td>vor 1 Woche</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">2</span><span class="label label-primary">Niedrig</span></td>
                                                <td>8</td>
                                                <td>Kleinerer Fehler</td>
                                                <td>Neu</td>
                                                <td>Heute</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">2</span><span class="label label-primary">Niedrig</span></td>
                                                <td>9</td>
                                                <td>Textfehler</td>
                                                <td>Neu</td>
                                                <td>Heute</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">4</span><span class="label label-warning">Hoch</span></td>
                                                <td>10</td>
                                                <td>Absturz</td>
                                                <td>Neu</td>
                                                <td>Heute</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">3</span><span class="label label-success">Normal</span></td>
                                                <td>11</td>
                                                <td>Absturz</td>
                                                <td>Neu</td>
                                                <td>Heute</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">5</span><span class="label label-danger">Sehr Hoch</span></td>
                                                <td>12</td>
                                                <td>Absturz</td>
                                                <td>Neu</td>
                                                <td>Heute</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                            <tr>
                                                <td><span class="hiddenSort">4</span><span class="label label-warning">Hoch</span></td>
                                                <td>13</td>
                                                <td>Absturz</td>
                                                <td>Neu</td>
                                                <td>Heute</td>
                                                <td>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>          
                            </div>   
                        </div>                           
                    </div>
                </div>
            </div>
        </div>                    
    </div>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrap-treeview.js"></script>
<script type="text/javascript" src="js/notify.js"></script>
<script>
    $("#btnShowProjectList").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
        $(this).toggleClass("btnPosition");
    });
    
    var tree = [
    {
        text: "PvP",
        href: '#parent1',
        tags: ['4'],
        nodes: [
        {
            text: "Arena",
            tags: ['16'],
            nodes: [
                {
                    text: "Ring der Ehre",
                    tags: ['83']
                },
                {
                    text: "Nagrand",
                    tags: ['26']
                },
                {
                    text: "Ruinen von Lordaeron",
                    tags: ['12']
                },
                {
                    text: "Arena des Schergrats",
                    tags: ['56']
                },
                {
                    text: "Arena von Dalaran",
                    tags: ['90']
                }
            ]
        },
        {
            text: "Schlachtfeld",
            tags: ['10'],
            nodes: [
                {
                    text: "Arathibecken",
                    tags: ['66']
                },
                {
                    text: "Auge des Sturms",
                    tags: ['19']
                },
                {
                    text: "Strand der Uralten",
                    tags: ['26']
                },
                {
                    text: "Schlacht um Gilneas",
                    tags: ['10']
                },
                {
                    text: "Zwillingsgipfel",
                    tags: ['87']
                }
            ]
        }
        ]
    },
    {
        text: "Klassen",
        tags: ['519'],
        nodes: [
            {
                text: "Schamane",
                tags: ['44']
            },        
            {
                text: "Magier",
                tags: ['49']
            },
            {
                text: "Druide",
                tags: ['63']
            },
            {
                text: "Priester",
                tags: ['10']
            },
            {
                text: "Todesritter",
                tags: ['1']
            },
            {
                text: "Schurke",
                tags: ['146']
            },
            {
                text: "Hexenmeister",
                tags: ['184']
            },
            {
                text: "Paladin",
                tags: ['154']
            },
            {
                text: "Krieger",
                tags: ['14']
            },
            {
                text: "Jäger",
                tags: ['14']
            }
        ]
    },
    {
        text: "Instanzen",
        tags: ['198']
    },
    {
        text: "Schlachtzüge",
        tags: ['156']
    }
    ];                   

    $('#projectList').treeview({
        data: tree,
        expandIcon: 'glyphicon glyphicon-chevron-right',
        collapseIcon: 'glyphicon glyphicon-chevron-down',
        showTags: true
    });
       
    $(document).ready(function() {
        $('#buglist').DataTable({  
            "order": [[ 0, "desc" ]]                      
        });
    });
    
</script>
</body>
</html>

