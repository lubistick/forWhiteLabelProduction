<?php

// по идее, надо наследоваться от класса, например, Tables, 
// но т.к. таблица всего одна, поэтому этот класс существует без родителя

class TableUrls
{
	public $table_name;
	public $mysqli;
	
	public function __construct($db)
	{
		$this->mysqli = $db->mysqli;
		$this->table_name = Config::$db_prefix . 'urls';
	}
	
	public function getAll()
	{
		$sql = 'SELECT * FROM ' . $this->table_name;
		$stmt = $this->mysqli->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($id, $original, $short);
		
		$i = 0;
		$data = array();
		while ($stmt->fetch()) {
			$data[$i]['id'] = $id;
			$data[$i]['original'] = $original;
			$data[$i]['short'] = $short;
			
			$i++;
		}
		$stmt->close();
		//print_r($data);
		return $data;
	}
	
	public function deleteOnId($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . ' WHERE id = ?';
		$stmt = $this->mysqli->prepare($sql);
		$stmt->bind_param('i', $id);
		$success = $stmt->execute();
		$stmt->close();
		return $success;
	}
	
	public function insert($original, $short)
	{
		$sql = 'INSERT INTO ' . $this->table_name . ' (original, short) VALUES (?, ?)';
		$stmt = $this->mysqli->prepare($sql);
		$stmt->bind_param('ss', $original, $short);
		$success = $stmt->execute();
		$stmt->close();
		return $success;
	}
	
	public function isExistsShort($short)
	{
		$sql = 'SELECT id FROM ' . $this->table_name . ' WHERE short = ?';
		$stmt = $this->mysqli->prepare($sql);
		$stmt->bind_param('s', $short);
		$stmt->execute();
		$stmt->bind_result($id);
		
		$i = 0;
		$data = array();
		while ($stmt->fetch()) {
			$data[$i]['id'] = $id;
			
			$i++;
		}
		$stmt->close();
		if (count($data) === 1) {
			return true;
		} else {
			return false;
		}
	}
	
	public function generateShort($length = 6)
	{
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		//$chars = 'abcd';
		$num_chars = strlen($chars);
		$short = '';
		for ($i = 0; $i < $length; $i++) {
			$short .= substr($chars, mt_rand(1, $num_chars) - 1, 1);
		}
		
		// если сгенерировалось значение, которое существует, надо сгенерировать заново
		// для этого делаю рекурсию, она правильно работает, я проверял =)
		if ($this->isExistsShort($short)) {
			//echo 'Here ';
			$new_short = $this->generateShort();
			return $new_short;
		}
		return $short;
	}
	
	public function getOriginalOnShort($short)
	{ 
		$sql = 'SELECT original FROM ' . $this->table_name . ' WHERE short = ?';
		$stmt = $this->mysqli->prepare($sql);
		$stmt->bind_param('s', $short);
		$stmt->execute();
		$stmt->bind_result($original);
		
		$stmt->fetch();
		$stmt->close();
		return $original;
	}
}
