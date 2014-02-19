<?php

class database_helpers {

	private $results;

	public function __construct() {
		$this -> c = config::get('pdo_database_settings');

		try {

			$this -> db = new PDO($this -> c['dsn'], $this -> c['user'], $this -> c['pass']);

		} catch(PDOException $e) {

			echo "A database error occurred: <b>" . $e -> getMessage() . "</b>";
			exit();

		}

	}

	public function query($sql, $params = null) {
		$this -> results = $this -> db -> prepare($sql);
		$this -> results -> execute($params);
		return $this;
	}

	public function raw($sql) {
		$this -> results = $this -> db -> query($sql);
		return $this;
	}

	public function checkRow($row, $field, $value) {
		$sql = "SELECT EXISTS(SELECT * FROM `$row` WHERE `$field` = :value)";
		$result = $this -> query($sql, array(":value" => $value)) -> getAll();
		print_r($result);
		if ($result[0][0] == 0) {
			return false;
		} else {
			return true;
		}

	}

	public function delete($table, $field, $param) {
		$sql = "DELETE FROM `$table` WHERE `$field` = :param";

		return $this -> query($sql, array(":param" => $param));
	}

	public function update($table, $keycolumn, $key, $value,$param) {
			$sql = "UPDATE `$table` SET `$keycolumn`='{$value}' WHERE `$key` = :param";

			return $this -> query($sql, array(":param" => $param));
		}
	

	public function getObj() {
		$obj = $this -> results -> fetch(PDO::FETCH_OBJ);
		return $obj;
	}

	public function getAll() {
		$all = $this -> results -> fetchAll();
		return $all;
	}

	public function fetchColumn($column) {
		$sql = "SELECT * FROM register_ids";
		$results = $this -> db -> prepare($sql);
		$results -> execute();
		$array = $results -> fetchAll(PDO::FETCH_COLUMN, $column);

		return $array;
	}

	public function getArray() {
		$array = $this -> results -> fetch(PDO::FETCH_ASSOC);
		return $array;
	}

	public function getLazy() {
		$lazy = $this -> results -> fetch(PDO::FETCH_LAZY);
		return $lazy;
	}

}
