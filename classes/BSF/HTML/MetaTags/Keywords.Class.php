<?php

/**
 * 
 */
class Keywords
{
    private $name = "keywords";
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