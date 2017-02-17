<?php
include_once("BSF/HTML/HTMLElement.Class.php");

/**
 * 
 */
class Panel extends HTMLElement
{
    private $heading;
    private $body;
    private $footer;  
    private $panelColor;  

    function __construct($heading, $body, $footer, $panelColor = Colors::Standard, $id = "", $class = "")
    {
        $this->heading = $heading;
        $this->body = $body;
        $this->footer = $footer;
        $this->panelColor = $panelColor;
        $this->id = $id;
        $this->class = $class;
    }    

    public function getHeading()
    {
        return $this->heading;
    }

    public function getBody()
    {
        return $this-body;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    public function getPanelColor() 
    {
        return $this->panelColor;
    }

    public function generate() {
        return "<div class=\"panel panel-" . "asd" . "\">
        
                </div>";
    }
}

?>