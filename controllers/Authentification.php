<?php
    class Authentification extends Controller{
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
                $check=$routeur->checkStudent($etudiant);
         	    if(!$check[0]){
                    $routeur->addStudent($etudiant);
                    $request="
                <form action='$etudiant->url' method='post' id='sendUser' style='display:none'>
                    <input type='hidden' name='token' value='03246'>
                    <input type='text' name='pseudo' value='$etudiant->pseudo'>
                    <input type='text' name='mac' value='$etudiant->mac'>
                    <input type='text' name='ip' value='$etudiant->ip'>
                    <input type='text' name='heure' value='$etudiant->heure'>
                </form>
                ";
                echo $request;
                //$string_return = shell_exec("sudo /usr/local/lib/captiveportal/./addUser.sh " . $etudiant->mac);
                $confirm="<script>";
                $confirm.="alert('Bienvenue');sendUser.submit()";
                $confirm.="\n</script>";
                echo $confirm;
                }else{
                    if($check[1]=='connected'){
                        $error="Vous êtes déjà connecté sur un autre appareil";
                        $this->set(compact('error'));
                        $this->render('index');
                    }else{
                        $etudiant->heure=$check[1];
                        $request="
                        <form action='$etudiant->url' method='post' id='sendUser' style='display:none'>
                            <input type='hidden' name='token' value='03246'>
                            <input type='text' name='pseudo' value='$etudiant->pseudo'>
                            <input type='text' name='mac' value='$etudiant->mac'>
                            <input type='text' name='ip' value='$etudiant->ip'>
                            <input type='text' name='heure' value='$etudiant->heure'>
                        </form>
                        ";
                        echo $request;
                        //$string_return = shell_exec("sudo /usr/local/lib/captiveportal/./addUser.sh " . $etudiant->mac);
                        $confirm="<script>";
                        $confirm.="alert('Bienvenue');sendUser.submit()";
                        $confirm.="\n</script>";
                        echo $confirm;
                    }
                }
                // $request="\n<script> ";
                // $request.="\n function send(donne)";
                // $request.="\n{ ";
                // $request.="\n var xhr = new XMLHttpRequest();";
                // $request.="\n xhr.open('GET', 'http://".$this->nodeServer.":".$this->portNode."/index?pseudo='+donne.pseudo+'&mac='+donne.mac+'&ip='+donne.ip+'&heure='+donne.heure, true);";
                // $request.="\n xhr.send();"; 
                // $request.="\n } ;";
                // $request.="\n var data={ ";
                // $request.="\n 'pseudo':'".$etudiant->pseudo."',";
                // $request.="\n 'mac':'".$etudiant->mac."',";
                // $request.="\n 'ip':'".$etudiant->ip."',";
                // $request.="\n 'heure':'".$etudiant->heure."'";
                // $request.= "\n } ;";
                // $request.= "\n send(data);";
                // $request.="\n </script>";
                // echo $request;
                
                //header("Location:$etudiant->url");
            }
            if($this->result=="root")
            {
                //$routeur->addStudent($etudiant);
                //session_start();
                $request="
                <form action='http://".NODEIP.":".NODEPORT."/index' method='post' id='sendRoot' style='display:none'>
                    <input type='hidden' name='token' value='03246'>
                </form>
                ";
                echo $request;
                $confirm="<script>";
                $confirm.="alert('Vous vous êtes connecté en tant que root');sendRoot.submit()";
                $confirm.="\n</script>";
                echo $confirm;
				//header('Location:'.NODEIP.':'.NODEPORT.'/index');
            }
            if($this->result=="inscription")
            {
				session_start();
				$_SESSION['inscription']=true;
				header('Location:'.WEBROOT.'Inscription/index');
            }
            if($this->result === "refused"){
                $error = "Erreur, étudiant " . $_POST['pseudo'] . " non authentifié";
                $this->set(compact('error'));
                $this->render('index');
            }
        }
    }
?>