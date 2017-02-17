<?php
    require_once("includes/connect.inc.php");

    /**
     * 
     */
    class Change 
    {
        private $id;
        private $user;
        private $projectID;
        private $description;
        private $changeDate;
        private $bugs;

        function __construct($id, $user, $projectID, $description, $changeDate, $bugs)
        {
            $this->id = $id;
            $this->user = $user;
            $this->projectID = $projectID;
            $this->description = $description;
            $this->changeDate = $changeDate;

            if($bugs != null)
            {
                $this->bugs = $bugs;
            }
            else
            {
                $this->bugs = new SplDoublyLinkedList();
            }
        }

        public function setID($id)
        {
            $this->id = $id;
        }

        public function getID()
        {
            return $this->id;
        }

        public function setUser($user)
        {
            $this->user = $user;
        }

        public function getUser()
        {
            return $this->user;
        }

        public function setProjectID($projectID)
        {
            $this->projectID = $projectID;
        }

        public function getProjectID()
        {
            return $this->projectID;
        }

        public function setDescription($description)
        {
            $this->description = $description;
        }

        public function getDescription()
        {
            return $this->description;
        }

        public function setChangeDate($changeDate)
        {
            $this->changeDate = $changeDate;
        }

        public function getChangeDate()
        {
            return $this->changeDate;
        }

        public function setBugs($bugs)
        {
            $this->bugs = $bugs;
        }

        public function getBugs()
        {
            return $this->bugs;
        }

        /**
         * undocumented function summary
         *
         * Undocumented function long description
         *
         * @param type var Description
         **/
        public static function getChangeByID($changeID)
        {
            $query = "SELECT * FROM changelog WHERE id = '$changeID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $row = mysql_fetch_assoc($res);

            $id = $row['id'];
            $user = User::getUser((int) $row['userID']);
            $projectID = $row['projectID'];
            $description = $row['description'];
            $changeDate = $row['changeDate'];

            $bugs = Bug::getAllBugsByChangeID($id);   

            return new Change($id, $user, $projectID, $description, $changeDate, $bugs);            
        }

        /**
         * undocumented function summary
         *
         * Undocumented function long description
         *
         * @param type var Description
         **/
        public function getAllChangesByProjectID($projectID)
        {
            $query = "SELECT * FROM changelog WHERE projectID = '$projectID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $changes = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $id = $row['id'];
                $user = User::getUser((int) $row['userID']);
                $projectID = $row['projectID'];
                $description = $row['description'];
                $changeDate = $row['changeDate'];

                $bugs = Bug::getAllBugsByChangeID($id);   

                $changes->push(new Change($id, $user, $projectID, $description, $changeDate, $bugs));              
            }

            return $changes;
        }

        /**
         * undocumented function summary
         *
         * Undocumented function long description
         *
         * @param type var Description
         **/
        public static function create($projectID, $description, $bugs)
        {
            $query = "INSERT INTO changelog (userID, projectID, description, changeDate) VALUES ('" . unserialize($_SESSION['user'])->getID() . "', '" . $projectID . "', '" . mysql_real_escape_string($description) . "', NOW())";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            $changeID = mysql_insert_id();

            foreach($bugs as $key => $value)
            {
                $query = "INSERT INTO changelog_bug_relation (changeID, bugID) VALUES ('$changeID', '$value')";
                $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

                $bug = Bug::getBugByID((int) $value);

                Bug::change($bug->getID(), $bug->getPriority()->getID(), $bug->getTitle(), $bug->getDescription(), $bug->getAssignedToUser()->getID(), 100, Status::FIXED, true);
            }
            return $changeID;
        }

        /**
         * undocumented function summary
         *
         * Undocumented function long description
         *
         * @param type var Description
         **/
        public static function change($changeID, $description, $bugs)
        {
            $query = "UPDATE changelog SET description = '$description' WHERE id = '$changeID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            foreach($bugs as $key => $value)
            {
                $query = "INSERT INTO changelog_bug_relation (changeID, bugID) VALUES ('$changeID', '$value')";
                $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

                $bug = Bug::getBugByID((int) $value);

                Bug::change($bug->getID(), $bug->getPriority()->getID(), $bug->getTitle(), $bug->getDescription(), $bug->getAssignedToUser()->getID(), 100, Status::FIXED, true);
            }
        }        

        /**
         * Delete The Change and the Linked Bugs
         *
         * @param int $changeID ID of the Change
         * 
         * @return void
         **/
        public static function delete($changeID) 
        {
            $query = "DELETE FROM changelog_bug_relation WHERE changeID = '$changeID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            $query = "DELETE FROM changelog WHERE id = '$changeID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));            
        }

        /**
         * Removes the Linked Bugs from the given Change.
         *
         * @param int $changeID ID of the Change
         *
         * @return void
         **/
        public static function removeLinkedBugs($changeID)
        {
             $query = "DELETE FROM changelog_bug_relation WHERE changeID = '$changeID'";
             $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));            
        }

        public static function existsForBug($bugID)
        {
            $query = "SELECT * FROM changelog_bug_relation WHERE bugID = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));            
            
            if(mysql_num_rows($res) == 0)
            {
                return false;
            }

            return true;
        }
    }
    

?>