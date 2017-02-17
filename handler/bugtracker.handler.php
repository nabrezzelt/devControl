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

        if(!isset($_GET['categoryID'])) return "<div class='alert alert-info'><strong>Missing Category-ID! - You have no Category selected.</strong><br></div>";
        $categoryID = $_GET['categoryID'];
        if(!Project::hasAccess(Category::getCategoryByID($categoryID)->getProjectID(), unserialize($_SESSION['user'])->getID()))
        {
            return Helper::AlertNoProjectAccess();
        }

        switch ($act) {
            case 'category-show':                
                $bugs = Bug::getAllBugs($categoryID);                

                if (Permission::hasPermission(Permission::SHOW_BUGLIST, unserialize($_SESSION['user'])->getID())) 
                {                                        
                    $re .= "<div class='panel panel-default'>
                                <div class='panel-body'>
                                    <table class='table table-striped display buglist'>
                                        <thead>
                                            <tr>
                                                <th>Priorität</th>
                                                <th>ID</th>                                                
                                                <th>Status</th>
                                                <th>Aktualisiert</th>
                                                <th>Zusammenfassung</th>
                                                <th>Reported By</th>
                                            </tr>
                                        </thead>
                                        <tbody>";

                                            $bugs->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                            for ($bugs->rewind(); $bugs->valid(); $bugs->next()) 
                                            {
                                                $bug = $bugs->current();

                                                $statusText = $bug->getStatus()->getName();
                                                if($bug->getStatus()->getID() == Status::ASSIGNED)
                                                {                               
                                                    $statusText .=  " (<a href='/devControl/user/" . $bug->getAssignedToUser()->getID() . "/" . Helper::formatURL($bug->getAssignedToUser()->getUsername()) . "'>" . $bug->getAssignedToUser()->getUsername() . "</a>)"; 
                                                } 

                                                $re .= "<tr onClick='showBug(" . $bug->getCategoryID() . "," . $bug->getID() . ");' data-category-id='1' data-bug-id='1'>                                                            
                                                            <td><span class='hiddenSort'>" . $bug->getPriority()->getImportantValue() . "</span> <span class='label label-" . $bug->getPriority()->getColorName() . "'>" . $bug->getPriority()->getName() . "</span></td>
                                                            <td>" . $bug->getID() . "</td>
                                                            <td>$statusText</td>
                                                            <td><span class='hiddenSort'>" . $bug->getCreateTime() . "</span><span data-toggle='tooltip' data-placement='bottom' title='" . $bug->getCreateTime() . "'>" . Helper::humanTiming(strtotime($bug->getCreateTime())) . "</span></td>
                                                            <td>" . $bug->getTitle() . "</td>
                                                            <td><a href='/devControl/user/" . $bug->getUser()->getID() . "/" . Helper::formatURL($bug->getUser()->getUsername()) . "'>" . $bug->getUser()->getUsername() . "</a></td>
                                                        </tr>";                                                
                                            }
                                                                                       
                                $re .= "</tbody>
                                    </table>  
                                </div>
                            </div>";
                }
                else 
                {
                    $re .= Helper::noPermission(Permission::SHOW_BUGLIST);
                }                    
            break;

            case 'category-new-report':
                if(!Project::hasAccess(Category::getCategoryByID($_GET['categoryID'])->getProjectID(), unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::AlertNoProjectAccess();
                }

                if(Permission::hasPermission(Permission::BUG_CREATE, unserialize($_SESSION['user'])->getID()))
                {
                    $re .= "
                    <div class='panel panle-default'>
                        <div class='panel-body'>
                             <h3>New Report</h3>
                             <form class='form-horizontal' action='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug-create' method='POST'>
                                <div class='form-group'>
                                    <label class='control-label col-sm-2 col-md-1 col-lg-1' for='title'>Title:</label>
                                    <div class='col-sm-10 col-md-8'>
                                        <input type='text' name='title' class='form-control' placeholder='Enter title'>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='control-label col-sm-2 col-md-1 col-lg-1' for='description'>Description:</label>
                                    <div class='col-sm-10 col-md-8'>
                                        <textarea name='description' class='form-control' placeholder='Enter description'></textarea>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='control-label col-sm-2 col-md-1 col-lg-1' for='priority'>Priority:</label>
                                    <div class='col-sm-10 col-md-8'>
                                        <select name='priority' class='form-control'>";
                                        
                                        $priorities = Priority::getAllPriorities();
                                        $priorities->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                        for ($priorities->rewind(); $priorities->valid(); $priorities->next())
                                        {
                                            $priority = $priorities->current();
                                            $re .= "<option value='" . $priority->getID() . "'>" . $priority->getName() . "</option>";
                                        }

                                $re .= "</select>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <div class=' col-sm-offset-2 col-md-offset-1 col-lg-offset-1 col-sm-12 col-md-8'>
                                    <button type='submit' class='btn btn-warning'>Report</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>";
                }
                else
                {
                    return Helper::noPermission(Permission::BUG_CREATE);
                }

                break;

            case "category-bug-create":
                if(!Project::hasAccess(Category::getCategoryByID($_GET['categoryID'])->getProjectID(), unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::AlertNoProjectAccess();
                }

                if(Permission::hasPermission(Permission::BUG_CREATE, unserialize($_SESSION['user'])->getID()))
                {    
                    $categoryID = $_GET['categoryID'];
                    $priorityID = $_POST['priority'];
                    $title = $_POST['title'];
                    $description = $_POST['description'];
                    $userID = unserialize($_SESSION['user'])->getID();
                    $assignedToUserID = User::NOBODY;
                    $progress = 0;
                    $statusID = Status::NEW_REPORT;
                    $bugID = Bug::create($categoryID, $priorityID, $title, $description, $userID, $assignedToUserID, $progress, $statusID);
                    Helper::redirectTo("/devControl/bugtracker/category/$categoryID/bug/$bugID/show");
                }  
                else
                {
                    return Helper::noPermission(Permission::BUG_CREATE);
                }  
            break;

            case "bug-edit":
                if(!Project::hasAccess(Helper::getProjectIDByBugID($_GET['bugID']), unserialize($_SESSION['user'])->getID()))
                {
                   return Helper::AlertNoProjectAccess();
                }

                if(Permission::hasPermission(Permission::BUG_EDIT, unserialize($_SESSION['user'])->getID()))
                {
                    $bug = Bug::getBugByID($_GET['bugID']);

                    $re .= "<div class='panel panel-default'>
                                <div class='panel-body'>
                                    <form class='form-horizontal' action='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/apply-edit' method='POST'>
                                    <div class='form-group'>
                                        <label class='control-label col-sm-2 col-md-1 col-lg-1' for='title'>Title:</label>
                                        <div class='col-sm-10 col-md-8'>
                                            <input type='text' name='title' class='form-control' placeholder='Enter title' value='" . $bug->getTitle() . "' required='required'>
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <label class='control-label col-sm-2 col-md-1 col-lg-1' for='description'>Description:</label>
                                        <div class='col-sm-10 col-md-8'>
                                            <textarea name='description' class='form-control' placeholder='Enter description'>" . $bug->getDescription() . "</textarea>
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <label class='control-label col-sm-2 col-md-1 col-lg-1' for='priority'>Priority:</label>
                                        <div class='col-sm-10 col-md-8'>
                                            <select id='priority' name='priority' class='form-control js-example-basic-single'>";
                                            
                                            $priorities = Priority::getAllPriorities();
                                            $priorities->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                            for ($priorities->rewind(); $priorities->valid(); $priorities->next())
                                            {
                                                $priority = $priorities->current();
                                                $re .= "<option
                                                            value='" . $priority->getID() . "'
                                                            " . (($priority->getID() == $bug->getPriority()->getID()) ? "selected" : "") . ">
                                                            " . $priority->getName() . "
                                                        </option>";
                                            }

                                    $re .= "</select>
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <label class='control-label col-sm-2 col-md-1 col-lg-1' for='priority'>Status:</label>
                                        <div class='col-sm-10 col-md-8'>
                                            <select id='status' name='status' class='form-control js-example-basic-single' required='required'>";
                                            
                                            $status = Status::getAllStatus();
                                            $status->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                            for ($status->rewind(); $status->valid(); $status->next())
                                            {
                                                $s = $status->current();

                                                if($s->getID() == Status::FIXED)
                                                {
                                                    if($bug->getStatus()->getID() == Status::FIXED) 
                                                    {
                                                        $re .= "<option 
                                                                value='" . $s->getID() . "'
                                                                " . (($s->getID() == $bug->getStatus()->getID()) ? "selected" : "") . ">
                                                                " . $s->getName() . "
                                                            </option>";
                                                    }
                                                }
                                                else                                                
                                                {
                                                    $re .= "<option 
                                                                value='" . $s->getID() . "'
                                                                " . (($s->getID() == $bug->getStatus()->getID()) ? "selected" : "") . ">
                                                                " . $s->getName() . "
                                                            </option>";
                                                }                                                                     
                                            }

                                    $re .= "</select>
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <label class='control-label col-sm-2 col-md-1 col-lg-1' for='priority'>Assigned To:</label>
                                        <div class='col-sm-10 col-md-8'>
                                            <select id='assignedTo' name='assignedTo' class='form-control js-example-basic-single js-states'>";
                                            
                                            $users = User::getAllUsers();
                                            $users->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                            for ($users->rewind(); $users->valid(); $users->next())
                                            {
                                                $user = $users->current();
                                                $re .= "<option
                                                            value='" . $user->getID() . "'
                                                            " . (($user->getID() == $bug->getAssignedToUser()->getID()) ? "selected" : "") . ">
                                                            " . $user->getUserName() . "
                                                        </option>";
                                            }

                                    $re .= "</select>
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <label class='control-label col-sm-2 col-md-1 col-lg-1' for='priority'>Progress:</label>
                                        <div class='col-sm-10 col-md-8'>
                                        <input type='number' max='100' min='0' name='progress' value='" . $bug->getProgress() . "' required='required'>
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <div class=' col-sm-offset-2 col-md-offset-1 col-lg-offset-1 col-sm-12 col-md-8'>
                                        <button type='submit' class='btn btn-warning'>Save changes</button>
                                        <button type='reset' class='btn btn-warning'>Reset</button>
                                        </div>
                                    </div>
                                </form>        
                                </div>
                            </div>";
                }
                else
                {
                    return Helper::noPermission(Permission::BUG_EDIT);
                }  

            break;

            case "bug-delete":
                if(!Project::hasAccess(Helper::getProjectIDByBugID($_GET['bugID']), unserialize($_SESSION['user'])->getID()))   
                {
                    return Helper::AlertNoProjectAccess();
                } 

                if(Permission::hasPermission(Permission::BUG_DELETE, unserialize($_SESSION['user'])->getID()))
                {
                    if(!Change::existsForBug($_GET['bugID'])) 
                    {
                        Bug::delete($_GET['bugID']);
                        Helper::redirectTo("/devControl/bugtracker/category/" . $_GET['categoryID'] . "/show");
                    }
                    else
                    {
                        return "<div class='alert alert-dismissible alert-warning'>
                                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                                <strong>Bug couln't be deleted!</strong><br>
                                <strong>ErrorCode: </strong> chE-0 <br>
                                <strong>ErrorMessage: </strong> Please delete the Linked-Change first!<br>
                            </div>";
                    }                    
                } 
                else
                {
                    return Helper::noPermission(Permission::BUG_DELETE);
                }  
            break;

            case "bug-apply-edit":
                $re .= var_dump($_POST);

                if(!Project::hasAccess(Helper::getProjectIDByBugID($_GET['bugID']), unserialize($_SESSION['user'])->getID()))   
                {
                    return Helper::AlertNoProjectAccess();
                } 

                if(Permission::hasPermission(Permission::BUG_EDIT, unserialize($_SESSION['user'])->getID()))
                {
                    $bugID = $_GET['bugID'];
                    $priorityID = $_POST['priority'];
                    $title = $_POST['title'];
                    $description = $_POST['description'];
                    $assignedToUserID = $_POST['assignedTo'];
                    $progress = $_POST['progress'];
                    $status = $_POST['status'];

                    Bug::change($bugID, $priorityID, $title, $description, $assignedToUserID, $progress, $status);
                    Helper::redirectTo("/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/show");
                }

            break;

            case 'bug-show':
                $bug = Bug::getBugByID($_GET['bugID']);                
                
                if(!Project::hasAccess(Category::getCategoryByID($bug->getCategoryID())->getProjectID(), unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::AlertNoProjectAccess();
                }
                    

                if (Permission::hasPermission(Permission::BUG_VIEW, unserialize($_SESSION['user'])->getID()))
                {        
                    $options = "";                         
                    if(Permission::hasPermission(Permission::BUG_EDIT, unserialize($_SESSION['user'])->getID())) 
                    {
                        $options .= "<a href='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/edit' class='btn btn-default'><span class='glyphicon glyphicon-pencil'></span> Bearbeiten</a> ";
                    } 

                    if(Permission::hasPermission(Permission::BUG_DELETE, unserialize($_SESSION['user'])->getID()))
                    {
                        $options .= "<a href='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/delete' class='btn btn-default'><span class='glyphicon glyphicon-trash'></span> Löschen</a>";
                    } 
                                       

                    $re .= "<div class='row'>
                                <div class='col-sm-12'>
                                    <div class='panel panel-default'>
                                        <div class='panel-body'>

                                            <ul class='nav nav-tabs'>
                                                <li class='active'><a data-toggle='tab' href='#details'>Details</a></li>
                                                <li><a data-toggle='tab' href='#comments'>Comments <span class='badge'>" . $bug->getComments()->count() . "</span></a></li>
                                                <li><a data-toggle='tab' href='#attachments'>Attachments</a></li>
                                                <li><a data-toggle='tab' href='#history'>History</a></li>
                                            </ul>

                                            <div class='tab-content'>
                                                <div id='details' class='tab-pane fade in active'>
                                                    <table class='table table-striped table-bordered'>
                                                        <tr>
                                                            <th class='col-sm-2'>BugID</th>
                                                            <td>" . $bug->getID() . "</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Title</th>
                                                            <td class='text-info'>" . $bug->getTitle() . "</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Description</th>
                                                            <td class='text-info'>" . $bug->getDescription() . "</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Reporter</th>
                                                            <td><a href='/devControl/user/" . $bug->getUser()->getID() . "/" . Helper::formatURL($bug->getUser()->getUsername()) . "'>" . $bug->getUser()->getUsername() . "</a></td>
                                                        </tr>
                                                        <tr>
                                                            <th>CreateTime</th>
                                                            <td><span data-toggle='tooltip' data-placement='bottom' title='" . $bug->getCreateTime() . "'>" . Helper::humanTiming(strtotime($bug->getCreateTime())) . "</span> before</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Category</th>
                                                            <td>" . Category::getCategoryByID($bug->getCategoryID())->getName() . "</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Priority</th>
                                                            <td><span class='label label-" . $bug->getPriority()->getColorName() . "'>" . $bug->getPriority()->getName() . "<span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status</th>
                                                            <td>" . $bug->getStatus()->getName() . "</td>
                                                        </tr>                                                        
                                                        <tr>
                                                            <th>Progress</th>
                                                            <td>
                                                                <div class='progress'>
                                                                    " . generateProgressBar($bug->getProgress()) . "   
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr><td colspan='2' class='text-right'>$options</td></tr>
                                                    </table>
                                                </div>

                                                <div id='comments' class='tab-pane fade'>
                                                    <h3>Comments</h3>
                                                    <div class=''>
                                                        " . generateComments($bug->getComments()) . "
                                                    </div>
                                                    " . ((Permission::hasPermission(Permission::COMMENT_CREATE, unserialize($_SESSION['user'])->getID())) ? "<form action='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/create-comment' method='POST'>
                                                        <div class='form-group'>
                                                            <label for='comment'>Comment:</label>
                                                            <textarea class='form-control' rows='5' name='comment' placeholder='Comment here'></textarea>                                                          
                                                            <input type='submit' value='Post' class='btn btn-default comment-button' />                                                    
                                                        </div>
                                                    </form>" : "<div class='alert alert-info'><span class='glyphicon glyphicon-lock'></span> You have no Permission to write Comments</div>") . "                                                
                                                </div>
                                                <div id='attachments' class='tab-pane fade'>                                                    
                                                    <div class='row'>
                                                        <div class='col-sm-6'>
                                                            <h3>Attachments</h3>    
                                                        </div>
                                                        <div class='col-sm-6 text-right'>
                                                            <a id='btn_file_upload' class='btn btn-md btn-default' style='margin-top: 10px;' href='#'><span class='glyphicon glyphicon-upload'></span> Upload File</a>
                                                        </div>
                                                    </div>
                                                    " . generateAttachments($bug->getAttachments()) . "
                                                </div>
                                                <div id='history' class='tab-pane fade'>
                                                    <h3>History</h3>
                                                    " . generateHistory($bug->getHistory()) . "
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>";

                }
                else
                {
                    return Helper::noPermission(Permission::BUG_VIEW);                        
                }              
            break;

            case "bug-create-comment":
                if(!Project::hasAccess(Category::getCategoryByID($_GET['categoryID'])->getProjectID(), unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::AlertNoProjectAccess();
                }

                if (Permission::hasPermission(Permission::COMMENT_CREATE, unserialize($_SESSION['user'])->getID()))
                {
                    $bugID = mysql_real_escape_string($_GET['bugID']);
                    $categoryID = mysql_real_escape_string($_GET['categoryID']);
                    Comment::createComment($bugID, mysql_real_escape_string($_POST['comment']));

                    Helper::redirectTo("/devControl/bugtracker/category/$categoryID/bug/$bugID/show#comments", 0);
                }
                else
                {
                    return Helper::noPermission(Permission::COMMENT_CREATE);
                }                 
            break;

            case "comment-delete":
                $comment = Comment::getCommentByID($_GET['commentID']);

                if(!Project::hasAccess(Helper::getProjectIDByBugID($comment->getBugID()) , unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::AlertNoProjectAccess();             
                }

                if(Permission::hasPermission(Permission::COMMENT_DELETE, unserialize($_SESSION['user'])->getID()))
                {
                    Comment::delete($_GET['commentID']);
                    Helper::redirectTo("/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $comment->getBugID() . "/show#comments");
                }
                else
                {
                    return Helper::noPermission(Permission::COMMENT_DELETE);
                }           
            break;

            case "comment-edit":
                $comment = Comment::getCommentByID($_GET['commentID']);

                if(!Project::hasAccess(Helper::getProjectIDByBugID($comment->getBugID()) , unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::AlertNoProjectAccess();             
                }

                if(Permission::hasPermission(Permission::COMMENT_EDIT, unserialize($_SESSION['user'])->getID()) || $comment->getUser()->getID() == unserialize($_SESSION['user'])->getID())
                {
                    $re .= "<div class='panel panel-default'>
                                <div class='panel-body'>
                                    <form action='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/comment/" . $comment->getID() . "/apply-edit' method='POST'>
                                        <div class='form-group'>
                                            <label for='comment'>Edit Comment:</label>
                                            <textarea class='form-control' rows='5' name='comment' placeholder='Comment here'>" . $comment->getContent() . "</textarea>                                                          
                                            <input type='submit' value='Post' class='btn btn-default comment-button' />                                                    
                                        </div>
                                    </form>                                        
                                </div>
                            </div>";
                }
                else
                {
                    return Helper::noPermission(Permission::COMMENT_EDIT);
                }                
            break;

            case "comment-apply-edit":
                $comment = Comment::getCommentByID($_GET['commentID']);

                if(!Project::hasAccess(Helper::getProjectIDByBugID($comment->getBugID()) , unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::AlertNoProjectAccess();             
                }

                if(Permission::hasPermission(Permission::COMMENT_DELETE, unserialize($_SESSION['user'])->getID()) || $comment->getUser()->getID() == unserialize($_SESSION['user'])->getID())
                {
                    if(isset($_POST['comment'])) 
                    {
                        Comment::change($comment->getID(), $_POST['comment']);
                        //Helper::redirectTo("/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $comment->getBugID() . "/show#comments", 0);
                    }
                }

                // || $comment->getUser()->getID() == unserialize($_SESSION['user'])->getID()
            break;

            case "bug-file-upload":                               
                if(Permission::hasPermission(Permission::FILE_UPLOAD, unserialize($_SESSION['user'])->getID()))
                {
                    if(isset($_GET['bugID']) && $_FILES['uploadedFile']['size'] > 0 && isset($_FILES['uploadedFile']['size']) && $_FILES['uploadedFile']['size']  < 20000000)     //Maximalde Dateigröße auf 20MB begrenzen 
                    {
                        if(Project::hasAccess(Helper::getProjectIDByBugID($_GET['bugID']) , unserialize($_SESSION['user'])->getID()))
                        {
                            Attachment::uploadFile($_GET['bugID'], $_FILES['uploadedFile']); 
                            Helper::redirectTo("/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/show#attachments");                      
                        }
                        else
                        {
                            return Helper::AlertNoProjectAccess(); 
                        }
                    }
                    else { Helper::redirectTo("/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/show#attachments"); }
                }
                else
                {
                    return Helper::noPermission(Permission::FILE_UPLOAD);
                }            
            break;

            case "file-delete":
                if(Permission::hasPermission(Permission::FILE_DELETE, unserialize($_SESSION['user'])->getID()))
                {
                    if (Project::hasAccess(Helper::getProjectIDByAttachmentID($_GET['fileID']), unserialize($_SESSION['user'])->getID())) 
                    {
                        Attachment::deleteFile($_GET['fileID']);
                        Helper::redirectTo("/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/show#attachments");                
                    }
                    else
                    {
                        return Helper::AlertNoProjectAccess(); 
                    }
                }
                else
                {
                    return Helper::noPermission(Permission::FILE_DELETE);
                }
            break;
            
            default:
                if (!isset($_GET['categoryID']))
                {
                    $re .= "<div class='alert alert-info'>
                            <strong>No Category selected!</strong> Please select any Category to view the Buglist.
                            </div>";
                }                                               

                $re .= "<div class='alert alert-warning'>
                            Act <strong>" . (isset($_GET['act']) ? $_GET['act']: "")  . "</strong> could not be excecuted!
                        </div>";
            break;
        }

        return $re;
    }
   

    function generateProgressBar($value) 
    {
        
        if($value <= 33) {
            $bar = "<div class='progress-bar progress-bar-danger' role='progressbar' aria-valuenow='$value' aria-valuemin='0' aria-valuemax='100' style='width: $value%'>
                    " . $value . "%
                    </div>";
        } elseif ($value > 33 && $value <= 66) {
            $bar = "<div class='progress-bar progress-bar-warning' role='progressbar' aria-valuenow='$value' aria-valuemin='0' aria-valuemax='100' style='width: $value%'>
                   " . $value . "%
                   </div>";
        } else {
            $bar = "<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='$value' aria-valuemin='0' aria-valuemax='100' style='width: $value%'>
            " . $value . "%
            </div>";
        }
        
        return $bar;
    }

    function generateComments($comments)
    {
        $re = "";
        if ($comments->count() < 1)
        {
            $re .= "<div class='alert alert-info'><strong>No Comments!</strong> Write the first comment here...</div>";
        }
        else
        {
            $comments->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
            for ($comments->rewind(); $comments->valid(); $comments->next())
            {
                $comment = $comments->current();
                $re .= "<div class='panel panel-default' style='margin-right: 15px;'>
                            <div class='panel-body'>
                                " . $comment->getContent() . "
                            </div>
                            <div class='panel-footer'>
                                <div class='row'>
                                    <div class='col-sm-6'>
                                        <span class='glyphicon glyphicon-time'></span> <span data-toggle='tooltip' data-placement='bottom' title='" . $comment->getCreateTime() . "'>" . Helper::humanTiming(strtotime($comment->getCreateTime())) . "</span> before
                                    </div>
                                    <div class='col-sm-6 text-right'>
                                        Posted by <a href='/devControl/user/" . $comment->getUser()->getID() . "/" . Helper::formatURL($comment->getUser()->getUsername()) . "'>" . $comment->getUser()->getUsername() . "</a>
                                        <div class='btn-group'>
                                            <button style='margin-left: 5px;' type='button' class='btn btn-xs btn-default dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                <span class='glyphicon glyphicon-cog'></span> <span class='caret'></span>
                                            </button>
                                            <ul class='dropdown-menu'>
                                                <li><a href='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/comment/" . $comment->getID() . "/edit'><span class='glyphicon glyphicon-pencil'></span> Bearbeiten</a></li>
                                                <li><a href='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/comment/" . $comment->getID() . "/delete'><span class='glyphicon glyphicon-trash'></span> Löschen</a></li>                                                
                                            </ul>
                                        </div>   
                                    </div>
                                </div>
                            </div>
                        </div>";
            }
        }        

        return $re;
    }

    function generateAttachments($attachments)
    {
        $re = "";

        if ($attachments->count() < 1)
        {
            $re .= "<div class='alert alert-info'><strong>No Attachments!</strong> Upload the first File.</div>";
        }
        else
        {
            $re .= "<table class='table table-striped table-hover'>                            
                        <tr>
                            <th></th><th>Filename</th><th>Size</th><th></th>
                        </tr>";

            $attachments->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
            for ($attachments->rewind(); $attachments->valid(); $attachments->next())
            {
                $attachment = $attachments->current();
                $options = "";

                if(Permission::hasPermission(Permission::FILE_DOWNLOAD, unserialize($_SESSION['user'])->getID()) && Permission::hasPermission(Permission::FILE_DELETE, unserialize($_SESSION['user'])->getID())) 
                {
                    $options = "<div class='btn-group'>
                                    <a type='button' class='btn btn-default' href='/devControl/bugtracker/file-download/" . $attachment->getID() . "'><span class='glyphicon glyphicon-download'></span> Download</a>
                                    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        <span class='caret'></span>
                                        <span class='sr-only'>Menü ein-/ausblenden</span>
                                    </button>
                                    <ul class='dropdown-menu'>
                                        <li><a href='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/file-delete/" . $attachment->getID() . "'><span class='glyphicon glyphicon-trash'></span> Löschen</a></li>
                                    </ul>
                                </div>";
                } 
                else if(Permission::hasPermission(Permission::FILE_DOWNLOAD, unserialize($_SESSION['user'])->getID()))
                {
                    $options = "<a href='/devControl/bugtracker/file-download/" . $attachment->getID() . "' class='btn btn-default'><span class='glyphicon glyphicon-download'></span></a>";
                } 
                else if(Permission::hasPermission(Permission::FILE_DELETE, unserialize($_SESSION['user'])->getID()))
                {
                    $options = "<a href='/devControl/bugtracker/category/" . $_GET['categoryID'] . "/bug/" . $_GET['bugID'] . "/file-delete/" . $attachment->getID() . "' class='btn btn-default'><span class='glyphicon glyphicon-trash'></span></a>";
                } 
                                
                $re .= 
                "<tr>
                    <td class='col-sm-1'>
                        <img src='" . getIconName($attachment->getName()) . "'>
                    </td>
                    <td>
                        " . $attachment->getName() . "
                    </td>
                    <td>
                        " . Helper::formatBytes($attachment->getSize()) . "
                    </td>
                    <td class='col-sm-2 text-right'>
                        $options
                    </td>
                </tr>";
            }

            $re .= "</table>";
        }

        return $re;
    }

    function generateHistory($history)
    {
        $re = "";

        $re .= "<table class='table table-striped'>
                <tr><th class='col-sm-2'>Date</th><th>Change</th></tr>";        

        $history->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
        for ($history->rewind(); $history->valid(); $history->next())
        {
            $historyEntry = $history->current();

            $re .= 
            "<tr>                
                <td><span data-toggle='tooltip' data-placement='bottom' title='" . $historyEntry->getCreateTime() . "'>" . Helper::humanTiming(strtotime($historyEntry->getCreateTime())) . "</span></td>
                <td>" . $historyEntry->getContent() . "</td>
            </tr>";                                                                   
        }
        $re .= "</table>";

        return $re;
    }

    function getIconName($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(file_exists("/devControl/images/file-icons/$filename.png"))
        {            
            return "/devControl/images/file-icons/$filename.png";            
        }
        return "/devControl/images/file-icons/blank.png";
    }
?>