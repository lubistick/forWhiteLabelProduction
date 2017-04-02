<?php 

mb_internal_encoding('UTF-8');

// автозагрузка классов
require 'lib/autoload.php';

$db = DataBase::getDBObject(); // класс для работы с БД
$table_urls = new TableUrls($db);  // класс для работы с таблицей "urls"

// controller
if (isset($_GET['url'])) {
	if ($table_urls->isExistsShort($_GET['url'])) {
		$location = $table_urls->getOriginalOnShort($_GET['url']); // получаю ссылку для редиректа
		header('Location: ' . $location); // редирект
		exit();
	} else {
		header('HTTP/1.0 404 Not Found');
		include 'not_found.html'; // страница 404
		exit();
	}
} else {
	$all_urls = $table_urls->getAll('urls'); // получаю всю таблицу Urls
	include 'view.html'; // главная страница
}
