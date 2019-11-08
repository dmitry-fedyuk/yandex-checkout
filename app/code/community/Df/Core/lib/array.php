<?php
use Df\Core\A;
use Df\Core\Exception as DFE;
use Varien_Object as _DO;

/**
 * @param mixed|mixed[] $v
 * @return mixed[]|string[]|float[]|int[]
 */
function df_array($v) {return is_array($v) ? $v : [$v];}

/**
 * 2015-02-11
 * Аналог @see array_column() для коллекций.
 * Ещё один аналог: @see \Varien_Data_Collection::getColumnValues(),
 * но его результат — не ассоциативный.
 * 2016-07-31 При вызове с 2-мя параметрами эта функция идентична функции @see df_each()
 * 2017-07-09
 * Now the function accepts an array as $object.
 * Even in this case it differs from @see array_column():
 * array_column() misses the keys: https://3v4l.org/llMrL
 * df_column() preserves the keys.
 * @used-by df_index()
 * @param \Traversable|array(int|string => _DO|array(string => mixed)) $c
 * @param string|\Closure $fv
 * @param string|null $fk [optional]
 * @return array(int|string => mixed)
 */
function df_column($c, $fv, $fk = null) {return df_map_kr($c, function($k, $v) use($fv, $fk) {return [
	!$fk ? $k : df_call($v, $fk), df_call($v, $fv)
];});}

/**
 * 2015-02-07
 * Эта функция аналогична методу @see \Varien_Data_Collection::walk(),
 * и даже может использоваться вместо @see \Varien_Data_Collection::walk(),
 * однако, в отличие от @see \Varien_Data_Collection::walk(),
 * она способна работать не только с коллекцией,
 * но также с массивом объектов и объектом, поддерживающим интерфейс @see \Traversable.
 *
 * 2016-07-31
 * При вызове с 2-мя параметрами эта функция идентична функции @see df_column()
 *
 * 2017-07-09
 * Now the function accepts an array as $object.
 * Even in this case it differs from @see array_column():
 * array_column() misses the keys: https://3v4l.org/llMrL
 * df_column() preserves the keys.
 *
 * @used-by dfa_ids()
 * @used-by \Df\Core\GlobalSingletonDestructor::process()
 * @used-by \Df\Qa\Context::render()
 * @param \Traversable|array(int|string => _DO|array(string => mixed)) $c
 * @param string|callable $f
 * @param mixed ...$p
 * @return mixed[]
 */
function df_each($c, $f, ...$p) {return df_map(function($v) use($f, $p) {return df_call($v, $f, $p);}, $c);}

/**
 * 2015-02-18
 * По смыслу функция @see df_extend() аналогична методу @see \Varien_Simplexml_Element::extend()
 * и предназначена для слияния настроечных опций,
 * только, в отличие от @see \Varien_Simplexml_Element::extend(),
 * @see df_extend() сливает не XML, а ассоциативные массивы.
 *
 * Обратите внимание, что вместо @see df_extend() нельзя использовать ни
 * @see array_replace_recursive(), ни @see array_merge_recursive(),
 * ни тем более @see array_replace() и @see array_merge()
 * Нерекурсивные аналоги отметаются сразу, потому что не способны сливать вложенные структуры.
 * Но и стандартные рекурсивные функции тоже не подходят:
 *
 * 1)
 * array_merge_recursive(array('width' => 180), array('width' => 200))
 * вернёт: array(array('width' => array(180, 200)))
 * http://php.net/manual/function.array-merge-recursive.php
 * Наша функция df_extend(array('width' => 180), array('width' => 200))
 * вернёт array('width' => 200)
 *
 * 2)
 * array_replace_recursive(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт: array('x' => array('С', 'B'))
 * http://php.net/manual/function.array-replace-recursive.php
 * Наша функция df_extend(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт array('x' => 'C')
 *
 * 2018-11-13
 * 1) df_extend(
 *		['TBCBank' => ['1111' => ['a' => 'b']]]
 *		,['TBCBank' => ['2222' => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => ['1111' => ['a' => 'b'], '2222' => ['c' => 'd']]
 * 2) df_extend(
 *		['TBCBank' => [1111 => ['a' => 'b']]]
 *		,['TBCBank' => [2222 => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => [1111 => ['a' => 'b'], 2222 => ['c' => 'd']]
 *
 * @used-by df_ci_add()
 * @used-by df_oi_add()
 * @param array(string => mixed) $defaults
 * @param array(string => mixed) $newValues
 * @return array(string => mixed)
 * @throws DFE
 */
