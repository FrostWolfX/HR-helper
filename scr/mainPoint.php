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

	/**
	 * @return string
	 */
	public function getStrSearch(): string
	{
		return $this->strSearch;
	}

	/**
	 * @return string
	 */
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
	public function viewAllResume()
	{
		$text = $this->getStrSearch();
		$strSearch = $this->workSearchText($text);
		//адрес поиска text=что ищем

		$s=rawurlencode($text);//сайт в кодировке win-1251
		$url = 'https://spb.hh.ru/search/resume?clusters=True&area=2&order_by=relevance&logic=normal&pos=position&exp_period=all_time&no_magic=False&ored_clusters=True&st=resumeSearch&text='
			. $s;

		$curl = new Curl($url);
		$summaries = $curl->summaries();

		$searchKeyWorld = new SearchKeyWorld($summaries, $this->getKeyWords());
		$sr = $searchKeyWorld->keywords();
		return $sr;
	}

	/*
	 * вернуть склеенные массивы ссылка=>название резюме
	 */
	public function viewNameResume()
	{
		$text = $this->getStrSearch();
		$strSearch = $this->workSearchText($text);
		//адрес поиска text=что ищем
		$url = 'https://spb.hh.ru/search/resume?clusters=True&area=2&order_by=relevance&logic=normal&pos=position&exp_period=all_time&no_magic=False&ored_clusters=True&st=resumeSearch&text='
			. $strSearch;

		$curl = new Curl($url);
		$content = $curl->content();

		$smallResume = $curl->smallViewResume($content);
		$link = $curl->linkResume($content);
		$name = $curl->nameResume($content);
		return array_combine($link, $name);
	}
}
