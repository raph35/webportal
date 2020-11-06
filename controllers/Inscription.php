<?php
 session_start();
 if(!isset($_SESSION['inscription']))
 {
     header('Location:'.WEBROOT.'Acceuil/index');
 }
class Inscription extends Controller
{
    function index()
    {
        $this->render('index');
    }
    function inscrire()
    {
        if(!empty($_POST['pseudo']) && !empty($_POST['mdp']))
        {
            $etudiant=new Etudiant($_POST['pseudo'],$_POST['mdp']);
            $retour = $etudiant->inscription();
            if($retour){
                $succes =  "Succès, étudiant ". $_POST['pseudo'] . " ajouté";
                $this->set(compact('succes'));
            } else {
                $error = "Erreur, identifiant " . $_POST['pseudo'] . " existant";
                $this->set(compact('error'));
            }
            $this->render('index');
        }
    }
}
?>