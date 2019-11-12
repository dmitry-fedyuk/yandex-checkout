<?php
use Df\Payment\Method as M;
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Order_Creditmemo as CM;
use Mage_Sales_Model_Order_Invoice as I;
use Mage_Sales_Model_Quote as Q;
use Mage_Sales_Model_Resource_Order_Invoice_Collection as IC;
/**
 * 2019-11-11
 * @param M $m
 * @param O|Q|I|CM|null $d [optional]
 * @return float in the order/payment currency
 */
function dfp_due(M $m, $d = null) {
	$d = $d ?: ($m->ii()['creditmemo'] ?: $m->oq());
	// 2018-10-06 This code handles the backend partial capture of a preauthorized bank card payment.
	if (df_is_o($d)) {
		$ic = $d->getInvoiceCollection(); /** @var IC $ic */
		if ($ic->count()) {
			$i = $ic->getLastItem(); /** @var I $i */
			if (!$i->getId()) {
				$d = $i;
			}
		}
	}
	return dfcf(function(M $m, $d) {/**@var O|Q|I|CM $d */ return $m->cFromBase(
		df_is_o($d) ? $d->getBaseTotalDue() : (
			$d instanceof CM || $d instanceof I || df_is_q($d) ? $d->getBaseGrandTotal() : df_error(
				'Invalid document class: %s.', df_cts($d)
			)
		)
	);}, [$m, $d]);
}