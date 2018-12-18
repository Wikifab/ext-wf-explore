<?php

class WfTutorialUtils {

	static function getFields($content) {
		$pattern = "/\|([_a-zA-Z0-9\-]+)\=/";
		preg_match_all($pattern, $content, $matches);
		if($matches) {
			return $matches[1];
		}
		return array();
	}

	static private function getFieldFromContent($field,$content) {
		$pattern = "/\|".$field."\=([^\|\}]*)/s";
		preg_match($pattern, $content, $matches);

		if($matches && strpos($matches[1], '{') !== false) {
			$result = rtrim ( $matches[1] );
			// hack : if fields contain '{', the first regexp may be wrong
			// try to find expression whit | or { on new line :
			$pattern = "/\|".$field."\=([^\|]*)\n([}|])/s";
			preg_match($pattern, $content, $matches);
			if ($matches) {
				return rtrim ( $matches[1] );
			}
			return $result;
		}
		if($matches) {
			return rtrim ( $matches[1] );
		}
		return false;
	}


	/**
	* return article data formated in arrays
	 * @params $text string
	 * @return array
	 */
	static public function getArticleData($content) {

		$fields = self::getFields($content);

		$result = array();
		foreach ($fields as $field) {
			$result[$field] = self::getFieldFromContent($field, $content);
		}

		return $result;
	}
}