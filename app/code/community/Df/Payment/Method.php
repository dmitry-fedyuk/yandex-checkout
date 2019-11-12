<?php
namespace Df\Payment;
use Mage_Core_Model_Store as S;
use Mage_Payment_Model_Info as Info;
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Order_Payment as OP;
use Mage_Sales_Model_Quote as Q;
use Mage_Sales_Model_Quote_Payment as QP;
/**
 * 2019-10-29
 * The class should be inherited from @see \Mage_Payment_Model_Method_Abstract
 * because of @see \Mage_Payment_Helper_Data::getMethodFormBlock()
 * @see \Df\YandexCheckout\Method()
 */
abstract class Method extends \Mage_Payment_Model_Method_Abstract {
	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::canUseCheckout()
	 * @used-by \Mage_Checkout_Block_Onepage_Payment_Methods::_canUseMethod()
	 * @return bool
	 */
	final function canUseCheckout() {return true;}

	/**
	 * 2016-08-20
	 * 2016-09-05
	 * Отныне валюта платёжных транзакций настраивается администратором опцией
	 * «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
	 * 2017-02-08 Конвертирует $a из учётной валюты в валюту платежа.
	 * @see \Df\Payment\Currency::iso3()
	 * @used-by _void()
	 * @used-by dfp_due()
	 * @param float $a
	 * @return float
	 * @uses \Df\Payment\Currency::fromBase()
	 */
	final function cFromBase($a) {return $this->convert($a);}

	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::getCode()
	 * @used-by getConfigData()
	 * @used-by \Mage_Payment_Block_Form_Container::_prepareLayout()
	 * @return string
	 */
	final function getCode() {return self::codeS();}

	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::getConfigData()
	 * @used-by isAvailable()
	 * @used-by \Mage_Payment_Helper_Data::getStoreMethods()
	 * @param string $k
	 * @param int|string|null|S $s [optional]
	 * @return string
	 */
	final function getConfigData($k, $s = null) {return \Mage::getStoreConfig(
		"payment/{$this->getCode()}/$k", $s ?: $this->_storeId
	);}

	/**
	 * 2019-10-29
	 * 2019-11-11 The result is an @see OP on an order placement.
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::getInfoInstance()
	 * @used-by ii()
	 * @used-by \Mage_Payment_Block_Form::getInfoData()
	 * @return Info|QP|OP
	 */
	final function getInfoInstance() {return $this->_ii;}

	/**
	 * 2016-02-09
	 * @override
	 * The @see \Mage_Payment_Model_Method_Abstract class does not have the `setStore` method:
	 * it is handled by @see \Varien_Object.
	 * @see \Varien_Object::__call()
	 * @used-by \Df\Payment\Settings::scopeDefault()
	 * @return int
	 */
	final function getStore() {return $this->_storeId;}

	/**
	 * 2019-11-11
	 * @used-by dfp_due()
	 * @used-by oq()
	 * @return Info|OP|QP
	 */
	final function ii() {return $this->getInfoInstance();}

	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::isApplicableToQuote()
	 * @used-by \Mage_Payment_Block_Form_Container::_canUseMethod()
	 * @used-by \Mage_Payment_Block_Form_Container::getMethods()
	 * @param Q $q
	 * @param int|null $checksBitMask
	 * @return bool
	 */
	final function isApplicableToQuote($q, $checksBitMask) {return true;}

	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::isAvailable()
	 * @used-by \Mage_Payment_Helper_Data::getStoreMethods()
	 * @param Q|null $q [optional]
	 * @return bool
	 */
	final function isAvailable($q = null) {return (bool)(int)$this->getConfigData(
		'active', $q ? $q->getStoreId() : null
	);}

	/**
	 * 2017-02-07
	 * 2017-10-26
	 * A customer has reported that this method can return `null`, but I am unable to reproduce it:
	 * https://mage2.pro/t/4764
	 * 2018-10-07 We should not cache the result: https://github.com/mage2pro/core/issues/80
	 * @used-by dfp_due()
	 * @return O|Q
	 */
	final function oq() {return $this->ii()->getOrder() ?: $this->ii()->getQuote();}