function df_extend(array $defaults, array $newValues) {
	/** @var array(string => mixed) $result */
	// Здесь ошибочно было бы $result = [],
	// потому что если ключ отсутствует в $newValues,
	// то тогда он не попадёт в $result.
	$result = $defaults;
	foreach ($newValues as $key => $newValue) {
		/** @var int|string $key */ /** @var mixed $newValue */ /** @var mixed $defaultValue */
		$defaultValue = dfa($defaults, $key);
		if (!is_array($defaultValue)) {
			// 2016-08-23 unset добавил сегодня.
			if (is_null($newValue)) {
				unset($result[$key]);
			}
			else {
				$result[$key] = $newValue;
			}
		}
		else {
			if (is_array($newValue)) {
				$result[$key] = df_extend($defaultValue, $newValue);
			}
			else {
				if (is_null($newValue)) {
					unset($result[$key]);
				}
				else {
					// Если значение по умолчанию является массивом,
					// а новое значение не является массивом,
					// то это наверняка говорит об ошибке программиста.
					df_error(
						"df_extend: значением по умолчанию ключа «{$key}» является массив {defaultValue},"
						. "\nоднако программист ошибочно пытается заместить его"
						. ' значением {newValue} типа «{newType}», что недопустимо:'
						. "\nзамещаемое значение для массива должно быть либо массивом, либо «null»."
						,[
							'{defaultValue}' => df_t()->singleLine(df_dump($defaultValue))
							,'{newType}' => gettype($newValue)
							,'{newValue}' => df_dump($newValue)
						]
					);
				}
			}
		}
	}
	return $result;
}

/**
 * 2016-11-08
 * Отличия этой функции от @uses array_filter():
 * 1) работает не только с массивами, но и с @see \Traversable
 * 2) принимает аргументы в произвольном порядке.
 * Третий параметр — $flag — намеренно не реализовал,
 * потому что вроде бы для @see \Traversable он особого смысла не имеет,
 * а если у нас гарантирвоанно не @see \Traversable, а ассоциативный массив,
 * то мы можем использовать array_filter вместо df_filter.
 * @param callable|array(int|string => mixed)|array[]\Traversable $a
 * @param callable|array(int|string => mixed)|array[]|\Traversable $b
 * @return array(int|string => mixed)
 */
function df_filter($a, $b) {return array_filter(...(
	is_callable($a) ? [df_ita($b), $a] : [df_ita($a), $b]
));}

/**
 * 2016-10-25 Оказалось, что в ядре нет такой функции.
 * @used-by df_handle_prefix()
 * @used-by df_oq_sa()
 * @used-by df_sales_email_sending()
 * @used-by ikf_oi_pid()
 * @param callable|array(int|string => mixed)|array[]|mixed|\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|mixed|\Traversable $a2
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition [optional]
 * @return mixed|null
 * @throws DFE
 */
function df_find($a1, $a2, $pAppend = [], $pPrepend = [], $keyPosition = 0) {
	/** @var callable $callback */  /** @var array(int|string => mixed)|\Traversable $array */
	list($array, $callback) = dfaf($a1, $a2);
	df_assert_callable($callback);
	$array = df_ita(df_assert_traversable($array));
	$pAppend = df_array($pAppend);
	$pPrepend = df_array($pPrepend);
	$result = null; /** @var mixed|null $result */
	foreach ($array as $key => $item) {
		/** @var int|string $key */
		/** @var mixed $item */
		/** @var mixed[] $primaryArgument */
		switch ($keyPosition) {
			case DF_BEFORE:
				$primaryArgument = [$key, $item];
				break;
			case DF_AFTER:
				$primaryArgument = [$item, $key];
				break;
			default:
				$primaryArgument = [$item];
		}
		if ($r = call_user_func_array($callback, array_merge($pPrepend, $primaryArgument, $pAppend))) {
			$result = !is_bool($r) ? $r : $item;
			break;
		}
	}
	return $result;
}

