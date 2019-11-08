<?php
/**
 * Обратите внимание, что мы намеренно не используем для @uses Df_Core_Dumper
 * объект-одиночку, потому что нам надо вести учёт выгруженных объектов,
 * чтобы не попасть в бесконечную рекурсию при циклических ссылках.
 * @param \Varien_Object|mixed[]|mixed $value
 * @return string
 */
function df_dump($value) {return \Df\Core\Dumper::i()->dump($value);}

/**
 * @param mixed[] $args
 * @return string
 */
function df_format(...$args) {
	$args = df_args($args);
	/** @var string $result */
	$result = null;
	switch (count($args)) {
		case 0:
			$result = '';
			break;
		case 1:
			$result = $args[0];
			break;
		case 2:
			/** @var mixed $params */
			$params = $args[1];
			if (is_array($params)) {
				$result = strtr($args[0], $params);
			}
			break;
	}
	return !is_null($result) ? $result : df_sprintf($args);
}

/**
 * 2017-07-09
 * @used-by df_api_rr_failed()
 * @used-by \Df\API\Client::p()
 * @used-by \Df\Qa\Context::render()
 * @param array(string => string) $a
 * @param int|null $pad [optional]
 * @return string
 */
function df_format_kv(array $a, $pad = null) {return df_cc_n(df_map_k(df_clean($a),
	function($k, $v) use($pad) {return
		(!$pad ? "$k: " : df_pad("$k:", $pad))
		.(is_array($v) || (is_object($v) && !method_exists($v, '__toString')) ? "\n" . df_json_encode($v) : $v)
	;}
));}

/**
 * 2019-06-13
 * @param array(string => string) $a
 * @return string
 */
function df_format_kv_table(array $a) {return df_tag('table', [], df_map_k(
	df_clean($a), function($k, $v) {return
		df_tag('tr', [], [
			df_tag('td', [], $k)
			,df_tag('td', [],
				is_array($v) || (is_object($v) && !method_exists($v, '__toString'))
					? "\n" . df_json_encode($v) : $v					
			)
		])
	;}
));}

/**
 * Эта функция имеет 2 отличия от @see print_r():
 * 1) она корректно обрабатывает объекты и циклические ссылки
 * 2) она для верхнего уровня не печатает обрамляющее «Array()» и табуляцию, т.е. вместо
 *		Array
 *		(
 *			[pattern_id] => p2p
 *			[to] => 41001260130727
 *			[identifier_type] => account
 *			[amount] => 0.01
 *			[comment] => Оплата заказа №100000099 в магазине localhost.com.
 *			[message] =>
 *			[label] => localhost.com
 *		)
 * выводит:
 *	[pattern_id] => p2p
 *	[to] => 41001260130727
 *	[identifier_type] => account
 *	[amount] => 0.01
 *	[comment] => Оплата заказа №100000099 в магазине localhost.com.
 *	[message] =>
 *	[label] => localhost.com
 *
 * @param array(string => string) $params
 * @return mixed
 */
function df_print_params(array $params) {return \Df\Core\Dumper::i()->dumpArrayElements($params);}

/**
 * @param string|mixed[] $pattern
 * @return string
 * @throws Exception
 */
function df_sprintf($pattern) {
	/** @var string $result */
	/** @var mixed[] $arguments */
	if (is_array($pattern)) {
		$arguments = $pattern;
		$pattern = df_first($arguments);
	}
	else {
		$arguments = func_get_args();
	}
	try {
		$result = df_sprintf_strict($arguments);
	}
	catch (Exception $e) {
		/** @var bool $inProcess */
		static $inProcess = false;
		if (!$inProcess) {
			$inProcess = true;
			//df_notify_me(df_ets($e));
			$inProcess = false;
		}
		$result = $pattern;
	}
	return $result;
}

/**
 * @param string|mixed[] $pattern
 * @return string
 * @throws \Exception
 */
function df_sprintf_strict($pattern) {
	/** @var mixed[] $arguments */
	if (is_array($pattern)) {
		$arguments = $pattern;
		$pattern = df_first($arguments);
	}
	else {
		$arguments = func_get_args();
	}
	/** @var string $result */
	if (1 === count($arguments)) {
		$result = $pattern;
	}
	else {
		try {
			$result = vsprintf($pattern, df_tail($arguments));
		}
		catch (Exception $e) {
			/** @var bool $inProcess */
			static $inProcess = false;
			if (!$inProcess) {
				$inProcess = true;
				df_error(
					'При выполнении sprintf произошёл сбой «{message}».'
					. "\nШаблон: {$pattern}."
					. "\nПараметры:\n{params}."
					,[
						'{message}' => df_ets($e)
						,'{params}' => print_r(df_tail($arguments), true)
					]
				);
				$inProcess = false;
			}
		}
	}
	return $result;
}

/**
 * 2016-03-09 Замещает переменные в тексте.
 * @used-by df_file_name()
 * 2016-08-07 егодня разработал аналогичные функции для JavaScript: df.string.template() и df.t()
 * @param string $s
 * @param array(string => string) $variables
 * @param string|callable|null $onUnknown
 * @return string
 */
function df_var($s, array $variables, $onUnknown = null) {return preg_replace_callback(
	'#\{([^\}]*)\}#ui', function($m) use($variables, $onUnknown) {return
		dfa($variables, dfa($m, 1, ''), $onUnknown)
	;}, $s
);}