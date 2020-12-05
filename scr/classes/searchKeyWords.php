<?php

class SearchKeyWorld
{
	private $summaries;
	private $keywords;

	public function __construct(array $summaries, array $keywords)
	{
		$this->summaries = $summaries;
		$this->keywords = $keywords;
	}

	/**
	 * @return array
	 */
	private function getSummaries(): array
	{
		return $this->summaries;
	}

	/**
	 * @return array
	 */
	private function getKeywords(): array
	{
		return $this->keywords;
	}

	private function setSummaries(array $summaries)
	{
		$this->summaries = $summaries;
	}

	function keywords(): array
	{
		$summaries = $this->getSummaries();
		$keywords = $this->getKeywords();
		$word = [];
		$quantity = 0;
		/*
		 * массив выделенных слов
		 */
		/*for ($i = 0; $i < count($keywords); $i++) {
			$keywordsGreen[$i] = '<span style="font-size: 200%; font-family: monospace; background: lightgreen">' . $keywords[$i] . '</span>';
		}*/
		foreach ($summaries as $link => $summary) {
			$f = 0;
			foreach ($keywords as $keyword) {
				$replace = '<span style="font-size: 200%; font-family: monospace; background: #90ee90">' . $keyword . '</span>';
				if ($f == 0) {
					$query = str_ireplace($keyword, $replace, $summary, $count);
				} else {
					$query = str_ireplace($keyword, $replace, $summaries[$link]['resume'], $count);
				}

				if ($count > 0) {
					$word[] = $keyword;
					$quantity++;
				}
				$summaries[$link] = ['resume' => $query];
				$f++;
			}
			$summaries[$link] = ['resume' => $query,
				'countKeys' => $quantity,
				'key' => $word,
			];
			$word = [];
			$quantity = 0;
		}
		/*for ($i = 0; $i < count($keywords); $i++) {
			$keywordsGreen[$i] = '<span style="font-size: 200%; font-family: monospace; background: lightgreen">' . $keywords[$i] . '</span>';
		}
		$sum = 0;
		$word = [];

		foreach ($summaries as $link => $resume) {
			for ($i = 0; $i < count($keywords); $i++) {
				$search = mb_strtolower($keywords[$i], 'UTF-8');
				$replace = '<span style="font-size: 200%; font-family: monospace; background: lightgreen">' . $search . '</span>';
				if ($i == 0) {
					$subject = $resume;
				} else {
					$subject = $query;
				}
				$query = str_ireplace($search, $replace, $subject, $count);


				if ($count > 0) {
					$summaries[$link] = $query;
					$sum++;
					$word[] = $search;
				}
			}
			foreach ($keywords as $key){
				$i = str_ireplace($key, $keywordsGreen, $summaries[$link], $c);
				if ($c > 0){
					var_dump($key);
				}
			}
//			$i = str_ireplace($keywords, $keywordsGreen, $summaries[$link], $c);
//			$summaries[$link] = $i;

			$summaries[$link] = [$i, $sum, $word];

			$sum = 0;
			$word = [];
		}
		var_dump($summaries);*/
		return $summaries;
	}

	function morphy()
	{
		// Подключите файл common.php. phpmorphy-0.3.2 - для версии 0.3.2,
		// если используется иная версия исправьте код.
		require_once __DIR__ . '/../phpmorphy-0.3.7/src/common.php';

		// Укажите путь к каталогу со словарями
		$dir = '/../dicts';

		// Укажите, для какого языка будем использовать словарь.
		// Язык указывается как ISO3166 код страны и ISO639 код языка,
		// разделенные символом подчеркивания (ru_RU, uk_UA, en_EN, de_DE и т.п.)
		$lang = 'ru_RU';

		// Укажите опции
		// Список поддерживаемых опций см. ниже
		$opts = array(
			'storage' => PHPMORPHY_STORAGE_FILE,
		);

		// создаем экземпляр класса phpMorphy
		// обратите внимание: все функции phpMorphy являются throwable т.е.
		// могут возбуждать исключения типа phpMorphy_Exception (конструктор тоже)
		try {
			$morphy = new phpMorphy($dir, $lang, $opts);
		} catch (phpMorphy_Exception $e) {
			die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
		}

// далее под $morphy мы подразумеваем экземпляр класса phpMorphy


	}
}