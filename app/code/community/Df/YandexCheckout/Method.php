<?php
namespace Df\YandexCheckout;
use Df\YandexCheckout\Block\Form;
// 2019-10-29
final class Method extends \Df\Payment\Method {
	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::getFormBlockType()
	 * @used-by \Mage_Payment_Helper_Data::getMethodFormBlock()
	 * @return string
	 */
	function getFormBlockType() {return Form::class;}

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
	function getOrderPlaceRedirectUrl() {return \Mage::getUrl("{$this->getCode()}/redirect", ['_secure' => true]);}

	/**
	 * 2019-10-29
	 * @override
	 * @see \Mage_Payment_Model_Method_Abstract::getTitle()
	 * @used-by app/design/frontend/base/default/template/checkout/onepage/payment/methods.phtml
	 * @return string
	 */
	function getTitle() {return 'Яндекс.Касса';}

	/**
	 * 2019-11-10
	 * @used-by \Df_YandexCheckout_RedirectController::indexAction()
	 * @return string
	 */
	function redirectUrl() {return \Df\YandexCheckout\RedirectURL::get($this);}
}