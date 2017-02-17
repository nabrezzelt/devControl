<?php

/**
 * 
 */
class Robots
{
    private $name = "robots";
    private $content;
    
    function __construct($content)
    {   
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getNameOfClass()
    {
        return static::class;
    }
}


?>