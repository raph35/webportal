<?php
    class Authentification{
        //nodeJS
        
        public $nodeServer=NODEIP;
        public $portNode=NODEPORT;
        
        public $result;
        
        function index(){
            $routeur= new Routeur();
            if(!empty($_POST['pseudo']) && !empty($_POST['pass']))
            { 
                $etudiant=new Etudiant($_POST['pseudo'],$_POST['pass']);
                $this->result=$etudiant->authentification();
            }
            else
            {
                header('Location:'.WEBROOT.'Acceuil/index');
            }
            if($this->result=="accepted")
            {
                $routeur->addStudent($etudiant);
                $date = date('m/d/Y h:i:s a', time());
                $request="\n<script> ";
                $request.="\n function send(donne)";
                $request.="\n{ ";
                $request.="\n var xhr = new XMLHttpRequest();";
                $request.="\n xhr.open('GET', 'http://".$this->nodeServer.":".$this->portNode."/index?pseudo='+donne.pseudo+'&mac='+donne.mac+'&ip='+donne.ip+'&date='+donne.date, true);";
                $request.="\n xhr.send();"; 
                $request.="\n } ;";
                $request.="\n var data={ ";
                $request.="\n 'pseudo':'".$etudiant->pseudo."',";
                $request.="\n 'mac':'".$etudiant->mac."',";
                $request.="\n 'ip':'".$etudiant->ip."',";
                $request.="\n 'date':'".$date."'";
                $request.= "\n } ;";
                $request.= "\n send(data);";
                $request.="\n </script>";
                echo $request;
                $confirm="<script>";
                $confirm.="alert('Bienvenu');document.location.href='".$etudiant->url."';";
                $confirm.="\n</script>";
                echo $confirm;
                shell_exec("/home/raph35/Documents/Projets/findetudel3misa/gitHub/captivePortal/script_bash/./addUser.sh " . $etudiant->mac);
                //header("Location:$etudiant->url");
            }
            if($this->result=="root")
            {
                $routeur->addStudent($etudiant);
				session_start();
				$_SESSION['root']=true;
				header('Location:'.WEBROOT.'Admin/index');
            }
            if($this->result=="inscription")
            {
				session_start();
				$_SESSION['inscription']=true;
				header('Location:'.WEBROOT.'Inscription/index');
            }
        }
    }
?>