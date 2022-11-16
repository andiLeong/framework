<?php

namespace Andileong\Framework\Core\View;

class View
{
    private $content;

    public function render($path,$data = [])
    {
        extract($data);
        $path = str_replace('.','/',$path);
        require $_SERVER['DOCUMENT_ROOT'] .  "/assets/views/{$path}.php";
//        $this->content = file_get_contents($_SERVER['DOCUMENT_ROOT'] .  "/assets/views/{$path}.php");
        return $this;
    }

    public function getContent()
    {
       return $this->content;
    }

}