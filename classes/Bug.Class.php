<?php
    /**
     * 
     */
    class Bug 
    {
        private $id;
        private $categoryID;
        private $priority;
        private $title;
        private $description;
        private $user;
        private $assignedToUser;
        private $progress;
        private $status;
        private $createTime;
         
        private $comments;       
        private $history;      
        private $attachments;  
        
        function __construct($id, $categoryID, $priority, $title, $description, $user, $assignedToUser, $progress, $status, $createTime, $comments, $history, $attachments)
        {
            $this->id = (int) $id;            
            $this->categoryID = (int) $categoryID;
            $this->priority = $priority;
            $this->title = $title;
            $this->description = $description;
            $this->user = $user;
            $this->assignedToUser = $assignedToUser;
            $this->progress = (int) $progress;
            $this->status = $status;
            $this->createTime = $createTime;

            if ($comments == null) 
            {
                $comments = new SplDoublyLinkedList(); 
            }
            else 
            {
                $this->comments = $comments; 
            }

            if ($history == null) 
            {
                $history = new SplDoublyLinkedList(); 
            }
            else 
            {
                $this->history = $history; 
            }

            if ($attachments == null) 
            {
                $attachments = new SplDoublyLinkedList(); 
            }
            else 
            {
                $this->attachments = $attachments; 
            }
        }

        public function getID() 
        {
            return $this->id;
        }

        public function getCategoryID()
        {
            return $this->categoryID;
        }

        public function getPriority() 
        {
            return $this->priority;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function getDescription()
        {
            return $this->description;
        }

        public function getUser() 
        {
            return $this->user;
        }

        public function getAssignedToUser()
        {
            return $this->assignedToUser;
        }

        public function getProgress()
        {
            return $this->progress;
        }

        public function getStatus()
        {
            return $this->status;
        }

        public function getCreateTime()
        {
            return $this->createTime;
        }

        public function getComments()
        {
            return $this->comments;
        }

        public function getHistory()
        {
            return $this->history;
        }

        public function getAttachments()
        {
            return $this->attachments;
        }

        /**
         * Get all Bugs where in this categoryID
         *
         * @param int $categoryID CategoryID
         * @return List<Bug> Returns a List of Bugs
         **/
        public static function getAllBugs($categoryID)
        {
            $query = "SELECT * FROM bugs WHERE categoryID = '$categoryID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $bugList = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $id = $row['id'];    
                $priority = Priority::getPriorityByID($row['priority']);            
                $title = $row['title'];
                $description = $row['description'];
                $user = User::getUser((int) $row['userID']);
                $assignedToUser = User::getUser((int) $row['assignedToID']);
                $progress = $row['progress'];
                $status = Status::getStatusByID($row['statusID']);
                $createTime = $row['createTime']; 
                $comments = Comment::getAllCommentsByBugID($id);
                $history = History::getHistoryByBugID($id);
                $attachments = Attachment::getAttachmentsByBugID($id);

                $bugList->push(new Bug($id, $categoryID, $priority, $title, $description, $user, $assignedToUser, $progress, $status, $createTime, $comments, $history, $attachments));
            }

            return $bugList;
        }

        public static function getBugByID($bugID)
        {
            $query = "SELECT * FROM bugs WHERE id = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            $row = mysql_fetch_assoc($res);

            $categoryID = $row['categoryID'];    
            $priority = Priority::getPriorityByID($row['priority']);            
            $title = $row['title'];
            $description = $row['description'];
            $user = User::getUser((int) $row['userID']);
            $assignedToUser = User::getUser((int) $row['assignedToID']);
            $progress = $row['progress'];
            $status = Status::getStatusByID($row['statusID']);
            $createTime = $row['createTime']; 
            $comments = Comment::getAllCommentsByBugID($bugID);
            $history = History::getHistoryByBugID($bugID);
            $attachments = Attachment::getAttachmentsByBugID($bugID);

            return new Bug($bugID, $categoryID, $priority, $title, $description, $user, $assignedToUser, $progress, $status, $createTime, $comments, $history, $attachments);
        }

        public static function getAllBugsByChangeID($changeID)
        {
            $query = "SELECT changelog_bug_relation.changeID, bugs.id AS bugID, bugs.categoryID, bugs.priority, bugs.title, bugs.description, bugs.userID, bugs.assignedToID, bugs.progress, bugs.statusID, bugs.createTime FROM changelog_bug_relation
                      JOIN bugs
                      ON changelog_bug_relation.bugID = bugs.id
                      WHERE changeID = '$changeID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            $bugs = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $bugID = $row['bugID'];
                $categoryID = $row['categoryID'];    
                $priority = Priority::getPriorityByID($row['priority']);            
                $title = $row['title'];
                $description = $row['description'];
                $user = User::getUser((int) $row['userID']);
                $assignedToUser = User::getUser((int) $row['assignedToID']);
                $progress = $row['progress'];
                $status = Status::getStatusByID($row['statusID']);
                $createTime = $row['createTime']; 
                $comments = Comment::getAllCommentsByBugID($bugID);
                $history = History::getHistoryByBugID($bugID);
                $attachments = Attachment::getAttachmentsByBugID($bugID);

                $bugs->push(new Bug($bugID, $categoryID, $priority, $title, $description, $user, $assignedToUser, $progress, $status, $createTime, $comments, $history, $attachments));
            }

            return $bugs;
        }

        public static function getUnfixedBugsByProjectID($projectID)
        {
            $query = "SELECT bugs.* 
                      FROM bugs
                      JOIN category
                      ON category.id = bugs.categoryID
                      WHERE bugs.statusID != '" . Status::FIXED . "' AND category.projectID = '$projectID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            $bugs = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $bugID = $row['id'];
                $categoryID = $row['categoryID'];    
                $priority = Priority::getPriorityByID($row['priority']);            
                $title = $row['title'];
                $description = $row['description'];
                $user = User::getUser((int) $row['userID']);
                $assignedToUser = User::getUser((int) $row['assignedToID']);
                $progress = $row['progress'];
                $status = Status::getStatusByID($row['statusID']);
                $createTime = $row['createTime']; 
                $comments = Comment::getAllCommentsByBugID($bugID);
                $history = History::getHistoryByBugID($bugID);
                $attachments = Attachment::getAttachmentsByBugID($bugID);

                $bugs->push(new Bug($bugID, $categoryID, $priority, $title, $description, $user, $assignedToUser, $progress, $status, $createTime, $comments, $history, $attachments));
            }

            return $bugs;
        }

        public static function getBugsByProjectID($projectID)
        {
            $query = "SELECT bugs.* FROM bugs
                      JOIN category
                      ON category.id = bugs.categoryID
                      WHERE category.projectID = '$projectID'";
             $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            $bugs = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $bugID = $row['id'];
                $categoryID = $row['categoryID'];    
                $priority = Priority::getPriorityByID($row['priority']);            
                $title = $row['title'];
                $description = $row['description'];
                $user = User::getUser((int) $row['userID']);
                $assignedToUser = User::getUser((int) $row['assignedToID']);
                $progress = $row['progress'];
                $status = Status::getStatusByID($row['statusID']);
                $createTime = $row['createTime']; 
                $comments = Comment::getAllCommentsByBugID($bugID);
                $history = History::getHistoryByBugID($bugID);
                $attachments = Attachment::getAttachmentsByBugID($bugID);

                $bugs->push(new Bug($bugID, $categoryID, $priority, $title, $description, $user, $assignedToUser, $progress, $status, $createTime, $comments, $history, $attachments));
            }

            return $bugs;                      
        }

        public static function create($categoryID, $priorityID, $title, $description, $userID, $assignedToUserID, $progress, $statusID)
        {
            $query = "INSERT INTO bugs (`categoryID`, `priority`, `title`, `description`, `userID`, `assignedToID`, `progress`, `statusID`, `createTime`) VALUES
            ('" . mysql_real_escape_string($categoryID) . "',
             '" . mysql_real_escape_string($priorityID) . "',
             '" . mysql_real_escape_string($title) . "',
             '" . mysql_real_escape_string($description) . "',
             '" . mysql_real_escape_string($userID) . "',
             '" . mysql_real_escape_string($assignedToUserID) . "',
             '" . mysql_real_escape_string($progress) . "',
             '" . mysql_real_escape_string($statusID) . "',
             NOW())";
             $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
             
             $bugID = mysql_insert_id();
             History::addHistoryEntry($bugID, "Bug created.");

             return $bugID;
        }

        public static function change($bugID, $priorityID, $title, $description, $assignedToUserID, $progress, $statusID, $changeCreated = false)
        {
            $content = "";
            $bug = Bug::getBugByID($bugID);
            $query = "UPDATE bugs SET 
                      `priority` = '" . mysql_real_escape_string($priorityID) . "',
                      `title` = '" . mysql_real_escape_string($title) . "',
                      `description` = '" . mysql_real_escape_string($description) . "',
                      `assignedToID` = '" . mysql_real_escape_string($assignedToUserID) . "',
                      `progress` = '" . mysql_real_escape_string($progress) . "',
                      `statusID` = '" . mysql_real_escape_string($statusID) . "'
                    WHERE id = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            if($priorityID != $bug->getPriority()->getID())
            {                
                $priority = Priority::getPriorityByID($priorityID);
                $content .= "- changed Priority from <span class='text-" . $bug->getPriority()->getColorName() . "'>" . $bug->getPriority()->getName() . "</span> to <span class='text-" . $priority->getColorName() . "'>" . $priority->getName() . "</span><br>";
            }

            if($title != $bug->getTitle())
            {
                $content .= "- changed Title to $title.<br>";
            }

            if($description != $bug->getDescription())
            {
                $content .= "- changed Description.<br>";
            }

            if($assignedToUserID != $bug->getAssignedToUser()->getID())
            {                
                $user = User::getUser((int) $assignedToUserID);
                $content .= "- changed the Assigned-User from <a href='/devControl/user/" . $bug->getAssignedToUser()->getID() . "/" . $bug->getAssignedToUser()->getUsername() . "'>" . $bug->getAssignedToUser()->getUsername() . "</a> to <a href='/devControl/user/" . $user->getID() . "/" . $user->getUsername() . "'>" . $user->getUsername() . "</a>.<br>";
                //ToDo: Generate Message to the user x that the AssignedUser changed to x.
            }

            if($progress != $bug->getProgress())
            {
                $content .= "- changed the Progress from " . $bug->getProgress() . " to " . $progress . ". <br>";
            }

            if($statusID != $bug->getStatus()->getID())
            {
                $status = Status::getStatusByID($statusID);
                $content .= "- changed the status from " . $bug->getStatus()->getName() . " to " . $status->getName() . ".";
            }            

            if($changeCreated)
            {
                $content .= "Change created.";
            }

            if($content != "")
            {
                $historyContent = "<small>User <a href='/user/" . unserialize($_SESSION['user'])->getID() . "/" . unserialize($_SESSION['user'])->getUsername() . "'>" . unserialize($_SESSION['user'])->getUsername() . "</a></small><br>";
                $historyContent .= $content;
                History::addHistoryEntry($bugID, $historyContent);                            
            }                      
        }

        public static function getAllBugsAssignedTo($userID)
        {
            $query = "SELECT * FROM bugs WHERE assignedToID = '$userID' ORDER BY createTime DESC";            
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $bugs = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $bugID = $row['id'];
                $categoryID = $row['categoryID'];    
                $priority = Priority::getPriorityByID($row['priority']);            
                $title = $row['title'];
                $description = $row['description'];
                $user = User::getUser((int) $row['userID']);
                $assignedToUser = User::getUser((int) $row['assignedToID']);
                $progress = $row['progress'];
                $status = Status::getStatusByID($row['statusID']);
                $createTime = $row['createTime']; 
                $comments = Comment::getAllCommentsByBugID($bugID);
                $history = History::getHistoryByBugID($bugID);
                $attachments = Attachment::getAttachmentsByBugID($bugID);

                $bugs->push(new Bug($bugID, $categoryID, $priority, $title, $description, $user, $assignedToUser, $progress, $status, $createTime, $comments, $history, $attachments));
            }

            return $bugs; 
        }

        public static function getAllCreatedBugsByUserID($userID)
        {
            $query = "SELECT * FROM bugs WHERE userID = '$userID' ORDER BY createTime DESC";            
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $bugs = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $bugID = $row['id'];
                $categoryID = $row['categoryID'];    
                $priority = Priority::getPriorityByID($row['priority']);            
                $title = $row['title'];
                $description = $row['description'];
                $user = User::getUser((int) $row['userID']);
                $assignedToUser = User::getUser((int) $row['assignedToID']);
                $progress = $row['progress'];
                $status = Status::getStatusByID($row['statusID']);
                $createTime = $row['createTime']; 
                $comments = Comment::getAllCommentsByBugID($bugID);
                $history = History::getHistoryByBugID($bugID);
                $attachments = Attachment::getAttachmentsByBugID($bugID);

                $bugs->push(new Bug($bugID, $categoryID, $priority, $title, $description, $user, $assignedToUser, $progress, $status, $createTime, $comments, $history, $attachments));
            }

            return $bugs; 
        }

        public static function delete($bugID)
        {                        
            Attachment::deleteAttachmentsByBugID($bugID);
            Comment::deleteCommentsByBugID($bugID);
            History::deleteHistroyEntriesByBugID($bugID);

            $query = "DELETE FROM bugs WHERE id = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));             
        }        
    }
    
?>