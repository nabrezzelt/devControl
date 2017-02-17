<?php
    /**
     * 
     */
    class Project
    {
        private $id;
        private $name;

        function __construct($id, $name)
        {
            $this->id = $id;
            $this->name = $name;
        }

        public function getID()
        {
            return $this->id;
        }

        public function getName()
        {
            return $this->name;
        }

        public static function getAllProjects() 
        {
            $query = "SELECT * FROM projects";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            $projectList = new SplDoublyLinkedList();
            
            while($row = mysql_fetch_assoc($res))
            {
                $projectList->push(new Project($row['id'], $row['name']));
            }

            return $projectList;
        }

        public static function hasAccess($projectID, $userID)
        {
            $query = "SELECT * FROM project_user_relation WHERE projectID = '$projectID' AND userID = '$userID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            if (mysql_num_rows($res) > 0) 
            {
                return true;
            }

            return false;
        }
    }
    
?>