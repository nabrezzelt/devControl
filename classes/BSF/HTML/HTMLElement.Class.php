<?php

/**
 * 
 */
class HTMLElement
{
    protected $class;
    protected $id;
    protected $tag;
    protected $text;   
    
    function __construct($tag, $text, $id = "", $class = "")
    {
        $this->tag = $tag;
        $this->text = $text;
        $this->id = $id;
        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getID()
    {
        return $this->id;
    }

    public function setClass($class) 
    {
        $this->class = $class;
    }

    public function setID($id) 
    {
        $this->id = $id;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
    }
}

?>