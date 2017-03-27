<?php

require_once '../lib/database_class.php';
require_once '../lib/checkvalid_class.php';

$db_obj = DataBase::getDBObject();

switch ($_POST['action']) {
	
	case 'delete_url':	
		if ($db_obj->deleteOnId('urls', $_POST['id'])) {
			echo 'success';
		} else {
			echo 'error';
		}
		break;
		
	case 'insert_url':
		
		if (!$_POST['short_link']) {
			$short = $db_obj->generateUrl();
		} else {
			// если пользователь сам вписал короткую ссылку, надо проверить ее на уникальность
			$short = $_POST['short_link'];
			if (!CheckValid::validShortLink($short)) {
				echo 'invalid_short';
				break;
			}
			if ($db_obj->isExists('urls', 'short', $short)) {
				echo 'short_is_exists';
				break;
			}
		}
		
		if ($db_obj->insert('urls', array('original' => $_POST['original_link'], 'short' => $short))) {
			echo $short; // возвращаю url, чтобы его добавить в DOM
		} else {
			echo 'error';
		}
		break;
	
	default :
		echo 'error';
}