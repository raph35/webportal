<?php
  session_start();
  if(!isset($_SESSION['root']))
  {
      header('Location:'.WEBROOT.'Acceuil/index');
  }
class Admin extends Controller
{
    function index()
    {
      
        $routeur=new Routeur();
        $d['routeur']=$routeur;
        $this->set($d);
        $this->render('index');
    }
    function delete()
    {
        
        if(!empty($_POST['mac']))
        {
            $routeur=new Routeur(); 
            $routeur->delStudent($_POST['mac']);
            header('Location:'.WEBROOT.'Admin/index');
            
        }
    }
}
?>