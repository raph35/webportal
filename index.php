<?php
    /* le server name tkn mretourn katrany am index.php saing ts nety de namboarna tanana
    define('ROOT',str_replace('index.php','',$_SERVER['SERVER_NAME']));
    ay n server_name mretourn n nom an server
    */

    /*n webroot mretournan le url
        de n root le chemin absolu an le fichier
    */
    define('WEBROOT',str_replace('index.php','',$_SERVER['SCRIPT_NAME']));

    define('ROOT',str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']));

    require(ROOT.'configuration.php');
    require(ROOT.'core/Model.php');
    require(ROOT.'core/Controller.php');
    require(ROOT.'apk/Etudiant.php');
    require(ROOT.'apk/Routeur.php');

    
    //lecture de l'url
    $params = explode('/',$_GET['p']);
    $controller=$params[0];
    //var_dump($controller);
    if($controller=="" || ($controller != "Admin" && $controller != "Acceuil" && $controller != "Authentification" && $controller != "Inscription"))
    //if($controller=="")
    {
        header('Location:'.WEBROOT.'Acceuil/index');
    }
    $action= isset($params[1]) ? $params[1] : 'index';

    require('controllers/'.$controller.'.php');
    $controller =new $controller();

    if(method_exists($controller,$action))
    {
        $controller->$action();
    }
    else
    {
        echo "<h1> 404 erreur => $action not found <h1>";
    }
?>