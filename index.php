<?php 

mb_internal_encoding('UTF-8');

// автозагрузка классов
function myAutoload($class_name)
{
	require __DIR__ . '/lib/' . mb_strtolower($class_name) . '_class.php';
}
spl_autoload_register('myAutoload');

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
