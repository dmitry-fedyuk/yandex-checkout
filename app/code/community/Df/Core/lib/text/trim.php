<?php
/**
 * 2017-06-09
 * @used-by df_oqi_desc()
 * @param string $s
 * @param int|null $max [optional]
 * @return string
 */
function df_chop($s, $max = null) {return !$max || (mb_strlen($s = df_trim($s)) <= $max) ? $s :
	df_trim_right(mb_substr($s, 0, $max - 1)) . '…'
;}

/**
 * Обратите внимание, что иногда вместо данной функции надо применять @see trim().
 * Например, @see df_trim() не умеет отсекать нулевые байты,
 * которые могут образовываться на конце строки
 * в результате шифрации, передачи по сети прямо в двоичном формате, и затем обратной дешифрации
 * посредством @see Varien_Crypt_Mcrypt.
 * @see Df_Core_Model_RemoteControl_Coder::decode()
 * @see Df_Core_Model_RemoteControl_Coder::encode()
 * 2017-07-01 Добавил параметр $throw.
 * @used-by df_parse_colon()
 * @param string|string[] $s
 * @param string $charlist [optional]
 * @param bool|mixed|\Closure $throw [optional]
 * @return string|string[]
 */
function df_trim($s, $charlist = null, $throw = false) {return df_try(function() use($s, $charlist, $throw) {
	/** @var string|string $result */
	if (is_array($s)) {
		$result = df_map('df_trim', $s, [$charlist, $throw]);
	}
	else {
		if (!is_null($charlist)) {
			/** @var string[] $addionalSymbolsToTrim */
			$addionalSymbolsToTrim = ["\n", "\r", ' '];
			foreach ($addionalSymbolsToTrim as $addionalSymbolToTrim) {
				/** @var string $addionalSymbolToTrim */
				if (!df_contains($charlist, $addionalSymbolToTrim)) {
					$charlist .= $addionalSymbolToTrim;
				}
			}
		}
		/**
		 * Обратите внимание, что класс Zend_Filter_StringTrim может работать некорректно
		 * для строк, заканчивающихся заглавной кириллической буквой «Р».
		 * http://framework.zend.com/issues/browse/ZF-11223
		 * Однако решение, которое предложено по ссылке выше
		 * (http://framework.zend.com/issues/browse/ZF-11223)
		 * может приводить к падению интерпретатора PHP
		 * для строк, начинающихся с заглавной кириллической буквы «Р».
		 * Такое у меня происходило в методе @see Df_Autotrading_Model_Request_Locations::parseLocation()
		 * Кто виноват: решение или исходный класс @see Zend_Filter_StringTrim — не знаю
		 * (скорее, решение).
		 * Поэтому мой класс @see \Df\Zf\Filter\StringTrim дополняет решение по ссылке выше
		 * программным кодом из Zend Framework 2.0.
		 */
		/** @var \Df\Zf\Filter\StringTrim $filter */
		$filter = new \Df\Zf\Filter\StringTrim($charlist);
		$result = $filter->filter($s);
		/**
		 * @see Zend_Filter_StringTrim::filter() теоретически может вернуть null,
		 * потому что этот метод зачастую перепоручает вычисление результата функции @uses preg_replace()
		 * @url http://php.net/manual/function.preg-replace.php
		 */
		$result = df_nts($result);
		// Как ни странно, Zend_Filter_StringTrim иногда выдаёт результат « ».
		if (' ' === $result) {
			$result = '';
		}
	}
	return $result;
}, false === $throw ? $s : $throw);}

/**
 * Пусть пока будет так. Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
 * 2017-08-18 Today I have noticed that $charlist = null does not work for @uses ltrim()
 * @used-by df_trim_ds_left()
 * @param string $s
 * @param string $charlist [optional]
 * @return string
 */
function df_trim_left($s, $charlist = null) {return ltrim($s, $charlist ?: " \t\n\r\0\x0B");}

/**
 * Пусть пока будет так. Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
 * 2017-08-18 Today I have noticed that $charlist = null does not work for @uses rtrim()
 * @used-by df_chop()            
 * @used-by df_file_ext_def()
 * @used-by df_trim_ds_right()
 * @param string $s
 * @param string $charlist [optional]
 * @return string
 */
function df_trim_right($s, $charlist = null) {return rtrim($s, $charlist ?: " \t\n\r\0\x0B");}

/**
 * Отсекает у строки $s подстроку $trim, если она встречается в начале или в конце строки $s
 * 2016-10-28
 * Добавил поддержку нескольких $needle.
 * @param string $s
 * @param string|string[] $trim
 * @return string
 */
function df_trim_text($s, $trim) {return df_trim_text_left(df_trim_text_right($s, $trim), $trim);}

/**
 * 2016-10-28
 * @used-by df_trim_text_left()
 * @used-by df_trim_text_right()
 * @param string $s
 * @param string[] $trimA
 * @param callable $f
 * @return string
 */
function df_trim_text_a($s, array $trimA, callable $f) {
	$r = $s; /** @var string $r */
	$l = mb_strlen($r); /** @var int $l */
	foreach ($trimA as $trim) {/** @var string $trim */
		if ($l !== mb_strlen($r = call_user_func($f, $r, $trim))) {
			break;
		}
	}
	return $r;
}

/**
 * Отсекает у строки $s заданное начало $trim.
 * 2016-10-28 Добавил поддержку нескольких $trim.
 * @used-by df_media_url2path()
 * @used-by dfsm_code_short()        
 * @param string $s
 * @param string|string[] $trim
 * @return string
 */
function df_trim_text_left($s, $trim) {return is_array($trim) ? df_trim_text_a($s, $trim, __FUNCTION__) : (
	$trim === mb_substr($s, 0, $l = mb_strlen($trim)) ? mb_substr($s, $l) : $s
);}

/**
 * Отсекает у строки $s заданное окончание $trim.
 * 2016-10-28 Добавил поддержку нескольких $trim.
 * @param string $s
 * @param string|string[] $trim
 * @return string
 */
function df_trim_text_right($s, $trim) {return is_array($trim) ? df_trim_text_a($s, $trim, __FUNCTION__) : (
	0 !== ($l = mb_strlen($trim)) && $trim === mb_substr($s, -$l) ? mb_substr($s, 0, -$l) : $s
);}