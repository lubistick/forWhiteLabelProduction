'use strict';

var button = document.querySelector('#easy_links button');
var original_link = document.querySelector('#easy_links input[name=original_link]');
var short_link = document.querySelector('#easy_links input[name=short_link]');

button.onclick = function()
{
	if (checkOriginalLink()) {
		var o_link = original_link.value.replace(/\s/g, '');
		var short_link = document.querySelector('#easy_links input[name=short_link]');
		var s_link = short_link.value.replace(/\s/g, '');
		//console.log(s_link);
		insertUrl(o_link, s_link);
	} else {
		return;
	}
};

original_link.onblur = function()
{
	checkOriginalLink();
};

original_link.oninput = function()
{
	originalLinkIsFull(true);
};

short_link.oninput = function()
{
	shortLinkError(false, '');
};

// Вешаю событие click на все кнопки "удалить" в таблице
var table = document.querySelector('#easy_links_table');
table.onclick = function(event)
{
	var target = event.target;
	if (target.tagName == 'BUTTON') {
		var original_link = target.parentElement.previousElementSibling.previousElementSibling;
		var short_link = target.parentElement.previousElementSibling;
		var confirmDelete = confirm('Вы действительно хотите удалить короткую ссылку ' + short_link.innerHTML + '?');
		
		if (confirmDelete) {
			var row = target.parentElement.parentElement;
			var id = target.dataset.id;
			deleteUrl(id, row); // удаление из БД
		}
	}
};

function checkOriginalLink()
{
	var link = original_link.value.replace(/\s/g, '');
	if (!link) {
		originalLinkIsFull(false);
		return false;
	} else if (link.length > 255) {
		originalLinkTooLong();
		return false;
	};
	return true;
}

function originalLinkIsFull(is_full)
{
	var span = document.querySelectorAll('#easy_links span.error_text')[0];
	if (is_full) {
		span.innerHTML = '';
		original_link.classList.remove('error_border');
	} else {
		span.innerHTML = 'Поле не должно быть пустым!';
		original_link.classList.add('error_border');
	};
}

function originalLinkTooLong()
{
	var span = document.querySelectorAll('#easy_links span.error_text')[0];
	span.innerHTML = 'Максимальная длина ссылки 255 символов!';
	original_link.classList.add('error_border');
}

function getXHR()
{
	var xhr = ('onload' in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
	return new xhr();
}

function deleteUrl(id, row)
{
	var xhr = getXHR();
	xhr.open('POST', 'ajax/functions.php', true);
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhr.send('id=' + encodeURIComponent(id) + '&action=delete_url');
	xhr.onreadystatechange = function()
	{
		if (this.readyState == 4) {
			if (this.status == 200) {
				if (this.responseText == 'success') {
					deleteRow(row); // удаление из DOM
				} else {
					alert('Ошибка в deleteUrl');
				}
			} else {
				alert('Ошибка: ' + (this.status ? this.statusText : 'запрос не удался'));
			}
		}
	}
}

function insertUrl(original_link, short_link)
{
	// console.log(original_link);
	var xhr = getXHR();
	xhr.open('POST', 'ajax/functions.php', true);
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhr.send(
		'original_link=' + 
		encodeURIComponent(original_link) + 
		'&short_link=' + 
		encodeURIComponent(short_link) + 
		'&action=insert_url'
	);
	xhr.onreadystatechange = function()
	{
		if (this.readyState == 4) {
			if (this.status == 200) {
				if (this.responseText == 'error') {
					alert('Ошибка в insertUrl');
				} else if (this.responseText == 'short_is_exists') {
					// short_link не уникальный
					shortLinkError(true, 'Эта короткая ссылка уже занята!');
				} else if (this.responseText == 'invalid_short') {
					//alert('Короткая ссылка должна содаржеть только символы a-z и/или циры 0-9');
					shortLinkError(true, 'Короткая ссылка должна содаржеть только символы a-z и/или циры 0-9, и не превышать 255 символов');
				} else {
					//console.log(this.responseText);
					insertRow(original_link, this.responseText); // добавление в DOM
					clearInputs();
				}
			} else {
				alert('Ошибка: ' + (this.status ? this.statusText : 'запрос не удался'));
			}
		}
	}
}

function clearInputs()
{
	original_link.value = '';
	short_link.value = '';
}


function insertRow(original_link, short_link)
{
	var tr = document.createElement('tr');
	tr.innerHTML = '<td>' + original_link + '</td><td>test.tristfain.ru/' + short_link + '</td><td><button title="Удалить">X</button></td>';
	
	var table = document.querySelector('#easy_links_table');
	table.appendChild(tr);
}

function shortLinkError(bool, message)
{
	var short_link = document.querySelector('#easy_links input[name=short_link]');
	var span = document.querySelectorAll('#easy_links span.error_text')[1];
	if (!bool) {
		span.innerHTML = '';
		short_link.classList.remove('error_border');
	} else {
		span.innerHTML = message;
		short_link.classList.add('error_border');
	};
}

function deleteRow(row)
{
	row.parentElement.removeChild(row);
}