/**
 * Функция возвращает null, если массив пуст.
 * Обратите внимание, что неверен код
 *	$result = reset($a);
 *	return (false === $result) ? null : $result;
 * потому что если @uses reset() вернуло false, это не всегда означает сбой метода:
 * ведь первый элемент массива может быть равен false.
 * @see df_last()
 * @see df_tail()
 * @used-by dfa_group()
 * @used-by dfe_alphacommercehub_fix_amount_bug()
 * @used-by frugue/core/view/frontend/templates/wishlist/item/column/image.phtml
 * @param array $a
 * @return mixed|null
 */
function df_first(array $a) {return !$a ? null : reset($a);}

/**
 * 2019-08-21 https://www.php.net/manual/en/function.array-key-first.php
 * @param array(int|string => mixed) $a
 * @return string|int|null
 */
function df_first_key(array $a) {
	$r = null; /** @var int|string|null $r */
	foreach($a as $k => $v) { /** @var int|string $k */
		$r = $k;
		break;
	}
	return $r;
}

/**
 * 2015-03-13
 * Отсекает последний элемент массива и возвращает «голову» (массив оставшихся элементов).
 * Похожая системная функция @see array_pop() возвращает отсечённый последний элемент.
 * Противоположная системная функция @see df_tail() отсекает первый элемент массива.
 * @param mixed[] $a
 * @return mixed[]|string[]
 */
function df_head(array $a) {return array_slice($a, 0, -1);}

/**
 * 2015-12-30 Преобразует коллекцию или массив в карту.
 * @param string|\Closure $k
 * @param \Traversable|array(int|string => _DO) $items
 * @return mixed[]
 */
function df_index($k, $a) {return array_combine(df_column($a, $k), $a);}

/**
 * 2015-02-11
 * Эта функция отличается от @see iterator_to_array() тем, что допускает в качестве параметра
 * не только @see \Traversable, но и массив.
 * @param \Traversable|array $t
 * @return array
 */
function df_ita($t) {return is_array($t) ? $t : iterator_to_array($t);}

/**
 * Оба входных массива должны быть ассоциативными
 * @param array(string => mixed) $array1
 * @param array(string => mixed) $array2
 * @return array(string => mixed)
 */
function df_merge_not_empty(array $array1, array $array2) {return array_filter($array2) + $array1;}

/**
 * 2015-02-11
 * Эта функция отличается от @see array_merge() только тем,
 * что все вместо нескольких параметров принимает массив из параметров.
 * Это бывает удобно в функциональном программировании, например:
 * @used-by Df_Dataflow_Model_Registry_MultiCollection::getEntities()
 * @used-by Df_Dellin_Model_Request_Rate::getDates()
 * @param array(array(int|string => mixed)) $arrays
 * @return array(int|string => mixed)
 */
function df_merge_single(array $arrays) {return array_merge(...$arrays); }

/**
 * Функция возвращает null, если массив пуст.
 * Если использовать @see end() вместо @see df_last(),
 * то указатель массива после вызова end сместится к последнему элементу.
 * При использовании @see df_last() смещения указателя не происходит,
 * потому что в @see df_last() попадает лишь копия массива.
 *
 * Обратите внимание, что неверен код
 *	$result = end($array);
 *	return (false === $result) ? null : $result;
 * потому что если @uses end() вернуло false, это не всегда означает сбой метода:
 * ведь последний элемент массива может быть равен false.
 * http://www.php.net/manual/en/function.end.php#107733
 * @see df_first()
 * @see df_tail()
 * @used-by df_class_l()
 * @used-by df_fe_name_short()
 * @used-by df_package_name_l()
 * @used-by df_url_path()
 * @used-by df_url_staged()
 * @used-by ikf_eti()
 * @used-by \Df\Core\O::_prop()
 * @used-by \Df\Core\State::block()
 * @used-by \Df\Core\State::component()
 * @used-by \Df\Core\State::templateFile()
 * @used-by \Df\Core\Text\Regex::match()
 * @param mixed[] $array
 * @return mixed|null
 */
function df_last(array $array) {return !$array ? null : end($array);}

/**
 * @used-by Df_InTime_Api::call()
 * http://stackoverflow.com/a/18576902
 * @param mixed $value
 * @return array
 * @throws DFE
 */
function df_stdclass_to_array($value) {return df_json_decode(json_encode($value));}

/**
 * Отсекает первый элемент массива и возвращает хвост (аналог CDR в Lisp).
 * Обратите внимание, что если исходный массив содержит меньше 2 элементов,
 * то функция вернёт пустой массив.
 * @see df_first()
 * @see df_last()
 * @param mixed[] $a
 * @return mixed[]|string[]
 */
