<?php
    require_once("includes/connect.inc.php");
    require_once("includes/Autoloader.Class.php");
    Autoloader::load();

    if(!isset($_GET['fileID'])) return;

    if(Project::hasAccess(Helper::getProjectIDByAttachmentID($_GET['fileID']), unserialize($_SESSION['user'])->getID()))
    {
        $attachment = Attachment::getAttachmentByID($_GET['fileID']);

        $attachment->download();
    }
?>