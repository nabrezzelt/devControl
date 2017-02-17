<?php
    /**
     * 
     */
    class Permission 
    {
        const SHOW_BUGLIST = 1;
        const COMMENT_CREATE = 2;   
        const BUG_VIEW = 3;
        const BUG_CREATE = 5;
        const FILE_DOWNLOAD = 6;
        const FILE_UPLOAD = 7;
        const FILE_DELETE = 8;
        const BUG_EDIT = 9;
        const BUG_DELETE = 10;  
        const CHANGELOG_VIEW = 11;  
        const CHANGELOG_CREATE_CHANGE = 12;  
        const CHANGELOG_EDIT_CHANGE = 13;
        const CHANGELOG_DELETE_CHANGE = 14; 
        const COMMENT_DELETE = 15;
        const COMMENT_EDIT = 16;
        const ACCOUNT_VIEW_ASSIGNED_BUGS = 17;
        const ACCOUNT_VIEW_OPENED_BUGS = 18;
        const USER_SHOW_DATA = 19;
        const ADMIN_USER_BAN = 20;
        const USER_VIEW_BANSTATE = 21;
        const USER_VIEW_EMAIL = 22;
        const USER_VIEW_LAST_IP = 23;
        const USER_VIEW_LAST_LOGIN = 24;
        const USER_VIEW_REGISTER_DATE = 25;
        const ADMIN_VIEW_USER_DETAILS = 26;

        const ADMIN_VIEW_USER_GROUPS = 27;
        const ADMIN_VIEW_USER_PERMISSIONS = 28;
        const ADMIN_ADD_USER_TO_GROUP = 29;
        const ADMIN_CHANGE_USER_PERMISSIONS = 30;
        const ADMIN_VIEW_GROUPLIST = 31;
        const ADMIN_VIEW_GROUP_MEMBERS = 32;
        const ADMIN_VIEW_GROUP_DETAILS = 33;
        const ADMIN_VIEW_GROUP_PERMISSIONS = 34;
        const ADMIN_CHANGE_GROUP_PERMISSIONS = 35;
        const ADMIN_REMOVE_USER_FROM_GROUP = 36;
        const ADMIN_CREATE_GROUP = 37;
        const ADMIN_DELETE_GROUP = 38;
        const ADMIN_VIEW_USERLIST = 39;


        private $id;
        private $name;
        private $errorCode;
        private $errorMessage;
        
        function __construct($id, $name, $errorCode, $errorMessage)
        {
            $this->id = $id;
            $this->name = $name;
            $this->errorCode = $errorCode;
            $this->errorMessage = $errorMessage;            
        }
    
        public function setId($id)
        {
            $this->id = $id;
        }

        public function getId()
        {
            return $this->id;
        }

        public function setName($name)
        {
            $this->name = $name;
        }

        public function getName()
        {
            return $this->name;
        }

        public function setErrorCode($errorCode)
        {
            $this->errorCode = $errorCode;
        }

        public function getErrorCode()
        {
            return $this->errorCode;
        }

        public function setErrorMessage($errorMessage)
        {
            $this->errorMessage = $errorMessage;
        }

        public function getErrorMessage()
        {
            return $this->errorMessage;
        }

        /**
        * undocumented function summary
        *
        * Undocumented function long description
        *
        * @param type var Description
        **/    
        public static function hasPermission($permissionID, $userID) 
        {
            $query =   "SELECT permissionID
                        FROM 
                        (
                            SELECT group_permissions.permissionID
                            FROM group_user_relation
                            JOIN group_permissions
                            ON group_user_relation.groupID = group_permissions.groupID
                            WHERE (userID = $userID AND permissionID = $permissionID)
                        ) AS grp_permissions
                        UNION
                            SELECT permissionID
                            FROM 
                            (
                                SELECT permissionID
                                FROM user_permissions
                                WHERE (userID = $userID AND permissionID = $permissionID)
                        ) AS acc_permissions";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            if (mysql_num_rows($res) == 1) 
            {
                return true;
            }
            return false;
        }

        /**
         * undocumented function summary
         *
         * Undocumented function long description
         *
         * @param type var Description
         **/
        public static function getPermissionByID($id)
        {
            $query = "SELECT * FROM permissions WHERE id = '$id'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            $row = mysql_fetch_assoc($res);

            return new Permission((int) $row['id'], $row['name'],(int) $row['errorCode'], $row['errorMessage']);
        }

        public static function getAllPermissionsByGroup($groupID)
        {
            $query = "SELECT groupID, permissions.*
                      FROM group_permissions
                      JOIN permissions 
                      ON group_permissions.permissionID = permissions.ID
                      WHERE groupID = '$groupID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $permissions = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $id = $row['id'];                
                $name = $row['name'];
                $errorCode = $row['errorCode'];
                $errorMessage = $row['errorMessage'];

                $permissions->push(new Permission($id, $name, $errorCode, $errorMessage));
            }

            return $permissions;
        }
        
        public static function saveUserPermissions($userID, $permissions)
        {
            Permission::removeAllPermissionsFromUser($userID);

            foreach ($permissions as $i => $value) 
            {
                $id = $permissions[$i];

                $query = "INSERT INTO user_permissions (userID, permissionID) VALUES ($userID, $id)";
                $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            }           
        }

        public static function saveGroupPermissions($groupID, $permissions)
        {
            Permission::removeAllPermissionsFromGroup($groupID);

            foreach ($permissions as $i => $value) 
            {
                $id = $permissions[$i];

                $query = "INSERT INTO group_permissions (groupID, permissionID) VALUES ($groupID, $id)";
                $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            } 
        }
        
        public static function removeAllPermissionsFromUser($userID)
        {
            $query = "DELETE FROM user_permissions WHERE userID = $userID";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
        }

        public static function removeAllPermissionsFromGroup($groupID)
        {
            $query = "DELETE FROM group_permissions WHERE groupID = $groupID";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
        }
    }
    
?>