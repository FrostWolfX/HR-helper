<?php
require_once __DIR__ . '/searchKeyWords.php';

class Curl
{
	private string $url;

	public function __construct(string $url)
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	private function setCache($content, $cacheId)
	{
		if ($content == '') {
			return;
		}
		$fileName = 'cash/' . md5($cacheId);
		if (!file_exists('cash')) {
			mkdir('cash');
		}
		$f = fopen($fileName, 'w+');
		fwrite($f, $content);
		fclose($f);
	}

	private function getCache($cacheId, $cashExpired = true, &$fileName = ''): string
	{
		if (!$cashExpired) {
			return "";
		}
		$fileName = 'cash/' . md5($cacheId);
		if (!file_exists($fileName)) {
			return false;
		}
		$time = time() - filemtime($fileName);
		if ($time > $cashExpired) {
			return false;
		}
		return file_get_contents($fileName);
	}

	/*
	 * добавляет или извлекает страницу из кэша
	 */
	private function curlLoad($url, $cash = 0): string
	{
		$cacheId = $url;
		if ($content = $this->getCache($cacheId, $cash)) {
			return $content;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$content = curl_exec($ch);
		curl_close($ch);

		$this->setCache($content, $cacheId);
		return $content;
	}

	/*
	 * content возвращает всю страницу спарсенную с сайта, для дальнейшего парсинга нужных данных
	 */
	public function content(): string
	{
		return $this->curlLoad($this->getUrl(), $cash = 3600);
	}

	/*
	 * smallViewResume возвращает небольшое описание резюме
	 */
	public function smallViewResume(): array
	{
		$pattern = '~<div data-qa="resume-serp__results-search">.+<div class="bloko-gap bloko-gap_top">~isU';
		preg_match($pattern, $this->content(), $matches);
		return $matches;
	}

	/*
	 * linkResume возвращает массив ссылок на резюме
	 */
	public function link(): array
	{
		$matches = $this->smallViewResume();
		preg_match_all('~href="(.*)">.*</a>~isU', $matches[0], $links);
		/*
		 * дописываю в сылки домен сайта hh.ru
		 */
		array_walk($links[1], function (&$links, $key, $prefix) {
			$links = $prefix . "$links";
		}, 'https://spb.hh.ru');
		return $links[1];
	}

	/*
	 * nameResume возвращает массив названией резюме
	 */
	public function nameResume(): array
	{
		$matches = $this->smallViewResume();
		preg_match_all('~href=".*">(.*)</a>~isU', $matches[0], $links);
		return $links[1];
	}

	public function summaries(): array
	{
		$links = $this->link();
		$summaries = [];
		foreach ($links as $link => $f) {
			//парсинг резюме по ссылкам
			$summar = $this->curlLoad($f, $cash = 3600);
			$pattern = '~<div class="resume-applicant">(?P<resume>.*)?</div>\s*</div>\s*</div>\s*</div></div>\s*</div>\s*</div>\s*</div>\s*</div>\s*</div>~isU';
			preg_match($pattern, $summar, $matches);
			$summaries[$f] = $matches['resume'];
		}
		return $summaries;
	}
}
