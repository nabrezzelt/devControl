<?php
    /**
     * 
     */
    class History 
    {
        private $id;
        private $content;
        private $createTime;

        function __construct($id, $content, $createTime)
        {
            $this->id = (int) $id;
            $this->content = $content;
            $this->createTime = $createTime;
        }

        public function setID($id)
        {
            $this->id = $id;
        }

        public function getId()
        {
            return $this->id;
        }

        public function setContent($content)
        {
            $this->content = $content;
        }

        public function getContent()
        {
            return $this->content;
        }

        public function setCreateTime($createTime)
        {
            $this->createTime = $createTime;
        }

        public function getCreateTime()
        {
            return $this->createTime;
        }

        public static function getHistoryByID($id) 
        {
            throw new Exception("Not Implemented!", 1);            
        }

        public static function getHistoryByBugID($bugID) 
        {
            $query = "SELECT * FROM bug_history WHERE bugID = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $historyList = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $id = $row['id'];
                $content = $row['content'];
                $createTime = $row['createTime'];

                $historyList->push(new History($id, $content, $createTime));
            }
            
            return $historyList;
        }

        /**
         * Creates a HistoryEntry for an Bug
         *
         * @param int $bugID ID of an existing Bug
         * @param string $content Content of the HistoryEntry 
         **/
        public static function addHistoryEntry($bugID, $content) 
        {
            $query = "INSERT INTO bug_history (bugID, content, createTime) VALUES ('$bugID', '" . mysql_real_escape_string($content) ."', NOW())";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            return mysql_insert_id();
        }

        public static function deleteHistroyEntriesByBugID($bugID)
        {
            $query = "DELETE FROM bug_history WHERE bugID = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
        }
    }
    
?>