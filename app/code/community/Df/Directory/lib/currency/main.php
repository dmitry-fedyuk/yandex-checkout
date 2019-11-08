<?php
use Df\Directory\Model\Country;
use Mage_Core_Model_Store as Store;
use Mage_Directory_Model_Currency as C;
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Quote as Q;
use NumberFormatter as NF;

/**
 * 2016-07-04
 * «How to load a currency by its ISO code?» https://mage2.pro/t/1840
 * @param C|string|null $c [optional]
 * @return C
 */
function df_currency($c = null) {/** @var C $r */
	if (!$c) {
		$r = df_currency_base();
	}
	elseif ($c instanceof C) {
		$r = $c;
	}
	else {
		static $cache; /** @var array(string => Currency) $cache */
		if (!isset($cache[$c])) {
			$cache[$c] = (new C)->load($c);
		}
		$r = $cache[$c];
	}
	return $r;
}

/**
 * 2016-07-04
 * «How to programmatically get the base currency's ISO code for a store?» https://mage2.pro/t/1841
 *
 * 2016-12-15
 * Добавил возможность передачи в качестве $scope массива из 2-х элементов: [Scope Type, Scope Code].
 * Это стало ответом на удаление из ядра класса \Magento\Framework\App\Config\ScopePool
 * в Magento CE 2.1.3: https://github.com/magento/magento2/commit/3660d012
 * @param Store|O|Q|array(int|string)|string|int|null $s [optional]
 * @return C
 */
function df_currency_base($s = null) {
	/** @noinspection PhpUndefinedMethodInspection */
	return df_currency(df_assert_sne(df_cfg(C::XML_PATH_CURRENCY_BASE, df_is_oq($s) ? $s->getStore() : $s)))
;}

/**
 * 2017-01-29
 * «How to get the currency code for a country with PHP?» https://mage2.pro/t/2552
 * http://stackoverflow.com/a/31755693
 * @param string|Country $c
 * @return string
 */
function df_currency_by_country_c($c) {return dfcf(function($c) {return
	(new NF(df_locale_by_country($c), NF::CURRENCY))->getTextAttribute(NF::CURRENCY_CODE)
;}, [df_currency_code($c)]);}

/**
 * 2016-08-08
 * http://magento.stackexchange.com/a/108013
 * В отличие от @see df_currency_base() здесь мы вынуждены использовать не $scope, а $store,
 * потому что учётную валюту можно просто считать из настроек,
 * а текущая валюта может меняться динамически (в том числе посетителем магазина и сессией).
 * @param int|string|null|bool|Store $s [optional]
 * @return C
 */
function df_currency_current($s = null) {return df_store($s)->getCurrentCurrency();}