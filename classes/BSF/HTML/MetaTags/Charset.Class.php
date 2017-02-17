<?php

/**
 * 
 */
class Charset
{
    private $name = "charset";
    private $content;
    
    function __construct($content = "ISO-8859-1")
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