function df_tail(array $a) {return array_slice($a, 1);}

/**
 * http://en.wikipedia.org/wiki/Tuple
 * @param array $arrays
 * @return array
 */
function df_tuple(array $arrays) {
	/** @var array $result */
	$result = [];
	/** @var int $count */
	$countItems = max(array_map('count', $arrays));
	for ($ordering = 0; $ordering < $countItems; $ordering++) {
		/** @var array $item */
		$item = [];
		foreach ($arrays as $arrayName => $array) {
			$item[$arrayName]= dfa($array, $ordering);
		}
		$result[$ordering] = $item;
	}
	return $result;
}

/**
 * Раньше функция @see dfa() была универсальной:
 * она принимала в качестве аргумента $entity как массивы, так и объекты.
 * В 99.9% случаев в качестве параметра передавался массив.
 * Поэтому ради ускорения работы системы
 * вынес обработку объектов в отдельную функцию @see dfo()
 * @param array(int|string => mixed) $a
 * @param string|string[]|int $k
 * @param mixed|callable $d
 * @return mixed|null|array(string => mixed)
 */
function dfa(array $a, $k, $d = null) {return
	// 2016-02-13
	// Нельзя здесь писать return df_if2(isset($array[$k]), $array[$k], $d);
	// потому что получим «Notice: Undefined index».
	// 2016-08-07
	// В \Closure мы можем безнаказанно передавать параметры,
	// даже если closure их не поддерживает https://3v4l.org/9Sf7n
	is_array($k) ? dfa_select_ordered($a, $k) : (isset($a[$k]) ? $a[$k] : df_call_if($d, $k))
;}

/**
 * 2017-02-18
 * [array|callable, array|callable] => [array, callable]
 * @used-by df_find()
 * @used-by dfa_key_transform()
 * @used-by df_map()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a
 * @param null|callable|array(int|string => mixed)|array[]|\Traversable $b [optional]
 * @return array(int|string => mixed)
 */
function dfaf($a, $b) {return is_callable($a) ? [$b, $a] : [$a, $b];}

/**
 * 2017-01-01
 * 2017-01-02
 * 1) Если второй параметр — Closure, то первый должен быть объектом.
 * 2) Возможны ситуации, когда Closure — первый параметр:
 * так происходит при вызове dfak() из статического метода: @used-by \Df\Framework\Request::clean()
 * 3) Возможны ситуации, когда первый параметр — объект типа @see \Varien_Object
 * В таком случае мы трактуем этот объект как массив, а не как носитель кэша.
 * 2017-07-13
 * Эта функция может получать 2 или 3 параметра.
 * Третий параметр — это результат функции по умолчанию, этот параметр опционален.
 * Первые два параметра — это:
 * a) некий контейнер (массив или объект)
 * b) accessor: некий ключ доступа к полю контейнера (строка или Closure).
 * Эти параметры могут быть переданы в произвольном порядке относительно друг друга
 * (но третий параметр всегда должен быть в конце.)
 * @used-by df_ci_get()
 * @used-by df_credentials()
 * @used-by df_fe_fc()
 * @used-by df_oi_get()
 * @used-by df_package()
 * @used-by df_trd()
 * @param mixed[] ...$args
 * @return _DO|array(string => mixed)|mixed|null
 * @throws DFE
 */
function dfak(...$args) {
	/** @var object $o */
	if ($args[1] instanceof \Closure) {
		$o = array_shift($args);
		df_assert(is_object($o));
	}
	/** @var \Closure|_DO|array(string => mixed $a */
	$a = array_shift($args);
	$a = !$a instanceof \Closure ? $a : (isset($o) ? dfc($o, $a, [], false, 1) : dfcf($a, [], [], false, 1));
	/** @var string|string[]|null $k */
	$k = dfa($args, 0);
	/** @var _DO|array(string => mixed)|mixed|null $result */
	if (is_null($k)) {
		$result = $a;
	}
	else {
		if ($a instanceof _DO) {
			$a = $a->getData();
		}
		$result = is_array($k) ? dfa($a, $k) : dfa_deep($a, $k, dfa($args, 1));
	}
	return $result;
}

/**
 * 2016-08-21
 * @param mixed[] $a
 * @return A
 */
