<?php
use Df\Core\Exception as DFE;

// Глобальные константы появились в PHP 5.3.
// http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html#post1363452
const DF_AFTER = 1;
const DF_BEFORE = -1;

/**
 * 2015-02-11
 * Эта функция аналогична @see array_map(), но обладает 3-мя дополнительными возможностями:
 * 1) её можно применять не только к массивам, но и к @see \Traversable.
 * 2) она позволяет удобным способом передавать в $callback дополнительные параметры
 * 3) позволяет передавать в $callback ключи массива
 * до и после основного параметра (элемента массива).
 * 4) позволяет в результате использовать нестандартные ключи
 * Обратите внимание, что
 *		df_map('Df_Cms_Model_ContentsMenu_Applicator::i', $this->getCmsRootNodes())
 * эквивалентно
 *		$this->getCmsRootNodes()->walk('Df_Cms_Model_ContentsMenu_Applicator::i')
 * @used-by df_mail()
 * @used-by ikf_api_oi()
 * @used-by vendor/mage2pro/color/view/frontend/templates/index.phtml
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition [optional]
 * @param bool $returnKey [optional]
 * @return array(int|string => mixed)
 * @throws DFE
 */
function df_map($a1, $a2, $pAppend = [], $pPrepend = [], $keyPosition = 0, $returnKey = false) {
	/** @var callable $callback */
	/** @var array(int|string => mixed)|\Traversable $array */
	list($array, $callback) = dfaf($a1, $a2);
	df_assert_callable($callback);
	$array = df_ita(df_assert_traversable($array));
	/** @var array(int|string => mixed) $result */
	if (!$pAppend && !$pPrepend && 0 === $keyPosition && !$returnKey) {
		$result = array_map($callback, $array);
	}
	else {
		$pAppend = df_array($pAppend);
		$pPrepend = df_array($pPrepend);
		$result = [];
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
			/** @var mixed[] $arguments */
			$arguments = array_merge($pPrepend, $primaryArgument, $pAppend);
			/** @var mixed $item */
			$item = call_user_func_array($callback, $arguments);
			if (!$returnKey) {
				$result[$key] = $item;
			}
			else {
				$result[$item[0]] = $item[1]; // 2016-10-25 It allows to return custom keys.
			}
		}
	}
	return $result;
}

/**
 * 2016-08-09 Функция принимает аргументы в любом порядке.
 * @used-by df_format_kv()
 * @used-by df_format_kv_table()
 * @used-by dfe_modules_log()
 * @used-by vendor/mage2pro/color/view/frontend/templates/index.phtml
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 */
function df_map_k($a1, $a2) {return df_map($a1, $a2, [], [], DF_BEFORE);}

/**
 * 2016-11-08
 * Функция принимает аргументы в любом порядке.
 * @see dfa_key_transform()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 * @throws DFE
 */
function df_map_kr($a1, $a2) {return df_map($a1, $a2, [], [], DF_BEFORE, true);}

/**
 * 2016-11-08
 * Функция принимает аргументы в любом порядке.
 * @used-by df_modules_my()
 * @used-by df_parse_colon()
 * @used-by dfe_packages()
 * @used-by dfe_portal_stripe_customers()
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFrom3To2()
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFromCodeToName()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 */
function df_map_r($a1, $a2) {return df_map($a1, $a2, [], [], 0, true);}