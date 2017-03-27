<?php

require_once('config_class.php');

class DataBase
{
	private $mysqli;
	private static $database = null;
	
	private function __construct()
	{
		$this->mysqli = new mysqli(
			Config::$host,
			Config::$user,
			Config::$password,
			Config::$db
		);
	}
	
	public static function getDBObject()
	{
		if (self::$database === null) {
			self::$database = new DataBase();
		}
		return self::$database;
	}
	
	public function getAll($table_name)
	{
		return $this->select($table_name, array('*'), '', '', true);
	}
	
	public function getField($table_name, $field_out, $field_in, $value_in)
	{ 
		$data = $this->select($table_name, array($field_out), "`$field_in`='".addslashes($value_in)."'");
		if (count($data) != 1) {
			return false;
		}
		//если select вернул 0 или более 1 строки, то использовать данный метод некорректно
		return $data[0][$field_out];
	}
	
	public function deleteOnID($table_name, $id)
	{
		return $this->deleteFromTable($table_name, "`id` = '$id'");
	}
	
	public function insert($table_name, $new_values)
	{
		$table_name = Config::$db_prefix . $table_name;
		$query = 'INSERT INTO ' . $table_name . ' (';
		foreach ($new_values as $field => $value) {
			$query .= '`' . $field . '`,';
		}
		$query = substr($query, 0, -1);
		$query .= ') VALUES (';
		foreach ($new_values as $field => $value) {
			$query .= "'" . addslashes($value) . "',";
		}
		$query = substr($query, 0, -1);
		$query .= ')';
		return $this->query($query);
	}
	
	public function generateUrl($length = 6)
	{
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		//$chars = 'abcd';
		$num_chars = strlen($chars);
		$string = '';
		for ($i = 0; $i < $length; $i++) {
			$string .= substr($chars, mt_rand(1, $num_chars) - 1, 1);
		}
		
		// если сгенерировалось значение, которое существует, надо сгенерировать заново
		// для этого делаю рекурсию, она правильно работает, я проверял =)
		if ($this->isExists('urls', 'short', $string)) {
			//echo 'Here ';
			$new_string = $this->generateUrl();
			return $new_string;
		}
		return $string;
	}
	
	// функция проверяет, есть ли поле field со значением value в таблице table_name
	public function isExists($table_name, $field, $value) 
	{
		$data = $this->select($table_name, array('id'), "`$field` = '" . addslashes($value) . "'");
		if (count($data) === 0) return false;
		return true;
	}
	
	/*
	Вспомогательные функци
	*/
	
	private function query($query)
	{
		return $this->mysqli->query($query);
	}
	
	private function select($table_name, $fields, $where = '', $order = '', $up = true, $limit = '', $distinct = false)
	{
		for ($i = 0; $i < count($fields); $i++) {
			if ((strpos($fields[$i], '(') === false) && ($fields[$i] != '*')) $fields[$i] = '`'.$fields[$i].'`';
		}
		$fields = implode(',', $fields); //превращаю массив $fields в строку
		$table_name = Config::$db_prefix . $table_name; // соединяю с секретным префиксом
				//echo 'here ';
		if (!$order) {
			$order = 'ORDER BY `id`';
			if (!$up) {
				$order .= ' DESC';
			}
		} elseif ($order != 'RAND()') {
			$order = 'ORDER BY `' . $order . '`';
			if (!$up) {
				$order .= ' DESC';
			}
		} else {
			$order = 'ORDER BY ' . $order;
		}
		$distinct = ($distinct === true)? 'DISTINCT ' : '';
		if ($limit) {
			$limit = 'LIMIT ' . $limit;
		}
		if ($where) {
			$query = 'SELECT ' . $distinct . $fields . ' FROM ' . $table_name . ' WHERE ' . $where . ' ' . $order . ' ' . $limit;
		} else {
			$query = 'SELECT ' . $distinct . $fields . ' FROM ' . $table_name . ' ' . $order . ' ' . $limit;
		}
		// echo $query.'<br />';
		
		$result_set = $this->query($query);
		if (!$result_set) {
			return false;
		}
		$i = 0;
		$data = array();
		while ($row = $result_set->fetch_assoc()) {
			$data[$i] = $row; //сформировал двумерный массив
			$i++;
		}
		$result_set->close();
		// print_r($data);
		return $data; //возвращаю двумерный массив
	}
	
	private function deleteFromTable($table_name, $where = '')
	{
		$table_name = Config::$db_prefix . $table_name;
		if ($where) {
			$query = 'DELETE FROM ' . $table_name . ' WHERE ' . $where;
			return $this->query($query);
		}
		return false;
	}
	
	public function __destruct()
	{
		if ($this->mysqli) {
			$this->mysqli->close();
		}
	}
}
