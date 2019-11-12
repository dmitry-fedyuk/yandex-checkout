<?php
use Mage_Sales_Model_Quote as Q;

/**
 * 2016-07-18
 * @see df_order()
 * @used-by \Df\Payment\ConfigProvider::config()
 * @used-by \Df\Payment\Method::getInfoInstance()
 * @used-by \Df\Payment\Settings::applicableForQuoteByCountry()
 * @used-by \Df\Payment\Settings::applicableForQuoteByMinMaxTotal()
 * @param Q|int|null $q [optional]
 * @return Q
 */
function df_quote($q = null) {return $q instanceof Q ? $q : (
	$q ? df_load(Q::class, $q) : df_checkout_session()->getQuote()
);}