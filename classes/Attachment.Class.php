<?php

    /**
     * 
     */
    class Attachment 
    {
        private $id;
        private $name;
        private $size;
        private $type;
              
        function __construct($id, $name, $size, $type)
        {
            $this->id = (int) $id;
            $this->name  = $name;
            $this->size = $size;
            $this->type = $type;
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

        public function setSize($size)
        {
            $this->size = $size;
        }

        public function getSize()
        {
            return $this->size;
        }

        public function getType()
        {
            return $this->type;
        }

        public function download() 
        {            
            $dir = "../devControl/attachments/";
            
            header("Content-Type: $this->type");
            
            header("Content-Disposition: attachment; filename=\"$this->name\"");
            
            readfile($dir.$this->id);            
        }

        public static function uploadFile($bugID, $file)        
        {
            $query = "INSERT INTO attachments (`bugID`, `name`, `type`, `size`) VALUES ('$bugID', '" . $file['name'] . "', '" . $file['type'] . "', '" . $file['size'] . "')";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            $fileID = mysql_insert_id();            

            History::addHistoryEntry($bugID, "User <a href='/user/" . unserialize($_SESSION['user'])->getID() . "/" . unserialize($_SESSION['user'])->getUsername() . "'>" . unserialize($_SESSION['user'])->getUsername() . "</a> uploaded the File " . $file['name'] . ".");

            move_uploaded_file($file['tmp_name'], "../devControl/attachments/" . $fileID);
        }

        public static function getAttachmentsByBugID($bugID)
        {
            $query = "SELECT * FROM attachments WHERE bugID = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $attachments = new SplDoublyLinkedList();

            while($row = mysql_fetch_assoc($res))
            {
                $id = $row['id'];
                $name = $row['name'];
                $size = $row['size'];
                $type = $row['type'];

                $attachments->push(new Attachment($id, $name, $size, $type));
            }

            return $attachments;
        }
        
        public static function getAttachmentByID($id)
        {
            $query = "SELECT `name`, `size`, `type` FROM attachments WHERE id = '$id'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            $row = mysql_fetch_assoc($res);

            $name = $row['name'];
            $size = $row['size'];
            $type = $row['type'];

            return new Attachment($id, $name, $size, $type);
        }    

        public static function deleteFile($fileID)
        {
            $file = Attachment::getAttachmentByID($fileID);

            $query = "DELETE FROM attachments WHERE id = '$fileID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));            

            History::addHistoryEntry($_GET['bugID'], "User <a href='/user/" . unserialize($_SESSION['user'])->getID() . "/" . unserialize($_SESSION['user'])->getUsername() . "'>" . unserialize($_SESSION['user'])->getUsername() . "</a> removed the File " . $file->getName() . ".");        

            unlink("../devControl/attachments/" . $fileID);
        }  

        public static function deleteAttachmentsByBugID($bugID)         
        {
            $query = "SELECT * FROM attachments WHERE bugID = '$bugID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            while ($row = mysql_fetch_assoc($res)) 
            {
                Attachment::deleteFile($row['id']);
            }
        }      
    }

?>