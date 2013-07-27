<?php
require_once("Rest.inc.php");

class API extends REST 
{
	public $data = "";
	private $db = NULL;
	
	public function __construct()
	{
		parent::__construct();// Init parent contructor
		$this->dbConnect();// Initiate Database connection
		
	}

	//Database connection
	private function dbConnect()
	{
		$this->db = mysql_connect("localhost", "root", "shekhar!");
		if($this->db)
		{
			mysql_select_db("products",$this->db);
		}
	}
	
	//Public method for access api.
	//This method dynmically call the method based on the query string
	public function processApi()
	{
		$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
		if((int)method_exists($this,$func) > 0)
		{
			$this->$func();
		}
		else
		{
			// If the method not exist with in this class, response would be "Page not found".
			$this->response('',404);
		}				
			
	}
	
	// 1.) Retrieve the list of products
	private function listproducts()
	{ 
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		$meminstance = new Memcache();
		$meminstance->pconnect('localhost', 11211);
		$query = "SELECT * FROM products";
		$querykey = "LISTPRODUCTS".md5($query);
		$result_c = $meminstance->get($querykey);
		if(!$result_c)
		{
			$sql = mysql_query("SELECT * FROM products", $this->db);
			if(mysql_num_rows($sql) > 0)
			{
				$result = array();
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC))
				{
					
					$result[] = $rlt;
				}
				$meminstance->set($querykey, serialize($result), 0, 600);
				echo "FROM the mysql table";
				// If success everythig is good send header as "OK" and return list of products from the mysql table
				$this->response($this->json($result), 200);
			}
		}
		else
		if($result_c)
		{
			echo "From cache";
			// If success everythig is good send header as "OK" and return list of products from the cache
			$this->response($this->json(unserialize($result_c)), 200);		
		}
		else
			$this->response('',204); // If no records "No Content" status
	}
	
	//2.) View Product
	private function product()
	{

		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		$meminstance = new Memcache();
		$meminstance->pconnect('localhost', 11211);
		$id = (int)$this->_request['id'];
		if($id > 0)
		{    
			$query = "SELECT * FROM products WHERE id = $id";
			$querykey = "VIEWPRODUCT".md5($query);
			$result_c = $meminstance->get($querykey);
			if(!$result_c)
			{
				$sql = mysql_query("SELECT * FROM products WHERE id = $id");
				if(mysql_num_rows($sql) > 0)
				{
					$result = mysql_fetch_array($sql,MYSQL_ASSOC);
				}
				$meminstance->set($querykey, serialize($result), 0, 600);
				echo "FROM the mysql table";
				// If success everythig is good send header as "OK" and return the product from the mysql table
				$this->response($this->json($result),200);
			}
			else
			if($result_c)
			{
				echo "From cache";
				// If success everythig is good send header as "OK" and return the product from the cache
				$this->response($this->json(unserialize($result_c)), 200);		
			}
		}
		else
		{
			$this->response('',204); // If no records "No Content" status
		}
	}
	
	//3.) Search
	private function searchproduct()
	{

		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		$meminstance = new Memcache();
		$meminstance->pconnect('localhost', 11211);
		$name = $this->_request['name'];
		$query = "SELECT * FROM products WHERE name like '%$name%'";
		$querykey = "SEARCHPRODUCT".md5($query);
		$result_c = $meminstance->get($querykey); 
		if(!$result_c)
		{
			$sql = mysql_query("SELECT * FROM products WHERE name like '%$name%'");
			if(mysql_num_rows($sql) > 0)
			{
				$result = array();
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC))
				{
					
					$result[] = $rlt;
				}
				$meminstance->set($querykey, serialize($result), 0, 600);
				// If success everythig is good send header as "OK" and return list of products from the mysql table
				echo "FROM the mysql table";
				$this->response($this->json($result), 200);
			}
		}
		else
		if($result_c)
		{
			echo "From cache";
			// If success everythig is good send header as "OK" and return list of products from the cache
			$this->response($this->json(unserialize($result_c)), 200);
		}
		else
		{
			$this->response('',204); // If no records "No Content" status
		}
	}
	
	//4.) Create a product
	private function createproduct()
	{
		if($this->get_request_method() != "POST")
		{
			$this->response('',406);
		}
		$name = $this->_request['name'];
		  
		$sql = mysql_query("INSERT INTO products(name) VALUES('$name')");
		if($sql)
		{
			$success = array('status' => "Success", "msg" => "Successfully created a product.");
			$this->response($this->json($success),200);
		}
		else
		{
			$this->response('',400); // Bad request status
		}
	}
	
	//5.) Update a product
	private function updateproduct()
	{
		if($this->get_request_method() != "PUT")
		{
			$this->response('',406);
		}
		$name = $this->_request['name'];
		$id = (int)$this->_request['id'];
		$sql = mysql_query("UPDATE products SET name = '$name' WHERE id = $id");
		if($sql)
		{
			$success = array('status' => "Success", "msg" => "Successfully updated a product.");
			$this->response($this->json($success),200);
		}
		else
		{
			$this->response('',400); //  Bad request status
		}
	}
	
	//6.) Delete a product
	private function deleteproduct()
	{

		if($this->get_request_method() != "DELETE")
		{
			$this->response('',406);
		}
		$id = (int)$this->_request['id'];
		if($id > 0)
		{    
			mysql_query("DELETE FROM products WHERE id = $id");
			$success = array('status' => "Success", "msg" => "Successfully one record deleted.");
			$this->response($this->json($success),200);
		}
		else
		{
			$this->response('',204); // If no records "No Content" status
		}
	}
	
	
	//Encode array into JSON
	private function json($data)
	{
		if(is_array($data))
		{
			return json_encode($data);
		}
	}
}
// Initiate Library
$api = new API;
$api->processApi();

?>
