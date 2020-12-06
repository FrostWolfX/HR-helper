<?php
require_once __DIR__ . '/head.php';
require_once __DIR__ . '/scr/mainPoint.php';
$text = $_POST['text'];
$keyWords = $_POST['keyWords'];

if (!empty($_POST['text'])) {
	$mainPoint = new MainPoint($text, $keyWords);
	$resumes = $mainPoint->viewAllResume();
	$name = $mainPoint->viewName();

	/*
	 * добавляю в массив резюме название резюме
	 */
	foreach ($resumes as $link => $resume) {
		$resumes[$link]['name'] = $name[$i];
		$i++;
	}
	/*
	 * сортировка резюме по количеству найденых ключей
	 */
	$edition = array_column($resumes, 'countKeys');
	array_multisort($edition, SORT_DESC, $resumes);
	/*
	 * вывод резюме
	 */
	foreach ($resumes as $link => $resume) {

		$r = $resume['resume'];
		$count = $resume['countKeys'];
		$key = implode(', ', $resume['key']);
		$name = $resume['name'];

		echo '<article>'
			. '<a class="links" href="' . $link . '">' . $name . '</a><br>'
			. 'Найдено ключей: ' . $count . '. Ключи: '
			. $key . '<br><br>'
			. $r
			. '</article>';
	}
} else {
	header('Location: http://pixsam.mcdir.ru/');
	exit;
}
require_once __DIR__ . '/bottom.php';