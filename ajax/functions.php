<?php

// автозагрузка классов, для ajax
require '../lib/autoload.php';

$db = DataBase::getDBObject(); // класс для работы с БД
$table_urls = new TableUrls($db);  // класс для работы с таблицей "urls"

switch ($_POST['action']) {
	
	case 'delete_url':	
		if ($table_urls->deleteOnId($_POST['id'])) {
			echo 'success';
		} else {
			echo 'error';
		}
		break;
		
	case 'insert_url':
		
		if (!$_POST['short_link']) {
			$short = $table_urls->generateShort();
		} else {
			// если пользователь сам вписал короткую ссылку, надо проверить ее на уникальность
			$short = $_POST['short_link'];
			if (!CheckValid::validShortLink($short)) {
				echo 'invalid_short';
				break;
			}
			if ($table_urls->isExistsShort($short)) {
				echo 'short_is_exists';
				break;
			}
		}
		
		if ($table_urls->insert($_POST['original_link'], $short)) {
			echo $short; // возвращаю url, чтобы его добавить в DOM
		} else {
			echo 'error';
		}
		break;
	
	default :
		echo 'error';
}
