<?php
require_once __DIR__ . '/classes/curl.php';
require_once __DIR__ . '/classes/searchKeyWords.php';

class MainPoint
{
	private string $strSearch;
	private string $keyWords;

	public function __construct(string $strSearch, string $keyWords)
	{
		$this->strSearch = $strSearch;
		$this->keyWords = $keyWords;
	}

	public function getStrSearch(): string
	{
		return $this->strSearch;
	}

	public function getKeyWords(): array
	{
		//формируем массив ключей для поиска keywords
		$strKey = str_ireplace(', ', ',', $this->keyWords);
		$keywords = explode(',', $strKey);
		if ($keywords === "") {
			return [];
		} else {
			return $keywords;
		}
	}


	//обработка переменных
	private function workSearchText(string $tempSearch): string
	{
		//замена пробелов в строке поиска на +
		if (strpos($tempSearch, ' ') !== false) {
			$tempSearch = str_ireplace(" ", "+", $tempSearch);
		}
		//если строка поиска пустая ничего не делать
		if (empty($tempSearch)) {
			return '';
		}
		return $tempSearch;
	}

	/*
	 * вернуть резюме с выделенными словами
	 */
	public function viewAllResume(): array
	{
		$text = $this->getStrSearch();

		$s = rawurlencode($text);//сайт в кодировке win-1251
		$strSearch = $this->workSearchText($s);
		$url = 'https://spb.hh.ru/search/resume?clusters=True&area=2&order_by=relevance&logic=normal&pos=position&exp_period=all_time&no_magic=False&ored_clusters=True&st=resumeSearch&text='
			. $strSearch;

		$curl = new Curl($url);
		$summaries = $curl->summaries();

		/*
		 * вызов поиска ключевых слов, возвращает массив
		 * 'resume'
		 * 'countKeys'
		 * 'key'
		 */
		$searchKeyWorld = new SearchKeyWorld($summaries, $this->getKeyWords());
		return $searchKeyWorld->keywords();
	}

	public function viewName(): array
	{
		$text = $this->getStrSearch();

		$s = rawurlencode($text);//сайт в кодировке win-1251
		$url = 'https://spb.hh.ru/search/resume?clusters=True&area=2&order_by=relevance&logic=normal&pos=position&exp_period=all_time&no_magic=False&ored_clusters=True&st=resumeSearch&text='
			. $s;
		$curl = new Curl($url);
		return $curl->NameResume();
	}
}
