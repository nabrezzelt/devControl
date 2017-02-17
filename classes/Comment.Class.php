<?php
    /**
     * 
     */
    class Comment
    {
        private $id;
        private $bugID;
        private $content;
        private $user;
        private $createTime;

        function __construct($id, $bugID, $content, $user, $createTime)
        {
            $this->id = (int) $id;
            $this->bugID = $bugID;
            $this->content = $content;            
            $this->user = $user;
            $this->createTime = $createTime;
        }

        public function setID($id)
        {
            $this->id = $id;
        }

        public function getID()
        {
            return $this->id;
        }

        public function getBugID()
        {
            return $this->bugID;
        }

        public function setBugID($bugID) 
        {
            $this->bugID = $bugID;
        }

        public function setContent($content)
        {
            $this->content = $content;
        }

        public function getContent()
        {
            return $this->content;
        }

        public function setUser($user)
        {
            $this->user = $user;
        }

        public function getUser()
        {
            return $this->user;
        }

        public function setCreateTime($createTime)
        {
            $this->createTime = $createTime;
        }

        public function getCreateTime()
        {
            return $this->createTime;
        }    

        public static function getAllCommentsByBugID($bugID) 
        {
            $query = "SELECT * FROM comments WHERE bugID = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $comments = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $id = $row['id'];
                $bugID = $row['bugID'];
                $content = $row['content'];
                $user = User::getUser((int) $row['userID']);
                $createTime = $row['createTime'];

                $comments->push(new Comment($id, $bugID, $content, $user, $createTime));
            }
            
            return $comments;
        }

        public static function createComment($bugID, $content)
        {
            $user = unserialize($_SESSION['user']);

            $query = "INSERT INTO comments (`bugID`, `content`, `userID`, `createTime`) VALUES ('$bugID', '$content', '" . unserialize($_SESSION['user'])->getID() . "', NOW()) ";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            //create HistoryEntry:
            History::addHistoryEntry($bugID, "Comment from User <a href='/user/" . $user->getID() . "/" . $user->getUsername() . "'>" . $user->getUsername() . "</a> created.");

            return mysql_insert_id();
        }

        public static function deleteCommentsByBugID($bugID)
        {
            $query = "DELETE FROM comments WHERE bugID = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
        }

        public static function getCommentByID($commentID)
        {
            $query = "SELECT * FROM comments WHERE id = '$commentID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $row = mysql_fetch_assoc($res);

            $id = $row['id'];
            $bugID = $row['bugID'];
            $content = $row['content'];
            $user = User::getUser((int) $row['userID']);
            $createTime = $row['createTime'];

            return new Comment($id, $bugID, $content, $user, $createTime);
        }

        public static function change($commentID, $content)
        {
            $query = "UPDATE comments SET content = '" . mysql_real_escape_string($content) . "' WHERE id = '$commentID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
        }

        public static function delete($commentID)
        {
            $query = "DELETE FROM comments WHERE id = '$commentID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
        }
    }
    
?>