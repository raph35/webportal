<?php 
class Routeur extends Model{
	public $student;
	//lecture des utilisateur connecté a partir de mysql
    function __construct()
    {
		parent::__construct();
		$this->table="connected";
		try 
		{
			//$conn = new PDO("mysql:host=localhost;dbname=portailcaptif", 'admin', 'admin#portal');
			//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$query="SELECT * FROM connected";
			$stmt = $this->conn->query($query);
			$conteur=0;
			$this->student = array();
			while ($donnee= $stmt->fetch()){
				$this->student[$conteur]= new Etudiant($donnee['pseudo']);
				$this->student[$conteur]->mac=$donnee['mac'];
				$this->student[$conteur]->ip=$donnee['ip'];
				$this->student[$conteur]->heure=$donnee['heure'];
				$this->student[$conteur]->isConnected=$donnee['isConnected'];
				$conteur++;
			}
			

		}
		catch (Exception $e)
		{		
				echo "Connection failed: " . $e->getMessage();
		}
	}
	//affichage projet antérieur
    public function display()
    {    	$render="<table class='customTable'>
		  		<thead>
			  	<tr>
		  			<th style='border-width:1px;border-style:solid; width:20%;'>
		  				Pseudo
		  			</th>
		  			<th style='border-width:1px;border-style:solid; width:20%;'>
		  				Mac
		  			</th>
		  			<th style='border-width:1px;border-style:solid; width:20%;'>
		  				Ip
		  			</th>
		  			<th style='border-width:1px;border-style:solid; width:20%;'>
		  				Début de connection
		  			</th>
		  			<th style='border-width:1px;border-style:solid; width:20%;'>
		  				Option de suppression
		  			</th>
		  		</tr>
				  </thead>";
				  echo 	$render;
				if($this->student!=""){
				  for($i=0;$i<count($this->student);$i++)
				  {
				  	$this->student[$i]->display();
				  }
				}
			  echo "</table>";
			  
       
	}
	//verification si l'étudiant s'est déja connecté
	public function checkStudent($eleve)
	{
		$conteur=0;
		while ($conteur<count($this->student)){

			if($this->student[$conteur]->pseudo == $eleve->pseudo)
			{
				$query="UPDATE $this->table SET mac='$eleve->mac', ip='$eleve->ip',isConnected='1' WHERE pseudo='$eleve->pseudo'";
				$stmt = $this->conn->query($query);
				
				//prendre le temps restant de l'étudiant
				$data[0] = true;
				$data[1] =  $this->student[$conteur]->heure;
				return $data;
			}
			$conteur++;
		}
		$data[0] = false;
		$data[1]= TIMELIMIT;
		return $data;
	}


	//ajout d'utilisateur dans mySQl
    public function addStudent($eleve)
    {
	try {
			//$conn = new PDO("mysql:host=localhost;dbname=portailcaptif", 'admin', 'admin#portal');
		    //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
			$stmt = $this->conn->prepare("INSERT INTO connected (pseudo,mac,ip,heure,isConnected) VALUES (:pseudo,:mac,:ip,:heure,:isConnected)");
	 	  	$stmt->bindParam(':pseudo', $eleve->pseudo);
	 	  	$stmt->bindParam(':mac', $eleve->mac);
			$stmt->bindParam(':ip', $eleve->ip);	
			$stmt->bindParam(':heure', $time,PDO::PARAM_INT);
			$stmt->bindParam(':isConnected', $connect,PDO::PARAM_INT);   
			$time=TIMELIMIT;
			$connect=1;
	 	  	$stmt->execute();

	 	 }
    	
		catch (Exception $e)
		{
			    echo "Connection failed: " . $e->getMessage();
		}
		//save($eleve);	
	} 
	//suppression d'utilisateur
	public function delStudent($macaddr)
	{
		try {
			//$conn = new PDO("mysql:host=localhost;dbname=portailcaptif", 'admin', 'admin#portal');
		    //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $this->conn->prepare("DELETE FROM connected WHERE mac='$macaddr'");
	 	  	$stmt->execute();

	 	 }
    	
		catch (Exception $e)
		{
			    echo "Connection failed: " . $e->getMessage();
		}	
	}
}
?>