	/**
	 * 2016-07-13
	 * 2017-07-02
	 * Сегодня заметил, что параметр scope сюда никто не передаёт, поэтому убрал его.
	 * @see \Df\Payment\Settings::scopeDefault()
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\Payment\Currency::s()
	 * @param string|null $k [optional]
	 * @param mixed|callable $d [optional]
	 * @return Settings|mixed
	 */
	final function s($k = null, $d = null) {
		$r = dfc($this, function() { /** @var Settings $r */
			if (!($c = df_con_hier($this, Settings::class, false))) { /** @var string $c */
				df_error('Unable to find a proper «Settings» class for the «%s» payment module.',
					df_module_name($this)
				);
			}
			return new $c($this);
		});
		return is_null($k) ? $r : $r->v($k, null, $d);
	}

	/**
	 * 2019-10-29
	 * @override
	 * The @see \Mage_Payment_Model_Method_Abstract class does not have the `setInfoInstance` method:
	 * it is handled by @see Varien_Object.
	 * @see \Varien_Object::__call()
	 * @@used-by \Mage_Payment_Block_Form_Container::_assignMethod()
	 * @used-by \Mage_Payment_Model_Info::getMethodInstance()
	 * @param Info|QP $v
	 */
	final function setInfoInstance(Info $v) {$this->_ii = $v;}

	/**
	 * 2019-10-29
	 * @override
	 * The @see \Mage_Payment_Model_Method_Abstract class does not have the `setSortOrder` method:
	 * it is handled by @see \Varien_Object.
	 * @see Varien_Object::__call()
	 * @used-by Mage_Payment_Helper_Data::getStoreMethods()
	 * @param int $v
	 */
	final function setSortOrder($v) {$this->sort_order = $v;}

	/**
	 * 2019-10-29
	 * 2019-11-10
	 * The method should return $this: @see \Mage_Sales_Model_Quote_Payment::getMethodInstance()
	 * @override
	 * The @see \Mage_Payment_Model_Method_Abstract class does not have the `setStore` method:
	 * it is handled by @see \Varien_Object.
	 * @see \Varien_Object::__call()
	 * $v is an int here: @used-by Mage_Payment_Helper_Data::getStoreMethods()
	 * $v is an S here: @used-by \Mage_Sales_Model_Quote_Payment::getMethodInstance()
	 * @param S|string|null $v «34»
	 * @return $this
	 */
	final function setStore($v) {$this->_storeId = df_store_id($v); return $this;}

	/**
	 * 2016-09-06
	 * @uses \Df\Payment\Currency::fromBase()
	 * @uses \Df\Payment\Currency::fromOrder()
	 * @uses \Df\Payment\Currency::toBase()
	 * @uses \Df\Payment\Currency::toOrder()
	 * @used-by cFromBase()
	 * @used-by cToBase()
	 * @used-by cToOrder()
	 * @param float $a
	 * @return float
	 */
	private function convert($a) {return call_user_func(
		[$this->currency(), lcfirst(substr(df_caller_f(), 1))], $a, $this->oq()
	);}

	/**
	 * 2017-10-12
	 * @used-by convert()
	 * @used-by cPayment()
	 * @used-by isAvailable()
	 * @return Currency
	 */
	private function currency() {return dfc($this, function() {return dfp_currency($this);});}

	/**
	 * 2016-07-10
	 * @used-by dfpm_code()
	 * @used-by getCode()
	 * @return string
	 */
	final static function codeS() {return dfcf(function($c) {return df_const(
		$c, 'CODE', function() use($c) {return df_module_name_lc($c);}
	);}, [static::class]);}

	/**
	 * 2017-03-30
	 * Каждый потомок Method является объектом-одиночкой: @see \Df\Payment\Method::sg(),
	 * но вот info instance в него может устанавливаться разный.
	 * Поэтому будьте осторожны с кэшированием внутри Method!
	 * @used-by dfpm()
	 * @param string $c
	 * @return self
	 */
	final static function sg($c) {return dfcf(function($c) {return new $c;}, [dfpm_c($c)]);}

	/**
	 * 2019-10-29
	 * @used-by setSortOrder()
	 * @used-by \Mage_Payment_Helper_Data::_sortMethods()
	 * @var int
	 */
	public $sort_order;

	/**
	 * 2019-10-29
	 * @used-by getInfoInstance()
	 * @used-by setInfoInstance()
	 * @var Info|QP
	 */
	private $_ii;

	/**
	 * 2019-10-29
	 * @used-by getConfigData()
	 * @used-by setStore()
	 * @var int
	 */
	private $_storeId;
}