<?php


require_once __DIR__ . '/head.php';
require_once __DIR__ . '/scr/mainPoint.php';
$text = $_POST['text'];
$keyWords = $_POST['keyWords'];

if (!empty($_POST['text'])) {
	$mainPoint = new MainPoint($text, $keyWords);
	$resumes = $mainPoint->viewAllResume();
	foreach ($resumes as $resume) {
		echo '<article>' . $resume . '</article>';
	}
} else {
	header('Location: http://pixsam.mcdir.ru/');
	exit;
}
require_once __DIR__ . '/bottom.php';