<?php 
class Routeur extends Model{
	public $student;
    function __construct()
    {

			$this->table="connected";
    		try {
					$conn = new PDO("mysql:host=localhost;dbname=portailcaptif", 'admin', 'admin#portal');
				    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$query="SELECT * FROM connected";
					$stmt = $conn->query($query);
					$conteur=0;
					while ($donnee= $stmt->fetch()){
						$this->student[$conteur]= new Etudiant($donnee['pseudo']);
						$this->student[$conteur]->mac=$donnee['mac'];
						$this->student[$conteur]->ip=$donnee['ip'];
						$this->student[$conteur]->time=$donnee['heure'];
						$conteur++;
					}
		
				}
					catch (Exception $e)
				{		
		   			 echo "Connection failed: " . $e->getMessage();
				}
    }
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

    public function addStudent($eleve)
    {
	try {
			$conn = new PDO("mysql:host=localhost;dbname=portailcaptif", 'admin', 'admin#portal');
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
			$stmt = $conn->prepare("INSERT INTO connected (pseudo,mac,ip,heure) VALUES (:pseudo,:mac,:ip,CURRENT_TIMESTAMP)");
	 	  	$stmt->bindParam(':pseudo', $eleve->pseudo);
	 	  	$stmt->bindParam(':mac', $eleve->mac);
	 	  	$stmt->bindParam(':ip', $eleve->ip);
	 	  	$stmt->execute();

	 	 }
    	
		catch (Exception $e)
		{
			    echo "Connection failed: " . $e->getMessage();
		}
		//save($eleve);	
	} 
	public function delStudent($macaddr)
	{
		try {
			$conn = new PDO("mysql:host=localhost;dbname=portailcaptif", 'admin', 'admin#portal');
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("DELETE FROM connected WHERE mac='$macaddr'");
	 	  	$stmt->execute();

	 	 }
    	
		catch (Exception $e)
		{
			    echo "Connection failed: " . $e->getMessage();
		}	
	}
}
?>