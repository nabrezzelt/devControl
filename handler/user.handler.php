<?php
    require_once("includes/connect.inc.php");
    require_once("includes/Autoloader.Class.php");
    Autoloader::load();

    function handler()
    {
        $re = "";

        if(isset($_GET['act'])) 
        {
            $act = $_GET['act'];
        }
        else
        {
            $act = "default";
        }

        switch ($act) {
            case '':                
            break;

            default:
                if(Permission::hasPermission(Permission::USER_SHOW_DATA, unserialize($_SESSION['user'])->getID()))
                {                    
                    if(isset($_GET['userID']))
                    {
                        $user = User::getUser((int) $_GET['userID']);

                        $re = "<div class='row'>
                                    <div class='col-sm-4'></div>
                                    <div class='col-sm-5 col-md-4'>                
                                        <div class='panel panel-default'>
                                            <div class='panel-heading'>
                                                <div class='row'>                                                    
                                                    <div class='col-sm-10 text-center'>
                                                        <h3>Account</h3>                                
                                                    </div>
                                                    <div class='col-sm-2 text-right'>
                                                        <div class='dropdown' style='margin-top: 20px; cursor: pointer;'>
                                                            <a class='dropdown-toggle' data-toggle='dropdown'><span class='glyphicon glyphicon-option-vertical'></span></a>
                                                            <ul class='dropdown-menu'>
                                                                <li><a href='#'><span class='glyphicon glyphicon-pencil'></span> Change Username</a></li>
                                                                <li><a href='#'><span class='glyphicon glyphicon-pencil'></span> Change E-Mail</a></li>
                                                                <li><a href='#'><span class='glyphicon glyphicon-pencil'></span> Change Rankname</a></li>                                        
                                                                <li><a href='#'><span class='glyphicon glyphicon-list'></span>  Show Permissions</a></li>
                                                                <li class='divider'></li>
                                                                <li><a href='#'><span class='glyphicon glyphicon-remove-circle'></span> Delete User</a></li>                                        
                                                            </ul>
                                                        </div>     
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='panel-body text-center'>
                                                <img src='/devControl/images/profilePictures/" . $user->getProfilePicture() . "' alt='profilePicture' class='img-rounded'>
                                                <h3>" . $user->getUsername() . "</h3>
                                                <hr>
                                                <h5><small>Rankname</small> " . $user->getRankName() . "</h5>
                                                " . ((Permission::hasPermission(Permission::USER_VIEW_EMAIL, unserialize($_SESSION['user'])->getID())) ? "<h5><small>E-Mail</small> " . $user->getEMail() . "</h5>" : "") . "
                                                " . ((Permission::hasPermission(Permission::USER_VIEW_LAST_LOGIN, unserialize($_SESSION['user'])->getID())) ? "<h5><small>Last Login</small> " . $user->getLastLogin() . "</h5>" : "") . "
                                                " . ((Permission::hasPermission(Permission::USER_VIEW_LAST_IP, unserialize($_SESSION['user'])->getID())) ? "<h5><small>Last IP</small> " . $user->getLastIP() . "</h5>" : "") . "
                                                " . ((Permission::hasPermission(Permission::USER_VIEW_REGISTER_DATE, unserialize($_SESSION['user'])->getID())) ? "<h5><small>Register Date</small> " . $user->getRegisterDate() . "</h5>" : "") . "
                                                " . (($user->isOnline()) ? "<h5 class='text-success'>Online</h5>" : "<h5 class='text-danger'>Offline</h5>") . "                                                    
                                                <hr>
                                                <div class='row'>
                                                    <div class='col-sm-6 text-left'>
                                                        " . ((Permission::hasPermission(Permission::USER_VIEW_BANSTATE, unserialize($_SESSION['user'])->getID())) ? (($user->isBanned()) ? "<h4 class='text-danger'><span class='glyphicon glyphicon-lock'></span> Banned</h4>" : "<h4 class='text-success'>Not Banned</h4>") : "")  . "   
                                                    </div>
                                                    <div class='col-sm-6 text-right'>
                                                        " . ((Permission::hasPermission(Permission::ADMIN_USER_BAN, unserialize($_SESSION['user'])->getID())) ? (($user->isBanned()) ? "<a class='btn btn-default' href='/devControl/admin-panel/user/" . $user->getID() . "/unban'>Unban Account</a>" : "<a class='btn btn-default' href='/devControl/admin-panel/user/" . $user->getID() . "/ban'>Ban Account</a>") : "") . "   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                
                                    </div>                                
                                </div>";
                    }
                }
                else
                {
                     $re .= Helper::noPermission(Permission::USER_SHOW_DATA);
                }
            break;  
        }
        
        return $re;
    }   
?>
    