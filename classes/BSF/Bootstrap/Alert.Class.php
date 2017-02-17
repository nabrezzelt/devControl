<?php
    /**
     * 
     */
    class Alert extends HTMLElement
    {
        private $color;
        private $dismissible;
        
        function __construct($id, $name, $color, $dismissible = false)
        {
            $this->id = $id;
            $this->name = $name;
            $this->color = $color;
            $this->dismissible = $dismissible;
            
        }

        public function setColor($color)
        {
            $this->color = $color;
        }

        public function getColor()
        {
            return $this->color;
        }

        public function setDismissible($dismissible)
        {
            $this->dismissible = $dismissible;
        }

        public function getDismissible()
        {
            return $this->dismissible;
        }

        public function generate()
        {
            if($this->dismissible)
            {
                return "<div class=\"alert alert-success alert-dismissible " . $this->class . "\">
                            <a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
                            <strong>Success!</strong> Indicates a successful or positive action.
                        </div>";
            }
            else
            {
                # code...
            }
        }

    }
    


?>