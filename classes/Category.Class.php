<?php    
    include_once("classes/Bug.Class.php");

    /**
     * 
     */
    class Category
    {
        private $id;
        private $name;
        private $projectID;
        private $subCategorys;        
        private $bugs;                

        function __construct($id, $name, $projectID, $subCategorys = null, $bugs = null)
        {
            $this->id = (int) $id;
            $this->name = $name;
            $this->projectID = (int) $projectID;

            if($subCategorys == null) 
            {
                $this->subCategorys = new SplDoublyLinkedList();
            }
            else
            {
                $this->subCategorys = $subCategorys;
            }

            if ($bugs == null) 
            {
                $this->bugs = new SplDoublyLinkedList();
            }
            else
            {
                $this->bugs = $bugs;
            }
        }

        public function getID()
        {
            return $this->id;
        }

        public function getName() 
        {
            return $this->name;
        }

        public function getProjectID()
        {
            return $this->projectID;
        }

        public function getSubCategorys() 
        {
            return $this->subCategorys;
        }

        public function getBugs() 
        {
            return $this->bugs;
        }

        /**
         * Check if this Category has SubCategorys
         *
         * @return Returns true if SubCategorys exist else false
         **/
        public function hasSubCategorys() 
        {
            $subCategorys = $this->subCategorys;
            if ($this->subCategorys->count() > 0) 
            {
                return true;
            }

            return false;
        }

        /**
         * Get the Category and all SubCategorys with this ID
         *
         * @param int $id CategoryID
         * @return Category Category-Object
         **/
        public static function getCategoryByID($id)
        {
            $query = "SELECT name, projectID FROM category WHERE id = '$id'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            $row = mysql_fetch_assoc($res);

            $name = $row['name'];
            $projectID = $row['projectID'];
            $subCategory = new SplDoublyLinkedList();
            $bugs = Bug::getAllBugs($id);
            
            $query = "SELECT * FROM category WHERE parentID = '$id'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));
            
            while($row = mysql_fetch_assoc($res))
            {
                $subCategoryID = $row['id'];
                $subCategoryName = $row['name'];
                $subCategoryProjectID = $row['projectID'];
                $subCategorySubCategory = new SplDoublyLinkedList();
                $subCategoryBugs = Bug::getAllBugs($row['id']);

                $query = "SELECT * FROM category WHERE parentID = '" . $row['id'] . "'";
                $res2 = mysql_query($query)or die("SQL-Query Error: " . mysql_error() . "<br>Query: " . $query . "<br>Class-Method: " . __METHOD__ . "<br>Line: " . __LINE__);
                
                while($row2 = mysql_fetch_assoc($res2)) 
                {
                    $subCategory2ID = $row2['id'];
                    $subCategory2Name = $row2['name'];
                    $subCategory2ProjectID = $row2['projectID'];
                    $subCategory2SubCategory = null;
                    $subCategory2Bugs = Bug::getAllBugs($row2['id']);

                    $subCategorySubCategory->push(new Category($subCategory2ID, $subCategory2Name, $subCategory2ProjectID, $subCategory2SubCategory, $subCategory2Bugs));
                }

                $subCategory->push(new Category($subCategoryID, $subCategoryName, $subCategoryProjectID, $subCategorySubCategory, $subCategoryBugs));
            }

            return new Category($id, $name, $projectID, $subCategory, $bugs);
        }

        /**
         * Check if Project with this ID has ChildProjects                 
         *
         * @param int $Category CategoryID
         * @return bool
         **/
        public static function hasChildCategory($categoryID)
        {
            $query ="SELECT id FROM category WHERE parentID = '$projectID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            if (mysql_num_rows($res) > 0) 
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
        public static function getAllCategorysByProjectID($projectID)
        {
            $projects = new SplDoublyLinkedList();

            $query = "SELECT * FROM category WHERE parentID IS NULL AND projectID = '$projectID'";
            $res = mysql_query($query)or die(Helper::SQLErrorFormat(mysql_error(), $query, __METHOD__, __FILE__, __LINE__));

            while($row = mysql_fetch_assoc($res))
            {
                $projects->push(Category::getCategoryByID($row['id']));
            }

            return $projects;
        } 

        public static function getAllCategorysInJSONByProjectID($projectID) 
        {
            $re  = "";
            $categorys = Category::getAllCategorysByProjectID($projectID);

            $categorys->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
            for ($categorys->rewind(); $categorys->valid(); $categorys->next()) 
            {
                $id = $categorys->current()->getID();
                $name = $categorys->current()->getName();
                $subCategorys = $categorys->current()->getSubCategorys();
                $bugsCounter = $categorys->current()->getBugs()->count();
                $bugs = $categorys->current()->getBugs();
                $fixedBugs = 0;                

                $bugs->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                for ($bugs->rewind(); $bugs->valid(); $bugs->next()) 
                {
                    if( $bugs->current()->getStatus()->getID() == Status::FIXED ||    // Fixed
                        $bugs->current()->getStatus()->getID() == Status::CLOSED  ||  // Closed
                        $bugs->current()->getStatus()->getID() == Status::NO_BUG)     // No Bug 
                    {
                        $fixedBugs++;
                    }
                }

                $re .= "{";
                $re .= "text: '$name', ";
                $re .= "href: '/devControl/bugtracker/category/$id/show', ";
                $re .= "tags: ['$fixedBugs/$bugsCounter'], ";
                $re .= "nodes: [ ";                

                if ($subCategorys->count() > 0)
                {
                    for ($subCategorys->rewind(); $subCategorys->valid(); $subCategorys->next()) 
                    {
                        $subID = $subCategorys->current()->getID();
                        $subName = $subCategorys->current()->getName();
                        $subCategorysSub = $subCategorys->current()->getSubCategorys();
                        $subBugsCounter = $subCategorys->current()->getBugs()->count();
                        $subBugs = $subCategorys->current()->getBugs();
                        $subFixedBugs = 0;

                        $subBugs->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                        for ($subBugs->rewind(); $subBugs->valid(); $subBugs->next()) 
                        {
                            if( $subBugs->current()->getStatus()->getID() == Status::FIXED ||    // Fixed
                                $subBugs->current()->getStatus()->getID() == Status::CLOSED  ||  // Closed
                                $subBugs->current()->getStatus()->getID() == Status::NO_BUG)     // No Bug 
                            {
                                $subFixedBugs++;
                            }
                        }

                        $re .= "{";
                        $re .= "text: '$subName', ";
                        $re .= "href: '/devControl/bugtracker/category/$subID/show', ";
                        $re .= "tags: ['$subFixedBugs/$subBugsCounter'], ";
                        $re .= "nodes: [ ";

                        if ($subCategorysSub->count() > 0) 
                        {                            
                            for($subCategorysSub->rewind(); $subCategorysSub->valid(); $subCategorysSub->next())
                            {                                
                                $sub2ID = $subCategorysSub->current()->getID();
                                $sub2Name = $subCategorysSub->current()->getName();
                                $subCategory2Sub = null;
                                $subCategory2BugsCounter = $subCategorysSub->current()->getBugs()->count();

                                $re .= "{";
                                $re .= "text: '$subName', ";
                                $re .= "href: '/devControl/bugtracker/category/$subID/show', ";
                                $re .= "tags: ['$subCategory2BugsCounter'], ";
                                $re .= "nodes: [ ";
                                $re .= "]";
                                $re .= "},";
                            }               
                            //Letztes Zeichen entfernen
                            $re = substr($re, 0, -1);                
                        }

                        $re .= "]";
                        $re .= "},";
                    }
                    //Letztes Zeichen entfernen
                    $re = substr($re, 0, -1);  
                }

                $re .= "]";
                $re .= "},";
            }

            return substr($re, 0, -1);
        }       
    }
?>