function dfao(array $a) {return new A($a);}

/**
 * 2019-01-28
 * @param array(int|string => mixed) $a
 * @param string[] $k
 * @param mixed|null $d [optional]
 * @return mixed|null
 */
function dfa_seq(array $a, array $k, $d = null) {
	$r = null; /** @var @var mixed|null $r */
	foreach ($k as $ki) { /** @var string $ki */
		$r = dfa($a, $ki);
		if (!is_null($r)) {
			break;
		}
	}
	return is_null($r) ? $d : $r;
}

/**
 * 2018-04-24
 * @param array(int|string => mixed) $a
 * @param string|int $k
 * @return array(int|string => array(int|string => mixed))
 */
function dfa_group(array $a, $k) {
	$r = []; /** @var array(int|string => array(int|string => mixed)) $r */
	$isInt = is_int($k); /** @var bool $isInt */
	foreach ($a as $v) { /** @var mixed $v */
		$index = $v[$k]; /** @var string $index */
		if (!isset($r[$index])) {
			$r[$index] = [];
		}
		unset($v[$k]);
		$r[$index][] = 1 === count($v) ? df_first($v) : (!$isInt ? $v : array_values($v));
	}
	return $r;
}

/**
 * 2015-02-07
 * Аналог @see array_change_key_case() с поддержкой UTF-8.
 * Реализацию взял отсюда: http://php.net/manual/function.array-change-key-case.php#107715
 * Обратите внимание, что @see array_change_key_case() некорректно работает с UTF-8.
 * Например:
		$countries = array('Россия' => 'RU', 'Украина' => 'UA', 'Казахстан' => 'KZ');
	array_change_key_case($countries, CASE_UPPER)
 * вернёт:
	(
		[РнссШя] => RU
		[УЪраШна] => UA
		[Њазахстан] => KZ
	)
 *
 * 2017-02-01
 * Отныне стал использовать константы MB_CASE_LOWER и MB_CASE_UPPER вместо CASE_LOWER и CASE_UPPER.
 * Обратите внимание, что они имеют противоположные значения:
 * CASE_LOWER = 0, а MB_CASE_LOWER = 1
 * CASE_UPPER = 1, а MB_CASE_UPPER = 0.
 *
 * @used-by dfa_key_lc()
 * @used-by dfa_key_uc()
 * @param array(string => mixed) $a
 * @param int $c
 * @return array(string => mixed)
 */
function dfa_key_case(array $a, $c) {return dfa_key_transform($a, function($k) use($c) {return
	mb_convert_case($k, $c, 'UTF-8')
;});}

/**
 * 2017-09-03
 * @uses df_int()
 * @see df_int_simple()
 * @param array(int|string => mixed) $a
 * @return array(int => mixed)
 */
function dfa_key_int(array $a) {return dfa_key_transform($a, 'df_int');}

/**
 * 2017-02-01
 * @param array(string => mixed) $a
 * @return array(string => mixed)
 */
function dfa_key_lc(array $a) {return dfa_key_case($a, MB_CASE_LOWER);}

/**
 * 2017-02-01 Функция принимает аргументы в любом порядке.
 * @see df_map_kr()
 * @used-by df_headers()
 * @used-by dfa_key_case()
 * @used-by dfa_key_int()
 * @used-by \Df\Framework\Request::extra()
 * @used-by \Df\Sentry\Client::tags_context()
 * @used-by \Df\Sentry\Extra::adjust()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 * @throws DFE
 */
function dfa_key_transform($a1, $a2) {
	/** @var callable $f */
	/** @var array(int|string => mixed)|\Traversable $a */
	list($a, $f) = dfaf($a1, $a2);
	df_assert_callable($f);
	$a = df_ita(df_assert_traversable($a));
	return array_combine(array_map($f, array_keys($a)), array_values($a));
}

/**
 * @param array(string => mixed) $a
 * @return array(string => mixed)
 */
function dfa_key_uc(array $a) {return dfa_key_case($a, MB_CASE_UPPER);}

/**
 * 2016-09-07
 * 2017-03-06
 * @uses mb_substr() корректно работает с $length = null
 * @param string[] $a
 * @param int|null $length
 * @return string[]
 */
function dfa_chop(array $a, $length) {return df_map('mb_substr', $a, [0, $length]);}

