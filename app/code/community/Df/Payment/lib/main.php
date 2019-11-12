<?php
use Df\Payment\Method as M;
use Mage_Payment_Model_Info as II;
use Mage_Payment_Model_Method_Abstract as IM;
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Order_Payment as OP;
use Mage_Sales_Model_Order_Payment_Transaction as T;
use Mage_Sales_Model_Quote as Q;
use Mage_Sales_Model_Quote_Payment as QP;
/**
 * 2017-03-21
 * 2019-08-01
 * 1) "PayPal backend payments fail with the "ID required" error":
 * https://github.com/mage2pro/core/issues/88
 * 2) https://github.com/mage2pro/core/issues/88#issuecomment-516964680
 * @used-by df_trans_is_test()
 * @used-by dfp_iia()
 * @used-by dfpm()
 * @used-by \Df\Payment\Method::getInfoInstance()
 * @used-by \Df\Payment\PlaceOrderInternal::s()
 * @used-by \Df\Payment\Choice::f()
 * @param II|OP|QP|O|Q|T $v
 * @return II|OP|QP|null
 */
function dfp($v) {return $v instanceof II ? $v : (
	$v instanceof T ? ($v['payment'] ?: df_load(OP::class, $v->getPaymentId())) : (
		df_is_oq($v) ? $v->getPayment() : df_error()
	)
);}

/**
 * 2016-05-20
 * @see df_ci_add()
 * @used-by \Df\Payment\Method::iiaAdd()
 * @param II|OP|QP $p
 * @param array $info
 */
function dfp_add_info(II $p, array $info) {
	foreach ($info as $k => $v) {/** @var string $k */ /** @var string $v */
		$p->setAdditionalInformation($k, $v);
	}
}

/**
 * 2017-01-19
 * 2017-02-09
 * Контейнеры используются для хранения в едином поле множества значений.
 * Пока это возможность используется только в сценарии возврата:
 * если возврат был инициирован на стороне Magento, то мы запоминаем посредством dfp_container_add()
 * его идентификатор, чтобы когда платёжная система сообщит нам о нашем же возврате через webhook,
 * мы знали, что этот возврат мы уже обрабатывали и не обрабатывали бы его повторно:
 * https://github.com/mage2pro/core/blob/1.12.16/StripeClone/Method.php?ts=4#L262-L273
 * @param II|OP|QP $p
 * @param string $k
 * @param string $v
 */
function dfp_container_add(II $p, $k, $v) {$p->setAdditionalInformation($k, df_json_encode(
	array_merge(dfp_container_get($p, $k), [$v])
));}

/**
 * 2017-01-19
 * 2017-02-09
 * Пока эта функция имеет лишь вспомогательное значение:
 * @used-by dfp_container_add()
 * @used-by dfp_container_has()
 * @param II|OP|QP $p
 * @param string $k
 * @return string[]
 * 2017-03-11
 * Формально возвращает array(string => mixed), но реально — string[].
 */
function dfp_container_get(II $p, $k) {/** @var string $j */ return
	!($j = $p->getAdditionalInformation($k)) ? [] : df_json_decode($j)
;}

/**
 * 2017-01-19
 * https://github.com/mage2pro/core/blob/1.12.16/StripeClone/WebhookStrategy/Charge/Refunded.php?ts=4#L21-L23
 * @param II|OP|QP $p
 * @param string $k
 * @param string $v
 * @return bool
 */
function dfp_container_has(II $p, $k, $v) {return in_array($v, dfp_container_get($p, $k));}

/**
 * 2016-08-08
 * @used-by \Df\Payment\Charge::iia()
 * @used-by \Df\Payment\Method::iia()
 * @used-by \Df\Payment\Token::get()
 * @param II|OP|QP|O|Q $p
 * @param mixed ...$k  [optional]
 * @return mixed|array(string => mixed)
 */
function dfp_iia($p, ...$k) {$p = dfp($p); return
	!($k = dfa_flatten($k)) ? $p->getAdditionalInformation() : (
		1 === count($k) ? $p->getAdditionalInformation($k[0]) :
			dfa_select_ordered($p->getAdditionalInformation(), $k)
	)
;}

/**
 * 2016-08-26
 * @param float|int|string $a
 * @return string
 */
function dfp_last2($a) {return substr(strval(round(100 * df_float($a))), -2);}

/**
 * 2016-08-19
 * @see df_trans_is_my()
 * @param IM|II|OP|QP|T|object|string|O|null $v
 * @return bool
 */
function dfp_my($v) {return $v && dfpm($v) instanceof M;}