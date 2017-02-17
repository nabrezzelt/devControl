 <?php
 
 
 public static function getProjectByID($id)
        {            
            $query = "SELECT * FROM projects WHERE parentID = '$id'";           
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $projectList = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res)) 
            {    
                $id = $row['id'];
                $name = $row['name'];
                $subProjects = null;
                $bugs = Bug::getAllBugs($row['id']);

                if (Project::hasChildProjects($row['id'])) 
                {                    
                    $subProjects = Project::getProjectByID($row['id']);
                }                

                $projectList->push(new Project($id, $name, $subProjects, $bugs));
            }

            return new Project($id, "General", $projectList, null);
        }

        /**
         * Check if Project with this ID has ChildProjects                 
         *
         * @param int $projectID ProjectID
         * @return bool
         **/
        public static function hasChildProjects($projectID)
        {
            $query ="SELECT id FROM projects WHERE parentID = '$projectID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            if (mysql_num_rows($res) > 0) 
            {
                return true;
            } 
            
            return false;            
        }

        ?>