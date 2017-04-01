<?php

function myAutoload($class_name)
{
	require __DIR__ . '/' . mb_strtolower($class_name) . '_class.php';
}
spl_autoload_register('myAutoload');
