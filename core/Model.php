<?php
    class Model
    {
        // public $apacheHost=APACHESERVER;
        // public $dbName=BDNAME;
        // $hostName=SQLUSER;
        // $passServer=SQLPASS;
        public $table;
        public $id;
        public $conn;
        
        public function __construct()
        {
            // echo "Constrcuteur appelle";
            // var_dump(APACHESERVER, BDNAME, SQLUSER, SQLPASS);
            // die();
            // mysql:host=localhost;dbname=portailcaptif", 'admin', 'admin#portal
            try {
                $this->conn = new PDO("mysql:host=" . APACHESERVER . ";dbname=" . BDNAME , SQLUSER, SQLPASS);
                // $this->conn = new \PDO("mysql:host=localhost;dbname=portailcaptif", 'admin', 'admin#portal');
                // var_dump($this->conn);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {		
                echo "Connection failed: " . $e->getMessage();
            }
        }
        static function load($name)
        {
            require "models/$name.php";
            return new $name;
        }

		/*
		Lit une ligne de la base de donnée  % à l'id
		fields colone spésifique ou champs
		*/

        public function read($fields=null)
        {
        	if ($fields==null) 
        	{
        		$fields= "*";
        		$sql = "SELECT $fields FROM ".$this->table." WHERE id=".$this->id;
                $req =$this->conn->query($sql);
                while ($data=$req->fetch())
                {
                    foreach ($data as $k => $v) {
                        $this->$k = $v;
                    }
                }
        		/*$data = mysql_fetch_assoc($req);
        		foreach ($data as $k => $v) {
        			$this->$k = $v;
        		}*/
        	}
        }

        /* sauvegarde dans la base de donnée*/
        public function save($data)
        {
        	if (isset($data['id']) && !empty($data['id'])) {
        		$sql="UPDATE ".$this->table." SET ";
                foreach ($data as $key => $value) {
                    if ($key!="id") {
                        $sql.= "$key = '$value',";
                    }
                }
                $sql=substr($sql, 0,-1);
                $sql.="WHERE id=".$data['id'];

        	}
            else
            {
                $sql="INSERT INTO ".$this->table." (";
                foreach ($data as $key => $value) {
                    $sql.="$key,";
                }
                $sql=substr($sql, 0,-1);
                $sql.=") VALUES (";
                foreach ($data as $key => $value) {
                    $sql.="$value,";
                }
                $sql=substr($sql, 0,-1);
                $sql.=")";
            }

            $this->conn->query($sql) ;
            if (!isset($data)) {
                //$this->id=mysql_insert_id();
            }
            else
            {
                $this->id=$data['id'];
            }
        }

        /* recherche dans la base de donnée*/ 
        public function find($data=array())
        {
            $condition="1=1"; //condition null
            $fields="*";
            $limit="";
            $order="id DESC"; //id décroissante
            extract($data);
            if(isset($data["conditions"])){ $condition = $data["condition"]; }
            if(isset($data["fields"])){ $fields = $data["fields"]; }
            if(isset($data["limit"])){ $limit ="LIMIT ".$data["limit"]; }
            if(isset($data["order"])){ $order = $data["order"]; }
            $sql="SELECT $fields FROM ".$this->table." WHERE $condition ORDER BY $order $limit";
            $req =$conn->query($sql) or die('Erreur : ' . $e->getMessage());
            $d=array();
            while ($data=$stmt->fetch())
            {
                $d[]=$data;
            }
            /*while($data = mysql_fetch_assoc($req))
            {
                $d[]=$data;
            }*/
            return $d;
        }

    }
