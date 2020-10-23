<?php
class Etudiant
{
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
		
		$this->pseudo=$nom;
		$this->pass=$mdp;
		$this->ip=$_SERVER['REMOTE_ADDR'];
		$this->mac=getMac();

		$this->url="https://www.google.com";
		
		$this->serverLDAP="192.168.10.121";
		$this->passLDAP="123456";
		$this->dnLDAP="cn=admin,dc=authentification,dc=com";
	} 
	public function display()
	{
		$resultat="<form method='post' action='/Admin/delete'>
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

	public function authentification()
	{
		$value=trim(base64_encode(pack("H*",md5($this->pass))));
        $this->pass="{MD5}".$value;
        $ldap_dn = $this->dnLDAP;
        $ldap_password = $this->passLDAP;
        $ldap_con = ldap_connect($this->serverLDAP);

        ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
        if(ldap_bind($ldap_con, $ldap_dn, $ldap_password))
        {

            //echo "Bind successful!";
            $filter = "(cn=$this->pseudo)";
            $result = ldap_search($ldap_con, "dc=authentification,dc=com", $filter) or exit("Unable to search");
            $entries = ldap_get_entries($ldap_con, $result);
            
           
            if (!empty($entries[0]['cn'][0]) && $entries[0]['cn'][0]==$this->pseudo) {
                if (!empty( $entries[0]['userpassword'][0]) &&  $entries[0]['userpassword'][0]==$this->pass) {
                    //$mac=takeMac();
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
					header('Location:/Acceuil/index');
      
                }
            }
            else
            {
				header('Location:/Acceuil/index');
			}
        }
        else{
			echo "Invalid user/pass or other errors!";
			header('Location:/Acceuil/index');

        }
        return "";
	}
	public function inscription()
	{
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
			header('Location:'.WEBROOT.'Inscription/index');
		}
		else{
			header('Location:'.WEBROOT.'Inscription/index');
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