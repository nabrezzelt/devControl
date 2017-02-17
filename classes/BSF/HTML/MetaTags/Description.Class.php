<?php

/**
 * 
 */
class Description
{
    private $name = "description";
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