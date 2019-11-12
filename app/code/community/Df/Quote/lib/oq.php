<?php
use Df\Core\Exception as DFE;
use Mage_Customer_Model_Customer as C;
use Mage_Payment_Model_Info as II;
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Order_Address as OA;
use Mage_Sales_Model_Order_Item as OI;
use Mage_Sales_Model_Order_Payment as OP;
use Mage_Sales_Model_Quote as Q;
use Mage_Sales_Model_Quote_Address as QA;
use Mage_Sales_Model_Quote_Item as QI;
use Mage_Sales_Model_Quote_Payment as QP;

/**
 * 2017-04-10
 * @used-by df_is_oq()
 * @used-by df_oq_currency_c ()
 * @used-by df_oq_customer_name()
 * @used-by df_oq_sa()
 * @used-by df_order()
 * @used-by df_visitor()
 * @used-by dfp_due()
 * @param mixed $v
 * @return bool
 */
function df_is_o($v) {return $v instanceof O;}

/**
 * 2017-04-20
 * @used-by df_oi()
 * @used-by df_product()
 * @param mixed $v
 * @return bool
 */
function df_is_oi($v) {return $v instanceof OI;}

/**
 * 2017-04-08        
 * @used-by df_currency_base()
 * @used-by df_oq()  
 * @used-by dfp()
 * @used-by dfpex_args()
 * @used-by dfpm()
 * @param mixed $v
 * @return bool
 */
function df_is_oq($v) {return df_is_o($v) || df_is_q($v);}

/**
 * 2017-04-10
 * @used-by df_is_oq()
 * @used-by df_oq_currency_c()
 * @used-by df_oq_sa()
 * @used-by dfp_due()
 * @param mixed $v
 * @return bool
 */
function df_is_q($v) {return $v instanceof Q;}

/**
 * 2017-04-20
 * @param mixed $v
 * @return bool
 */
function df_is_qi($v) {return $v instanceof QI;}

/**
 * 2017-03-12
 * @used-by dfpex_args()
 * @param O|Q $oq
 * @return O|Q
 */
function df_oq($oq) {return df_is_oq($oq) ? $oq : df_error();}

/**
 * 2016-11-15
 * @param O|Q $oq
 * @return string
 * @throws DFE
 */
function df_oq_currency_c($oq) {return df_is_o($oq) ? $oq->getOrderCurrencyCode() : (
	df_is_q($oq) ? $oq->getQuoteCurrencyCode() : df_error(
		'df_oq_currency_c(): an order or quote is required, but got a value of the type «%s».', gettype($oq)
	)
);}

/**
 * 2016-03-09
 * @param O|Q $oq
 * @return string
 */
function df_oq_customer_name($oq) {return dfcf(function($oq) {
	/** @var O|Q $oq */ /** @var string $r */
	// 2017-04-10
	// До завершения оформления заказа гостем quote не содержит имени покупателя,
	// даже если привязанные к quote адреса billing и shipping это имя содержат.
	$r = df_cc_s(array_filter([
		$oq->getCustomerFirstname(), $oq->getCustomerMiddlename(), $oq->getCustomerLastname()
	]));
	/** @var C $c */
	if (!$r && ($c = $oq->getCustomer())) {
		$r = $c->getName();
	}
	/** @var OA|QA|null $ba */
	if (!$r && ($ba = $oq->getBillingAddress())) {
		$r = $ba->getName();
	}
	/** @var OA|QA|null $ba */
	if (!$r && ($sa = $oq->getShippingAddress())) {
		$r = $sa->getName();
	}
	// 2016-08-24
	// Имени в адресах может запросто не быть
	// (например, если покупатель заказывает цифровой товар и requireBillingAddress = false),
	// и вот тогда мы попадаем сюда.
	// В данном случае функция вернёт просто «Guest».
	return $r ?: (df_is_o($oq) ? $oq->getCustomerName() : (string)__('Guest'));
}, [$oq]);}

/**
 * 2018-11-14
 * @param O|Q $oq
 * @return string
 */
function df_oq_iid($oq) {
	/** @var string $r */
	if (df_is_o($oq)) {
		$r = $oq->getIncrementId();
	}
	else {
		$r = $oq->reserveOrderId()->getReservedOrderId();
		// 2018-12-05
		// We should save the reserved order ID in the quote. It fixes the issue:
		// «The order number pulled into transaction description in the bank
		// doesn't match with our order numbers (it's off by 1)»:
		// https://github.com/mage2pro/tbc-bank/issues/1
		$oq->save();
	}
	return $r
;}

/**
 * 2017-11-02
 * An order/quote can be without a shipping address (consist of the Virtual products). In this case:
 * *) @uses \Mage_Sales_Model_Order::getShippingAddress() returns null
 * *) @uses \Mage_Sales_Model_Quote::getShippingAddress() returns an empty object.
 * It is useful for me to return an empty object in the both cases.
 * https://en.wikipedia.org/wiki/Null_object_pattern
 * An empty order address can be detected by a `null`-response on
 * @see \Mage_Sales_Model_Order_Address::getParentId()
 * @param O|Q $oq
 * @param bool $empty [optional]
 * @return OA|QA|null
 */
function df_oq_sa($oq, $empty = false) { /** @var OA|QA|null $r */
	if (df_is_o($oq)) {
		$r = $oq->getShippingAddress() ?: (!$empty ? null : new OA(['address_type' => OA::TYPE_SHIPPING]));
	}
	else if (df_is_q($oq)) {
		/**
		 * 2017-11-02
		 * I implemented it by analogy with @see \Mage_Quote_Model_Quote::_getAddressByType()
		 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Quote/Model/Quote.php#L1116-L1133
		 * @see \Mage_Sales_Model_Quote::getShippingAddress()
		 */
		$r = df_find(function(QA $a) use($empty) {return
			!$a->isDeleted() && QA::TYPE_SHIPPING === $a->getAddressType()
		;}, $oq->getAddressesCollection()) ?: (!$empty ? null : new QA(['address_type' => QA::TYPE_SHIPPING]));
	}
	else {
		df_error();
	}
	return $r;
}

/**
 * 2017-04-20
 * @param O|Q $oq
 * @return float
 */
function df_oq_shipping_amount($oq) {return df_is_o($oq) ? $oq->getShippingAmount() : (
	df_is_q($oq) ? $oq->getShippingAddress()->getShippingAmount() : df_error()
);}

/**
 * 2017-04-20
 * @param O|Q $oq
 * @return float
 */
function df_oq_shipping_desc($oq) {return df_is_o($oq) ? $oq->getShippingDescription() : (
	df_is_q($oq) ? $oq->getShippingAddress()->getShippingDescription() : df_error()
);}

/**
 * 2017-04-20
 * @param OI|QI $i
 * @return bool
 */
function df_oqi_is_leaf($i) {return df_is_oi($i) ? !$i->getChildrenItems() : (
	df_is_qi($i) ? !$i->getChildren() : df_error()
);}

/**
 * 2017-03-19
 * @param II|OP|QP $p
 * @return O|Q
 * @throws DFE
 */
function dfp_oq(II $p) {return df_assert($p instanceof OP ? $p->getOrder() : (
	$p instanceof QP ? $p->getQuote() : df_error()
));}