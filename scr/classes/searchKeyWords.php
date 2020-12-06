<?php

class SearchKeyWorld
{
	private array $summaries;
	private array $keywords;

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

	public function keywords(): array
	{
		$summaries = $this->getSummaries();
		$keywords = $this->getKeywords();
		$word = [];
		$quantity = 0;

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
		return $summaries;
	}
}