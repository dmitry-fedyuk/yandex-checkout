<?php
use Mage_Core_Model_Store as Store;
use Mage_Directory_Model_Currency as C;

/**
 * 2016-07-04
 * «How to programmatically convert a money amount from a currency to another one?» https://mage2.pro/t/1842
 * 2016-09-05
 * Обратите внимание, что перевод из одной валюты в другую
 * надо осуществлять только в направлении 'базовая валюта' => 'второстепенная валюта',
 * но не наоборот
 * (Magento не умеет выполнять первод 'второстепенная валюта' => 'базовая валюта'
 * даже при наличии курса 'базовая валюта' => 'второстепенная валюта',
 * и возбуждает исключительную ситуацию).
 *
 * @param float $a
 * @param C|string|null $from [optional]
 * @param C|string|null $to [optional]
 * @param null|string|int|Store $s [optional]
 * @return float
 */
function df_currency_convert($a, $from = null, $to = null, $s = null) {return df_currency_convert_from_base(
	df_currency_convert_to_base($a, $from, $s), $to, $s
);}

/**
 * 2017-04-15
 * @param float $a
 * @param C|string|null $from [optional]
 * @param C|string|null $to [optional]
 * @param null|string|int|Store $s [optional]
 * @return float
 */
function df_currency_convert_safe($a, $from = null, $to = null, $s = null) {return df_try(
	function() use($a, $from, $to, $s) {return df_currency_convert($a, $from, $to, $s);}, $a
);}

/**
 * 2016-09-05
 * @param float $a
 * @param C|string|null $to
 * @param null|string|int|Store $s [optional]
 * @return float
 */
function df_currency_convert_from_base($a, $to, $s = null) {return df_currency_base($s)->convert($a, $to);}

/**
 * 2016-09-05
 * @used-by df_currency_convert()
 * @param float $a
 * @param C|string|null $from
 * @param null|string|int|Store $s [optional]
 * @return float
 */
function df_currency_convert_to_base($a, $from, $s = null) {return $a / df_currency_base($s)->convert(1, $from);}

/**
 * 2016-06-30
 * «How to programmatically check whether a currency is allowed
 * and has an exchange rate to the base currency?» https://mage2.pro/t/1832
 * @param string $iso3
 * @param int|string|null|bool|Store $s [optional]
 * @return string[]
 */
function df_currency_has_rate($iso3, $s = null) {return !!dfa(df_currencies_ctn($s), $iso3);}

/**
 * 2016-08-08
 * @return float
 */
function df_currency_rate_to_current() {return df_currency_base()->getRate(df_currency_current());}