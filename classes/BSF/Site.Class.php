<?php

/**
 * 
 */
class Site
{
    private $name; 
    private $themeURL;
    private $title;
    private $language;
    private $backgroundImage;
    private $backgroundColor;
    private $navigation;
    private $fullWitdhContent; 
    private $content;    
    private $footer;
    private $contentIndex = 0;
    private $footerIndex = 0;  

    private $tags = array('Author' => '', 'Charset' => '', 'Content-Language' => '', 'Description' => '', 'Keywords' =>  '', 'Robots' => '', 'Viewport' => 'width=device-width, initial-scale=1');

    function __construct($name, $themeURL, $title, $language, $backgroundColor = "white", $backgroundImage = null, $fullWitdhContent = false)
    {
        $this->name = $name;
        $this->themeURL = $themeURL;
        $this->title = $title;
        $this->language = $language;
        $this->tags['Content-Language'] = $language;
        $this->backgroundColor = $backgroundColor;
        $this->backgroundImage = $backgroundImage; 
        $this->fullWitdhContent = $fullWitdhContent;

        //Create new ContentList:
        $this->content = new SplDoublyLinkedList();        
    }

    public function setTag($tag)
    {
        $classname = $tag->getNameOfClass();             
        $this->tags[$classname] = $tag->getContent();
    }


    public function getName() 
    {
        return $this->name; 
    }

    public function addNavigation($navigation)
    {
        $this->navigation = $navigation;
    }

    public function addContent($content) 
    {   
        $this->content->push($content);        
    }

    public function getContent()
    {
        return $this->content;
    }

    public function generate() 
    {
        $re = "<html lang=\"$this->language\">
                    <head>
                        <title>$this->title</title>
                        <meta name=\"charset\" content=\"" . $this->tags['Charset'] . "\" />
                        <meta name=\"viewport\" content=\"" . $this->tags['Viewport'] . "\" />                        
                        <meta name=\"author\" content=\"" . $this->tags['Author'] . "\" />
                        <meta name=\"content-language\" content=\"" . $this->tags['Content-Language'] . "\" />
                        <meta name=\"description\" content=\"" . $this->tags['Description'] . "\" />
                        <meta name=\"robots\" content=\"" . $this->tags['Robots'] . "\">
                        <meta name=\"keywords\" content=\"" . $this->tags['Keywords'] . "\" />
                        <meta name=\"generator\" content=\"BSFramework\" />

                        <link rel=\"stylesheet\" href=\"" . $this->themeURL . "\" />

                        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>
                        <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js\"></script>
                    </head>
                    <body>" .
                        $this->navigation->generate()    
                 . "</body>
                </html>";

        $this->content->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
        for ($this->content->rewind(); $this->content->valid(); $this->content->next()) {
            echo var_dump($this->content->current());
        }

        return $re .= "Site created!";
    }
}




?>