/**               
 * 2016-11-25
 * @used-by \Df\Core\Validator::byName()
 * @used-by df_a_to_options()
 * @param string[]|int[] ...$a
 * @return array(int|string => int|string)
 */
function dfa_combine_self(...$a) {$a = df_args($a); return array_combine($a, $a);}

/**
 * Эта функция отличается от @uses array_fill() только тем,
 * что разрешает параметру $length быть равным нулю.
 * Если $length = 0, то функция возвращает пустой массив.
 * @uses array_fill() разрешает параметру $num (аналог $length)
 * быть равным нулю только начиная с PHP 5.6:
 * http://php.net/manual/function.array-fill.php
 * «5.6.0	num may now be zero. Previously, num was required to be greater than zero»
 * @param int $startIndex
 * @param int $length
 * @param mixed $value
 * @return mixed[]
 */
function dfa_fill($startIndex, $length, $value) {return !$length ? [] : 
	array_fill($startIndex, $length, $value)
;}

/**
 * 2016-03-25 http://stackoverflow.com/a/1320156
 * @used-by df_cc_class()
 * @used-by df_cc_class_uc()
 * @used-by df_mail()
 * @param array $a
 * @return mixed[]
 */
function dfa_flatten(array $a) {
	$r = []; /** @var mixed[] $r */
	array_walk_recursive($a, function($a) use(&$r) {$r[]= $a;});
	return $r;
}

/**
 * 2016-07-31
 * @param \Traversable|array(int|string => _DO) $collection
 * @return int[]|string[]
 */
function dfa_ids($collection) {return df_each($collection, 'getId');}

/**
 * 2016-08-26
 * Вставляет новые элементы внутрь массива.
 * http://php.net/manual/function.array-splice.php
 * Если нужно вставить только один элемент, то необязательно обрамлять его в массив.
 * 2016-11-23
 * Достоинство этой функции перед @uses array_splice()
 * ещё и в отсутствии требования передачи первого параметра по ссылке.
 * 2016-11-24
 * Отныне функция правильно работает с ассоциативными массивами.
 * @param mixed[] $a
 * @param int $pos
 * @param mixed|mixed[] $add
 * @return mixed[]
 */
function dfa_insert(array $a, $pos, $add) {
	if (!is_array($add) || !df_is_assoc($add)) {
		array_splice($a, $pos, 0, $add);
	}
	else {
		/**
		 * 2016-11-24
		 * Отныне функция правильно работает с ассоциативными массивами.
		 * http://stackoverflow.com/a/1783125
		 */
		$a = array_slice($a, 0, $pos, true) + $add + array_slice($a, $pos, null, true);
	}
	return $a;
}

/**
 * 2017-10-28
 * Plain `array_merge($r, $b)` works wronly,
 * if $b contains contains SOME numeric-string keys like "99":
 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
 * https://stackoverflow.com/a/5929671
 * @used-by dfa_select_ordered()
 * @param array(string|int => mixed) $r
 * @param array(string|int => mixed) $b
 * @return array(string|int => mixed)
 */
function dfa_merge_numeric(array $r, array $b) {
	foreach ($b as $k => $v) {
		$r[$k] = $v;
	}
	return $r;
}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
 *		$source = array(
 *			'RU' => 'Россия', 'KZ' => 'Казахстан', 'TJ' => 'Таджикистан','US' => 'США','CA' => 'Канада'
 *		);
 *		$priorityKeys = array('TJ', 'CA');
 *		print_r(dfa_prepend_by_keys($source, $priorityKeys));
 * Вернёт:
 *	 Array
 *	 (
 *		 [TJ] => Таджикистан
 *		 [CA] => Канада
 *		 [RU] => Россия
 *		 [KZ] => Казахстан
 *		 [US] => США
 *	 )
 * http://3v4l.org/QYffO
 * Обратите внимание, что @uses array_flip() корректно работает с пустыми массивами:
 *	print_r(array_flip([]));
 * вернёт array
 * http://3v4l.org/Kd01X
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * @param array(string => mixed) $source
 * @param string[] $priorityKeys
 * @return array(string => mixed)
 */
