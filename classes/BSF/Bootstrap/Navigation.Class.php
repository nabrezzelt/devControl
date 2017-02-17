<?php
include_once("BSF/HTML/HTMLElement.Class.php");

/**
 * 
 */
class Navigation extends HTMLElement
{
    private $brand;
    private $brandLink;
    private $navigationStyle;
    private $navButtons;
    
    function __construct($brand = "", $brandLink = "#", $navigationStyle = NavigationStyles::Standard)
    {
        $this->brand = $brand;        
        $this->brandLink = $brandLink;
        $this->navigationStyle = $navigationStyle;
        $this->id = "";
        $this->class = "";

        $this->navButtons = new SplDoublyLinkedList();
    }

    public function addButton(NavigationButton $button) 
    {
        $this->navButtons->push($button);
    }

    public function generate() 
    {
        $re  = "<nav id=\"" . $this->id . "\" class=\"" . $this->class . " " . $this->navigationStyle . "\">
                    <div class=\"container-fluid\">
                        <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#main_navbar\" aria-expanded=\"false\">
                            <span class=\"sr-only\">Navigation ein-/ausblenden</span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                        </button>";
                        if ($this->brand != "") {
                            $re .= "<a class=\"navbar-brand\" href=\"" . $this->brandLink . "\">" . $this->brand . "</a>";    
                        }
                        
                        
                    $re .= "<div class=\"collapse navbar-collapse\" id=\"main_navbar\">
                                <ul class=\"nav navbar-nav\">";

                                $this->navButtons->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                                for ($this->navButtons->rewind(); $this->navButtons->valid(); $this->navButtons->next()) {
                                    $navButton = $this->navButtons->current();
                                    
                                    if ($navButton->getAlign() != "right")
                                    {
                                        if ($navButton->hasSubButtons()) {
                                            generateDropDownSubButton($navButton);
                                        }
                                        else
                                        {
                                            generateSubButton($navButton);
                                        }
                                        
                                    }
                                }

                        $re .= "</ul>
                                <ul class=\"nav navbar-nav navbar-right\">

                                </ul>
                            </div>";
                    







                        

                        $this->navButtons->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
                        for ($this->navButtons->rewind(); $this->navButtons->valid(); $this->navButtons->next()) {
                            $navButton = $this->navButtons->current();
                            
                            if ($navButton->getAlign() == "right")
                            {
                                if ($navButton->hasSubButtons()) {
                                    generateDropDownSubButton($navButton);
                                }
                                else
                                {
                                    generateSubButton($navButton);
                                }
                            }
                        }

        $re .= "    </div>    
                </nav>";

        return $re;
    }

    private function generateDropDownSubButton($button) 
    {
        if ($button->isActive()) {
            return "<li>
                        <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">Menü <span class=\"caret\"></span></a>    
                        
                    </li>";
        }
    }

    private function generateSubButton($button) 
    {
        if ($button->isActive()) 
        {
           return "<li class=\"active\"><a href=\"" . $button->getLink() . "\">" . $button->getName() . "</a></li>";
        }
         else 
        {
            return "<li class=\"active\"><a href=\"" . $button->getLink() . "\">" . $button->getName() . "</a></li>";
        }
    }
}


?>