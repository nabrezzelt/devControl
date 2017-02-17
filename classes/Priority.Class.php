<?php
    /**
     * 
     */
    class Priority 
    {
        private $id;
        private $name;
        private $colorName;
        private $importantValue;

        function __construct($id, $name, $colorName, $importantValue)
        {
            $this->id = $id;
            $this->name = $name;
            $this->colorName = $colorName;
            $this->importantValue = $importantValue;
        }

        public function getID()
        {
            return $this->id;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getColorName()
        {
            return $this->colorName;
        }

        public function getImportantValue()
        {
            return $this->importantValue;
        }

        /**
         * undocumented function summary
         *
         * Undocumented function long description
         *
         * @param type var Description
         **/
        public static function getPriorityByID($id)
        {
            $query = "SELECT * FROM priority WHERE id = '$id'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            $row = mysql_fetch_assoc($res); 
            
            return new Priority($row['id'], $row['name'], $row['colorName'], $row['importantValue']);                        
        }

        public static function getAllPriorities()
        {
            $query = "SELECT * FROM priority ORDER BY importantValue";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            $priorities = new SplDoublyLinkedList();
            while($row = mysql_fetch_assoc($res))
            {
                $id = $row['id'];
                $name = $row['name'];
                $colorName = $row['colorName'];
                $importantValue = $row['importantValue'];

                $priorities->push(new Priority($id, $name, $colorName, $importantValue));
            }

            return $priorities;
        }
    }
    
?>