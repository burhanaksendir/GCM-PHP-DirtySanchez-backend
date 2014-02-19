<?php
 
class database extends database_helpers {
 
	private static $instance;
 
	public static function get_instance()
	{
 
		if(!self::$instance){
			self::$instance = new database();
		}
		return self::$instance;
	}
 
	public function __construct()
	{
		parent::__construct();
	}
 
	public function select($table)
	{
		$sql = "SELECT registration FROM `$table`";
		$results = $this->raw($sql)->getAll();
		return $results;
	}
	
	public function getRegIds()
	{
		$array = $this->fetchColumn(1);
		return $array;	
	}
 
	public function checkRegister($regId)
	{
		if($this->checkRow("register_ids", "registration", $regId)){
			return true;
		}else {
			return false;
		}
	}
 
	public function insertRegisterId($regId)
	{
		if($this->checkRegister($regId) == false){
			$sql = "INSERT INTO register_ids (idregister_ids, registration) VALUES (null,\"".$regId."\")";
			$results = $this->raw($sql);
			return true;
		}else {
			return false;
		}
 
	}
	
	public function updateRegisterId($table, $column,$id,$regId,$param)
	{
		
		$results = $this->update($table,$column,$id, $regId,$param);
		return $results;
	}	
}