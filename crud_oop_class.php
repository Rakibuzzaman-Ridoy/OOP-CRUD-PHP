
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>CRUP OOP</title>
  </head>
  <body>
    <h1 class="text-center text-warning bg-dark col-sm-8 offset-2">CRUD Operation using OOP Concept!</h1>
    <hr/>
    <?php 
		class database
		{
			private $db_servername = "localhost";
			private $db_username   = "root";
			private $db_password   = "";
			private $db_name       = "university";

			private $connection    = false;
			private $conn          = "";
			private $result        = array();

			//Function to connect the database!
			public function __construct()
			{
				if(!$this->connection)
				{
					$this->conn = new mysqli($this->db_servername,$this->db_username,$this->db_password,$this->db_name);
					$this->connection = true;

					if($this->conn->connect_error)
					{
						array_push($this->result,$this->conn->connect_error);
						return false;
					}
				}
				else
				{
					return true;
				}
			}

			//Function to insert data into the database!
			public function insert($table,$params=array())
			{
				if($this->tableExists($table))
				{
					$insert_keys = implode(", ", array_keys($params));
					$insert_values = implode("', '", $params);

					$sql = "INSERT INTO $table ($insert_keys) VALUES ('$insert_values')";
					//print_r($sql);
					$query = $this->conn->query($sql);
					if($this->conn->query($sql))
					{
						array_push($this->result,$this->conn->insert_id);
						return true;
					}
					else
					{
						array_push($this->result,$this->conn->error);
						return false;
					}
				}
				else
				{
					return false;
				}
			}

			//Function to update data!
			public function update($table,$params=array(),$where=null)
			{
				if($this->tableExists($table))
				{
					$args = array();
					foreach($params as $key=>$value)
					{
						$args[] = "$key = '$value'";
					}

					$sql = "UPDATE $table SET ". implode(", ", $args);
					if($where !=null)
					{
						$sql .= " WHERE $where";
					}
					if($this->conn->query($sql))
					{
						array_push($this->result,$this->conn->affected_rows);
						return true;
					}
					else
					{
						array_push($this->result,$this->conn->error);
						return false;
					}		
				}
				else
				{
					return false;
				}
			}

			//Function to delete data!
			public function delete($table,$where=null)
			{
				if($this->tableExists($table))
				{
					$sql = "DELETE FROM $table ";
					if($where !=null)
					{
						$sql .= " WHERE $where";
					}
					
					if($this->conn->query($sql))
					{
						array_push($this->result,$this->conn->affected_rows);
						return true;
					}
					else
					{
						array_push($this->result,$this->conn->error);
						return false;
					}
				}
				else
				{
					return false;
				}
			}

			//Function to select data by RAW query!
			public function selectQuery($sql)
			{
				$query = $this->conn->query($sql);
				if($query)
				{
					if($query->num_rows>1)
					{
						$this->result = $query->fetch_all(MYSQLI_ASSOC);
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}

			//Function to read data! (SELECT)
			public function select($table,$row="*",$join=null,$where=null,$order=null,$limit=null)
			{
				if($this->tableExists($table))
				{
					$sql = "SELECT $row FROM $table ";
					if($join !=null)
					{
						$sql .= " JOIN $join";
					}
					if($where !=null)
					{
						$sql .= " WHERE $where";
					}
					if($order !=null)
					{
						$sql .= " ORDER BY $order";
					}
					if($limit !=null)
					{
						if(isset($_GET['page']))
						{
							$page = $_GET['page'];
						}
						else
						{
							$page = 1;
						}
						$offset = ($page-1)*$limit;
						$sql .= " LIMIT $offset,$limit";
					}

						$query = $this->conn->query($sql);
						if($query)
						{
							if($query->num_rows>1)
							{
								$this->result = $query->fetch_all(MYSQLI_ASSOC);
								return true;
							}
							else
							{
								return false;
							}
						}
						else
						{
							return false;
						}

				}
				else
				{
					return false;
				}
			}

			//Pagination!
			public function pagination($table,$join=null,$where=null,$limit=null)
			{
				if($this->tableExists($table))
				{
					if($limit !=null)
					{
						$sql = "SELECT count(*) from student";
						if($join !=null)
						{
							$sql .= " JOIN $join";
						}
						if($where !=null)
						{
							$sql .= " WHERE $where";
						}

						$query = $this->conn->query($sql);
						$total_records = $query->fetch_array();
						// print_r($total_records);
						$total_records = $total_records[0];
						$total_pages   = ceil($total_records/$limit);
						$url = $_SERVER['PHP_SELF'];

						if(isset($_GET['page']))
						{
							$page = $_GET['page'];
						}
						else
						{
							$page = 1;
						}

						$output  = "<ul class='pagination'>";
						if($page>1)
						{
							$output .="<li><a class='btn-outline-warning text-center bg-dark'href='$url?page=".($page-1)."'>Prev</a></li>";
						}
						if($total_records>$limit)
						{
							for($i=1; $i<=$total_pages; $i++)
							{
								if($page==$i)
								{
									$cls = "clss = 'css'";
								}
								else
								{
									$cls = "";
								}
								$output .="<li><a $cls class='col-sm-2 offset-2 text-center bg-dark btn-outline-success' href='$url?page=$i'>Page:$i<br></a></li>";
							}
						}
						if($page<$total_pages)
						{
							$output .="<li><a class='btn-outline-warning text-center bg-dark offset-4' href='$url?page=".($page+1)."'>Next</a></li>";
						}

						return $output .="</ul>";


					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}


			//Show Result!
			public function getResults()
			{
				$variable = $this->result;
				$this->result = array();
				return $variable;


			}

			//Function to check the existence of table!
			private function tableExists($table)
			{
				$sql = "SHOW TABLES FROM $this->db_name LIKE '$table'";
				$tableInDb = $this->conn->query($sql);

				if($tableInDb)
				{
					if($tableInDb->num_rows==1)
					{
						return true;
					}
					else
					{
						array_push($this->result,$table. " does not found!");
						return false;
					}
				}
				else
				{
					return false;
				}
			}

			//Function to disconnect the database!
			public function __destruct()
			{
				if($this->connection)
				{
					if($this->conn->close())
					{
						$this->connection = false;
						return true;
					}
				}
				else
				{
					return false;
				}
			}
			
		}
	?>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>