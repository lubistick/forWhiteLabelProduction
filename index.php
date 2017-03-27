<?php 

require_once 'lib/model.php';

$db_obj = DataBase::getDBObject(); // создаю экземпляр класса для работы с БД

// controller
if (isset($_GET['url'])) {
	if ($db_obj->isExists('urls', 'short', $_GET['url'])) {
		$location = $db_obj->getField('urls', 'original', 'short', $_GET['url']); // получаю ссылку для редиректа
		header('Location: ' . $location); // редирект
		exit();
	} else {
		header('HTTP/1.0 404 Not Found');
		include 'not_found.html'; // страница 404
		exit();
	}
} else {
	$all_urls = $db_obj->getAll('urls'); // получаю всю таблицу Urls
	include 'view.html'; // главная страница
}
