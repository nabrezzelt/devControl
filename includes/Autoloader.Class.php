<?php
    /**
     * 
     */
    class AutoLoader
    {
        
       public static function Load()
       {
            require_once("classes/exceptions/SQLException.Class.php");
            require_once("classes/BasicEnum.Class.php");
            require_once("classes/Priority.Class.php");
            require_once("classes/Bug.Class.php");
            require_once("classes/Crypt.Class.php");
            require_once("classes/Category.Class.php");
            require_once("classes/User.Class.php");
            require_once("classes/Status.Class.php");
            require_once("classes/Project.Class.php");
            require_once("classes/Permission.Class.php");
            require_once("classes/ExtendedPermission.Class.php");
            require_once("classes/Group.Class.php");
            require_once("classes/Change.Class.php");
            
            require_once("classes/Comment.Class.php");
            require_once("classes/History.Class.php");
            require_once("classes/Attachment.Class.php");

            require_once("classes/Helper.Class.php");

       }
    }
?>