function dfa_prepend_by_keys(array $source, array $priorityKeys) {return
	dfa_select_ordered($source, $priorityKeys) + $source
;}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
 *		$source = array(
 *			'Россия' => 'RU'
 *			,'Казахстан' => 'KZ'
 *			,'Таджикистан' => 'TJ'
 *			,'США' => 'US'
 *			,'Канада' => 'CA'
 *		);
 *		$priorityValues = array('TJ', 'CA');
 *		print_r(dfa_prepend_by_values($source, $priorityValues));
 * вернёт:
 *		Array
 *		(
 *			[Таджикистан] => TJ
 *			[Канада] => CA
 *			[Россия] => RU
 *			[Казахстан] => KZ
 *			[США] => US
 *		)
 * http://3v4l.org/tNms4
 * @uses dfa_prepend_by_keys()
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * @param array(string => mixed) $source
 * @param string[] $priorityValues
 * @return array(string => mixed)
 */
function dfa_prepend_by_values(array $source, array $priorityValues) {return array_flip(
	dfa_prepend_by_keys(array_flip($source), $priorityValues)
);}

/**
 * 2016-07-31
 * Возвращает повторяющиеся элементы исходного массива (не повторяя их).
 * https://3v4l.org/YEf5r
 * В алгоритме пользуемся тем, что @uses array_unique() сохраняет ключи исходного массива.
 * @param array $a
 * @return array
 */
function dfa_repeated(array $a) {return array_values(array_unique(array_diff_key($a, array_unique($a))));}

/**
 * Работает в разы быстрее, чем @see array_unique()
 * «Just found that array_keys(array_flip($array)); is amazingly faster than array_unique();.
  * About 80% faster on 100 element array,
  * 95% faster on 1000 element array
  * and 99% faster on 10000+ element array.»
 * http://stackoverflow.com/questions/5036504/php-performance-question-faster-to-leave-duplicates-in-array-that-will-be-searc#comment19991540_5036538
 * http://www.php.net/manual/en/function.array-unique.php#70786
 * 2015-02-06
 * Обратите внимание, что т.к. алгоритм @see dfa_unique_fast() использует @uses array_flip(),
 * то @see dfa_unique_fast() можно применять только в тех ситуациях,
 * когда массив содержит только строки и целые числа,
 * иначе вызов @uses array_flip() завершится сбоем уровня E_WARNING:
 * «array_flip(): Can only flip STRING and INTEGER values»
 * http://magento-forum.ru/topic/4695/
 * В реальной практике сбой случается, например, когда массив содержит значение null:
 * http://3v4l.org/bat52
 * Пример кода, приводящего к сбою: dfa_unique_fast(array(1, 2, 2, 3, null))
 * В то же время, несмотря на E_WARNING, метод всё-таки возвращает результат,
 * правда, без недопустимых значений:
 * при подавлении E_WARNING dfa_unique_fast(array(1, 2, 2, 3, null)) вернёт:
 * array(1, 2, 3).
 * Более того, даже если сбойный элемент содержится в середине исходного массива,
 * то результат при подавлении сбоя E_WARNING будет корректным (без недопустимых элементов):
 * dfa_unique_fast(array(1, 2, null,  2, 3)) вернёт тот же результат array(1, 2, 3).
 * http://3v4l.org/uvJoI
 * По этой причине добавил оператор @ перед @uses array_flip()
 * @param array(int|string => int|string) $a
 * @return array(int|string => int|string)
 */
function dfa_unique_fast(array $a) {return
	/** @noinspection PhpUsageOfSilenceOperatorInspection */ array_keys(@array_flip($a))
;}

/**
 * 2016-09-02
 * @see dfa_deep_unset()
 * @uses array_flip() корректно работает с пустыми массивами.
 * @param array(string => mixed) $a
 * @param string[] ...$keys
 * @return array(string => mixed)
 */
function dfa_unset(array $a, ...$keys) {return array_diff_key($a, array_flip(df_args($keys)));}

/**
 * Алгоритм взят отсюда:
 * http://php.net/manual/function.array-unshift.php#106570
 * @param array(string => mixed) $a
 * @param string $k
 * @param mixed $v
 */
function dfa_unshift_assoc(&$a, $k, $v)  {
	$a = array_reverse($a, $preserve_keys = true);
	$a[$k] = $v;
	$a = array_reverse($a, $preserve_keys = true);
}

/**
 * 2016-09-05
 * @used-by df_cfg_save()
 * @used-by df_url_bp()
 * @param int|string $v
 * @param array(int|string => mixed) $map
 * @return int|string|mixed
 */
function dftr($v, array $map) {return dfa($map, $v, $v);}