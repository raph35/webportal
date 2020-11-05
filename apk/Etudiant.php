<?php
class Etudiant
{
	//a propos de l'utilisateur
	public $pseudo;
	public $pass;
	public $mac;
	public $ip;
	public $time;
	public $url;
	public $serverLDPA;
	public $passLDAP;
	public $dnLDAP;
	public $date;

	function __construct($nom,$mdp=null, $ip=null, $mac=null)
	{
		//initialisation des données à partir ds pages webs
		$this->pseudo=$nom;
		$this->pass=$mdp;
		$this->ip=$_SERVER['REMOTE_ADDR'];
		$this->mac=getMac();
		$this->url="https://www.google.com";
		
		//LDAP server
		/*
		$this->serverLDAP="192.168.10.121";
		$this->passLDAP="123456";
		$this->dnLDAP="cn=admin,dc=authentification,dc=com";
		*/
		$this->serverLDAP=LDAPSERVER;
		$this->passLDAP=LDAPPASS;
		$this->dnLDAP=LDAPENTRI;
		
	} 

	//affichage des utilisateur connectés via php version anterieur du projet
	public function display()
	{
		$resultat="<form method='post' action='".WEBROOT."Admin/delete'>
					<tr>
		  			<td style='border-width:1px;border-style:solid; width:20%;'>
		  				$this->pseudo
		  			</td>
		  			<td style='border-width:1px;border-style:solid; width:20%;'>
		  				$this->mac
		  			</td>
		  			<td style='border-width:1px;border-style:solid; width:20%;'>
		  				$this->ip
		  			</td>
		  			<td style='border-width:1px;border-style:solid; width:20%;'>
		  				$this->time
		  			</td>
		  			<td style='border-width:1px;border-style:solid; width:20%;'>
		  				<input type='submit' value='Arreter'>
					  </td> 
					  </tr>
					  <input type='text' style='display:none' value='$this->mac' name='mac'>
				  </form>";
		 echo $resultat;
	}

	//authentification
	public function authentification()
	{
		//connection LDAP
		$value=trim(base64_encode(pack("H*",md5($this->pass))));
        $this->pass="{MD5}".$value;
        $ldap_dn = $this->dnLDAP;
        $ldap_password = $this->passLDAP;
        $ldap_con = ldap_connect($this->serverLDAP);

        ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
        if(ldap_bind($ldap_con, $ldap_dn, $ldap_password))
        {
            //recherche du pseudo dans l'annuaire
            $filter = "(cn=$this->pseudo)";
            $result = ldap_search($ldap_con, "dc=authentification,dc=com", $filter) or exit("Unable to search");
            $entries = ldap_get_entries($ldap_con, $result);
            
           //test si l'utilisateur a entré le bon mdp conform a son pseudo 
            if (!empty($entries[0]['cn'][0]) && $entries[0]['cn'][0]==$this->pseudo) {
                if (!empty( $entries[0]['userpassword'][0]) &&  $entries[0]['userpassword'][0]==$this->pass) {
					//$mac=takeMac();
					//test si l'utilisateur est root, inscription ou utilisateur normal

					if($this->pseudo=="root")
					{
						return "root";
					}
					if($this->pseudo=="inscription")
					{
						return "inscription";
					}
					

					return "accepted";       
                }
                else
                {
					return "refused";
					// header("Location:".WEBROOT."Acceuil/index");
      
                }
            }
            else
            {
				//header("Location:".WEBROOT."Acceuil/index");
				return "refused";
			}
        }
        else{
			// echo "Invalid user/pass or other errors!";
			// header("Location:".WEBROOT."Acceuil/index");
			return "refused";

        }
        return "";
	}

	//inscription de nouvel utilisateur dans LDAP 
	public function inscription()
	{
		//connection dans LDAP
		$ldap_dn = $this->dnLDAP;
		$ldap_password = $this->passLDAP;
		$server = $this->serverLDAP;
		$ldap_con = ldap_connect($server);
		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

		if ($ldap_con) {
		$r=ldap_bind($ldap_con,$ldap_dn,$ldap_password);

		// preparation des données
		//$entry['cn'] = $pseudo." ".$pseudo;
		$entry['cn'] = $this->pseudo;
    	$entry['sn'] = $this->pseudo;
   		$entry['objectclass'] = "person";
    	$entry['userPassword'] = '{MD5}' . base64_encode(pack('H*',md5($this->pass)));
		//test nom egaux
		
		$filter = "(cn=$this->pseudo)";
		$result = ldap_search($ldap_con, $ldap_dn, $filter);
		$research = ldap_get_entries($ldap_con, $result);
		if($research[0]['cn'][0] == ""){
			$r=ldap_add($ldap_con,"cn=".$this->pseudo.",cn=UNUM-group,ou=autentification_G,cn=admin,dc=authentification,dc=com", $entry);
			ldap_close($ldap_con);
			// header('Location:'.WEBROOT.'Inscription/index');
			return true;
		}
		else{
			return false;
			// header('Location:'.WEBROOT.'Inscription/index');
		}
		
		// Ajout des données dans l'annuaire
	
		} else {
		echo "Connexion au serveur LDAP impossible";

		}
	}
}
function getMac(){
	$mac = shell_exec('/usr/sbin/arp -na ' .$_SERVER['REMOTE_ADDR']);
	preg_match('/..:..:..:..:..:../', $mac, $matches);
	@$mac = $matches[0];
	return $mac;
}
function getUrl(){
	$url=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}
?>