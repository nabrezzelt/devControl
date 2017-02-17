<?php
    require_once("includes/connect.inc.php");
    require_once("includes/Autoloader.Class.php");
    Autoloader::load();

    function handler()
    {
        $re = "<div class='panel panel-default'>
                <div class='panel-body'>";
        if(isset($_GET['act'])) 
        {
            $act = $_GET['act'];
        }
        else
        {
            $act = "default";
        }

        if(!Permission::hasPermission(Permission::CHANGELOG_VIEW, unserialize($_SESSION['user'])->getID()))
        {
            return Helper::noPermission(Permission::CHANGELOG_VIEW);
        }

        switch ($act) {
            case 'change-delete':
                if(isset($_GET['changeID']) && Permission::hasPermission(Permission::CHANGELOG_DELETE_CHANGE, unserialize($_SESSION['user'])->getID())) 
                {
                    $change = Change::getChangeByID($_GET['changeID']);
                    
                    if(!Project::hasAccess($change->getProjectID(), unserialize($_SESSION['user'])->getID())) 
                    {
                        return Helper::AlertNoProjectAccess(); 
                    }    

                    Change::delete($change->getID());
                    Helper::redirectTo("/devControl/changelog");                
                }
                else
                {
                    return Helper::noPermission(Permission::CHANGELOG_DELETE_CHANGE);
                }
            break;

            case 'change-edit':
                if(isset($_GET['changeID']) && Permission::hasPermission(Permission::CHANGELOG_EDIT_CHANGE, unserialize($_SESSION['user'])->getID()))
                {
                    $change = Change::getChangeByID($_GET['changeID']);

                    if(!Project::hasAccess($change->getProjectID(), unserialize($_SESSION['user'])->getID())) 
                    {
                        return Helper::AlertNoProjectAccess(); 
                    }

                    $re .= "
                    <div class='panel panle-default'>
                        <div class='panel-body'>
                             <form class='form-horizontal' action='/devControl/changelog/" . $_GET['changeID'] . "/apply-edit' method='POST'>
                                <div class='form-group'>
                                    <label class='control-label col-sm-2 col-md-1 col-lg-1' for='title'>Description:</label>
                                    <div class='col-sm-10 col-md-8'>
                                        <input type='text' name='description' class='form-control' placeholder='Change description' value='" . $change->getDescription() . "' required='required'>
                                    </div>
                                </div>                               
                                <div class='form-group'>
                                    <label class='control-label col-sm-2 col-md-1 col-lg-1' for='priority'>Fixed Bugs:</label>
                                    <div class='col-sm-10 col-md-8'>
                                        <select id='bugs' name='bugs[]' class='form-control js-example-basic-multiple js-states' multiple='multiple'>";                                                                           

                                        $changeBugs = $change->getBugs();
                                        $changeBugs->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                        for ($changeBugs->rewind(); $changeBugs->valid(); $changeBugs->next())
                                        {
                                            $changeBug = $changeBugs->current();
                                            
                                            $re .= "<option
                                                        value='" . $changeBug->getID() . "'
                                                        selected>"
                                                        . $changeBug->getTitle() . "
                                                    </option>";
                                        }

                                        $bugs = Bug::getUnfixedBugsByProjectID($change->getProjectID());
                                        $bugs->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                        for ($bugs->rewind(); $bugs->valid(); $bugs->next())
                                        {
                                            $bug = $bugs->current();
                                            $re .= "<option
                                                        value='" . $bug->getID() . "'>"
                                                        . $bug->getTitle() . "
                                                    </option>";
                                        }
                                        
                                        /*
                                        $bugs = Bug::getBugsByProjectID($change->getProjectID());
                                        $bugs->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                        for ($bugs->rewind(); $bugs->valid(); $bugs->next())
                                        {
                                            $bug = $bugs->current();
                                            $selected = false;

                                            $changeBugs = $change->getBugs();
                                            $changeBugs->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                            for ($changeBugs->rewind(); $changeBugs->valid(); $changeBugs->next())
                                            {
                                                $changeBug = $changeBugs->current();
                                                if($bug->getID() == $changeBug->getID()) 
                                                {
                                                    $selected = true;
                                                    break;                                                    
                                                }                                                
                                            }
                                            
                                            $re .= "<option
                                                        value='" . $bug->getID() . "'
                                                        " . (($selected) ? "selected" : "") . ">"
                                                        . $bug->getTitle() . "
                                                    </option>";
                                        }
                                        */

                                $re .= "</select>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <div class=' col-sm-offset-2 col-md-offset-1 col-lg-offset-1 col-sm-12 col-md-8'>
                                        <button type='submit' class='btn btn-warning'>Save Change</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>";
                } 
                else
                {
                    return Helper::noPermission(Permission::CHANGELOG_EDIT_CHANGE);
                }                                   
            break;

            case 'change-apply-edit':
                $change = Change::getChangeByID($_GET['changeID']);
                if(!Permission::hasPermission(Permission::CHANGELOG_EDIT_CHANGE, unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::noPermission(Permission::CHANGELOG_EDIT_CHANGE);
                }                                

                if(!Project::hasAccess($change->getProjectID(), unserialize($_SESSION['user'])->getID()))
                {                    
                     return Helper::AlertNoProjectAccess(); 
                }

                Change::removeLinkedBugs($change->getID());

                $change->setBugs(new SplDoublyLinkedList());

                $ids = array();
                $i = 0;
                foreach($_POST['bugs'] as $key => $value)
                {
                    //$re .= "$key, $value";
                    if(Project::hasAccess(Helper::getProjectIDByBugID($value), unserialize($_SESSION['user'])->getID()))
                    {
                        $ids[$i] = $value;
                        $i++;
                    }                    
                }                

                Change::change($change->getID(), $_POST['description'], $ids);
                Helper::redirectTo("/devControl/changelog");                    
            break;

            case 'change-new':
                //<select class="js-example-basic-multiple js-states form-control" id="id_label_multiple" multiple="multiple"></select>
                if(!Project::hasAccess($_GET['projectID'], unserialize($_SESSION['user'])->getID())) 
                {
                    return Helper::AlertNoProjectAccess(); 
                }

                if(!Permission::hasPermission(Permission::CHANGELOG_CREATE_CHANGE, unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::noPermission(Permission::CHANGELOG_CREATE_CHANGE);
                }

                $re .= "
                    <div class='panel panle-default'>
                        <div class='panel-body'>
                             <h3>New Change</h3>
                             <form class='form-horizontal' action='/devControl/changelog/project/" . $_GET['projectID'] . "/change-create' method='POST'>
                                <div class='form-group'>
                                    <label class='control-label col-sm-2 col-md-1 col-lg-1' for='title'>Description:</label>
                                    <div class='col-sm-10 col-md-8'>
                                        <input type='text' name='description' class='form-control' placeholder='Change description' required='required'>
                                    </div>
                                </div>                               
                                <div class='form-group'>
                                    <label class='control-label col-sm-2 col-md-1 col-lg-1' for='priority'>Fixed Bugs:</label>
                                    <div class='col-sm-10 col-md-8'>
                                        <select id='bugs' name='bugs[]' class='form-control js-example-basic-multiple js-states' multiple='multiple'>";                                                                           

                                        $bugs = Bug::getUnfixedBugsByProjectID($_GET['projectID']);
                                        $bugs->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                        for ($bugs->rewind(); $bugs->valid(); $bugs->next())
                                        {
                                            $bug = $bugs->current();
                                            $re .= "<option value='" . $bug->getID() . "'>" . $bug->getTitle() . "</option>";
                                        }

                                $re .= "</select>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <div class=' col-sm-offset-2 col-md-offset-1 col-lg-offset-1 col-sm-12 col-md-8'>
                                        <button type='submit' class='btn btn-warning'>Create Change</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>";

            break;

            case 'change-create':
                if(!Permission::hasPermission(Permission::CHANGELOG_CREATE_CHANGE, unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::noPermission(Permission::CHANGELOG_CREATE_CHANGE);
                }                                

                if(!Project::hasAccess($_GET['projectID'], unserialize($_SESSION['user'])->getID()))
                {                    
                     return Helper::AlertNoProjectAccess(); 
                }

                $ids = array();
                $i = 0;
                foreach($_POST['bugs'] as $key => $value)
                {
                    //$re .= "$key, $value";
                    if(Project::hasAccess(Helper::getProjectIDByBugID($value), unserialize($_SESSION['user'])->getID()))
                    {
                        $ids[$i] = $value;
                        $i++;
                    }                    
                }                

                Change::create($_GET['projectID'], $_POST['description'], $ids);
                Helper::redirectTo("/devControl/changelog");
            break;

            default:
                if(!Permission::hasPermission(Permission::CHANGELOG_VIEW, unserialize($_SESSION['user'])->getID()))
                {
                    return Helper::noPermission(Permission::CHANGELOG_VIEW);
                }

                $re .= "<div class='panel-group' id='accordion'>";

                $projects = Project::getAllProjects();

                $projects->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                for ($projects->rewind(); $projects->valid(); $projects->next())
                {
                    $project = $projects->current();
                    if(Project::hasAccess($project->getID(), unserialize($_SESSION['user'])->getID())) 
                    {
                        $re .= "<div class='panel panel-default'>
                                    <div class='panel-heading'>
                                        <h4 class='panel-title'>
                                            <div class='row'>
                                                <div class='col-sm-8'>
                                                    Project: <a data-toggle='collapse' data-parent='#accordion' href='#project-" . $project->getID() . "'>
                                                    " . $project->getName() . "</a>    
                                                </div>
                                                <div class='col-sm-4 text-right'>
                                                    <a href='/devControl/changelog/project/" . $project->getID() . "/change-new' class='btn btn-default btn-xs'><span class='glyphicon glyphicon-plus'></span> New Change</a>    
                                                </div>
                                            </div>
                                            
                                        </h4>
                                    </div>
                                    <div id='project-" . $project->getID() . "' class='panel-collapse collapse in'>
                                        <div class='panel-body'>
                                            <table class='table'>";

                                            $changes = Change::getAllChangesByProjectID($project->getID());
                                            $changes->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                            for ($changes->rewind(); $changes->valid(); $changes->next()) 
                                            {
                                                $change = $changes->current();

                                                $re .= "<tr><td class='col-sm-3'>[<a href='/devControl/user/" . $change->getUser()->getID() . "/" . Helper::formatURL($change->getUser()->getUsername()) . "'>" . $change->getUser()->getUsername() . "</a>] fixed <span data-toggle='tooltip' data-placement='bottom' title='" . $change->getChangeDate() . "'>" . Helper::humanTiming(strtotime($change->getChangeDate())) . "</span> before: </td><td>" . $change->getDescription() . "</td>";
                                                $re .= "<td>";

                                                $bugs = $change->getBugs();
                                                $bugs->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                                for ($bugs->rewind(); $bugs->valid(); $bugs->next())
                                                {
                                                    $bug = $bugs->current();
                                                    $re .= "<a
                                                                class='label label-" . $bug->getPriority()->getColorName() . "' 
                                                                title='" . $bug->getTitle() . "' 
                                                                data-toggle='popover' 
                                                                data-trigger='hover' 
                                                                data-content='" . $bug->getDescription() . "' 
                                                                data-placement='auto bottom' 
                                                                href='/devControl/bugtracker/category/" . $bug->getCategoryID() . "/bug/" . $bug->getID() . "/show'>
                                                                Bug #" . $bug->getID() . " 
                                                                    " . (($bug->getStatus()->getID() == Status::FIXED) ? "<span class='glyphicon glyphicon-ok'></span>": "") . "                                                                
                                                            </a>&nbsp;";
                                                } 

                                                $re .= "</td>";

                                                if(Permission::hasPermission(Permission::CHANGELOG_EDIT_CHANGE, unserialize($_SESSION['user'])->getID()) && Permission::hasPermission(Permission::CHANGELOG_DELETE_CHANGE, unserialize($_SESSION['user'])->getID())) 
                                                {
                                                    $re .= "<td class='text-right'>
                                                            <div class='btn-group'>
                                                                <button style='margin-left: 5px;' type='button' class='btn btn-xs btn-default dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                                    <span class='glyphicon glyphicon-cog'></span> <span class='caret'></span>
                                                                </button>
                                                                <ul class='dropdown-menu'>
                                                                    <li><a href='/devControl/changelog/" . $change->getID() . "/edit'><span class='glyphicon glyphicon-pencil'></span> Bearbeiten</a></li>
                                                                    <li><a href='/devControl/changelog/" . $change->getID() . "/delete'><span class='glyphicon glyphicon-trash'></span> Löschen</a></li>                                                
                                                                </ul>
                                                            </div>
                                                        </td>";
                                                }
                                                else if(Permission::hasPermission(Permission::CHANGELOG_EDIT_CHANGE, unserialize($_SESSION['user'])->getID()))
                                                {
                                                    $re .= "<td class='text-right'>
                                                                <a class='btn btn-default btn-xs' href='/devControl/changelog/" . $change->getID() . "/edit'><span class='glyphicon glyphicon-pencil'></span> Bearbeiten</a>
                                                            </td>";
                                                }
                                                else if(Permission::hasPermission(Permission::CHANGELOG_DELETE_CHANGE, unserialize($_SESSION['user'])->getID()))
                                                {
                                                    $re .= "<td class='text-right'>
                                                                <a class='btn btn-default btn-xs' href='/devControl/changelog/" . $change->getID() . "/delete'><span class='glyphicon glyphicon-trash'></span> Löschen</a>
                                                            </td>";
                                                }

                                            $re .= "</tr>";
                                            }

                                    $re .= "</table>
                                            <small class='text-muted'><span class='glyphicon glyphicon-ok'></span> means fixed.</small>                                    
                                        </div>
                                    </div>
                                </div>";

                    }

                }

                $re .= "</div>";
            break;


                
        }

        $re .= "</div>
            </div>";

        return $re;
    }
?>
    