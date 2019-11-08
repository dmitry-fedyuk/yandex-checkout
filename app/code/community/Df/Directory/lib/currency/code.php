<?php
use Mage_Core_Model_Store as Store;
use Mage_Directory_Model_Currency as C;

/**
 * 2015-12-28
 * @param int|string|null|bool|Store $s [optional]
 * @return string[]
 */
function df_currencies_codes_allowed($s = null) {return df_store($s)->getAvailableCurrencyCodes(true);}

/**
 * 2016-09-05
 * @param null|string|int|Store $s [optional]
 * @return string
 */
function df_currency_base_c($s = null) {return df_currency_base($s)->getCode();}

/**
 * 2016-07-04       
 * @used-by df_currency_by_country_c()
 * @used-by df_currency_name()
 * @used-by df_currency_num()   
 * @param C|string|null $c [optional]
 * @return string
 */
function df_currency_code($c = null) {return df_currency($c)->getCode();}

/**
 * 2016-09-05
 * В отличие от @see df_currency_base_с() здесь мы вынуждены использовать не $scope, а $store,
 * потому что учётную валюту можно просто считать из настроек,
 * а текущая валюта может меняться динамически (в том числе посетителем магазина и сессией).
 * @param int|string|null|bool|Store $s [optional]
 * @return string
 */
function df_currency_current_c($s = null) {return df_currency_current($s)->getCode();}

/**
 * 2018-09-26
 * It returns the currency's numeric ISO 4217 code:
 * https://en.wikipedia.org/wiki/ISO_4217#Active_codes
 * I use the database from the `sokil/php-isocodes` library:
 * https://github.com/sokil/php-isocodes/blob/8cd8c1f0/databases/iso_4217.json
 * @param string|C|string[]|C[]|null $c
 * @return string
 */
function df_currency_num($c = null) {return dfa(df_currency_nums(), df_currency_code($c));}

/**
 * 2018-09-26  
 * @used-by df_currency_num()
 * @return array(string => string)
 */
function df_currency_nums() {return dfcf(function() {return array_column(
	df_module_json('Df_Directory', 'iso4217'), 'numeric', 'alpha'
);});}