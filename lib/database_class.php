<?php

class DataBase
{
	public $mysqli;
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
	
	public function __destruct()
	{
		if ($this->mysqli) {
			$this->mysqli->close();
		}
	}
}
