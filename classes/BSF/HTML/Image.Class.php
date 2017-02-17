<?php

/**
 * 
 */
class Image extends HTMLElement
{    
    private $src;
    private $alt;

    function __construct($id, $class, $src, $alt = "")
    {
        $this->id = $id;
        $this->class = $class;
        $this->src = $src;
        $this->alt = $alt;
    }
}


?>