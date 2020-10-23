<?php
    class Controller
    {
        var $vars = array();
        var $layout = 'default';
        //passage d'une variable vers une vue
        function set($d)
        {
            $this->vars=array_merge($this->vars,$d);
        }
        //chargement des views
        function render($fileName)
        {
            extract($this->vars);
            ob_start();
            require(ROOT."views/".get_class($this)."/".$fileName.".php");
            $content_for_layout = ob_get_clean();
            if($this->layout==false)
            {
                echo $content_for_layout;
            }
            else
            {
                require(ROOT."views/layout/".$this->layout.".php");
            }
        }
    }
?>