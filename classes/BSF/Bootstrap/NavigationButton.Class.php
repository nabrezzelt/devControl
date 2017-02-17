<?php

/**
 * 
 */
class NavigationButton
{
    private $name;
    private $link;
    private $align;
    private $active;
    private $subButtons;    
    
    function __construct($name, $link, $subButtons, $align = "left", $active = false)
    {
        $this->name = $name;
        $this->link = $link;
        $this->align = $align;        
        $this->active = $active;

        $this->subButtons = new SplDoublyLinkedList();
    }

    public function getName() 
    {
        return $this->name;
    }

    public function getLink() 
    {
        return $this->link;
    }

    public function getAlign()
    {
        return $this->align;
    }

    public function getActiveState()
    {
        return $this->active;
    }

    public function isActive() 
    {
        return $this->active;
    }

    public function getSubButtons() 
    {
        return $this->subButtons;
    }

    public function hasSubButtons()
    {
        if ($this->subButtons->count() < 1) {
            return false;
        }
        return true;
    }
}

?>