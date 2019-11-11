<?php
namespace Df\YandexCheckout;
use Df\YandexCheckout\Block\Form;
use Mage_Core_Model_Store as S;
use Mage_Payment_Model_Info as Info;
use Mage_Sales_Model_Order_Payment as OP;
use Mage_Sales_Model_Quote as Q;
use Mage_Sales_Model_Quote_Payment as QP;
/**
 * 2019-10-29
 * The class should be inherited from @see \Mage_Payment_Model_Method_Abstract
 * because of @see \Mage_Payment_Helper_Data::getMethodFormBlock()
 */
final class Method extends \Mage_Payment_Model_Method_Abstract {
	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::canUseCheckout()
	 * @used-by \Mage_Checkout_Block_Onepage_Payment_Methods::_canUseMethod()
	 * @return bool
	 */
	function canUseCheckout() {return true;}

	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::getCode()
	 * @used-by getConfigData()
	 * @used-by \Mage_Payment_Block_Form_Container::_prepareLayout()
	 * @return string
	 */
	function getCode() {return 'yandex_checkout';}

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
	function getConfigData($k, $s = null) {return \Mage::getStoreConfig(
		"payment/{$this->getCode()}/$k", $s ?: $this->_storeId
	);}

	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::getFormBlockType()
	 * @used-by \Mage_Payment_Helper_Data::getMethodFormBlock()
	 * @return string
	 */
	function getFormBlockType() {return Form::class;}

	/**
	 * 2019-10-29
	 * 2019-11-11 The result is an @see OP on an order placement.
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::getInfoInstance()
	 * @used-by \Mage_Payment_Block_Form::getInfoData()
	 * @return Info|QP|OP
	 */
	function getInfoInstance() {return $this->_ii;}

	/**
	 * 2019-11-10
	 * @override
	 * The @see \Mage_Payment_Model_Method_Abstract class does not have the `getOrderPlaceRedirectUrl` method:
	 * it is handled by @see Varien_Object.
	 * @used-by \Mage_Checkout_Model_Type_Onepage::saveOrder():
	 *		$redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
	 *		if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
	 *			try {
	 *				$order->sendNewOrderEmail();
	 *			} catch (Exception $e) {
	 *				Mage::logException($e);
	 *			}
	 *		}
	 *		// add order information to the session
	 *		$this->_checkoutSession->setLastOrderId($order->getId())
	 *			->setRedirectUrl($redirectUrl)
	 *			->setLastRealOrderId($order->getIncrementId());
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.4.3/app/code/core/Mage/Checkout/Model/Type/Onepage.php#L838-L857
	 * @return string
	 */
	function getOrderPlaceRedirectUrl() {return \Mage::getUrl('df-yandex-checkout/redirect', ['_secure' => true]);}

	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::getTitle()
	 * @used-by app/design/frontend/base/default/template/checkout/onepage/payment/methods.phtml
	 * @return string
	 */
	function getTitle() {return 'Яндекс.Касса';}

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
	function isApplicableToQuote($q, $checksBitMask) {return true;}

	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::isAvailable()
	 * @used-by \Mage_Payment_Helper_Data::getStoreMethods()
	 * @param Q|null $q [optional]
	 * @return bool
	 */
	function isAvailable($q = null) {return (bool)(int)$this->getConfigData('active', $q ? $q->getStoreId() : null);}

	/**
	 * 2019-11-10
	 * @used-by \Df_YandexCheckout_RedirectController::indexAction()
	 * @return string
	 */
	function redirectUrl() {return \Df\YandexCheckout\RedirectURL::get($this);}

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
	function setInfoInstance(Info $v) {$this->_ii = $v;}

	/**
	 * 2019-10-29
	 * @override
	 * The @see \Mage_Payment_Model_Method_Abstract class does not have the `setSortOrder` method:
	 * it is handled by @see \Varien_Object.
	 * @see Varien_Object::__call()
	 * @used-by Mage_Payment_Helper_Data::getStoreMethods()
	 * @param int $v
	 */
	function setSortOrder($v) {$this->sort_order = $v;}

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
	function setStore($v) {$this->_storeId = df_store_id($v); return $this;}

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