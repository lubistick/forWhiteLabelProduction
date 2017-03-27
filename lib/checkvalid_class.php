<?php

// В реальном приложении было бы больше валидации

class CheckValid
{
	public static function validOriginalLink($str)
	{
		$str = trim($str);
		if ((!is_string($str)) or (strlen($str) > 255) or (strlen($str) < 1)) {
			return false;
		}
		return true;
	}
	
	public function validShortLink($str)
	{
		$str = trim($str);
		if (!preg_match("/^[a-z0-9]+$/", $str)) {
			return false;
		}
		if ((!is_string($str)) or (strlen($str) > 255) or (strlen($str) < 1)) {
			return false;
		}
		return true;
	}
}
