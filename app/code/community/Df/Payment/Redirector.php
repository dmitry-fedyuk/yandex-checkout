<?php
namespace Df\Payment;
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Quote as Q;
// 2019-11-10
final class Redirector {
	/**
	 * 2018-11-19
	 * @used-by \Df\Payment\Observer::controller_action_predispatch_checkout()
	 * @used-by \Df_YandexCheckout_RedirectController::indexAction()
	 * @return bool
	 */
	static function is() {return !!df_checkout_session()->getData(self::$K);}

	/**
	 * 2018-11-19
	 * @used-by \Df\Payment\Observer::controller_action_predispatch_checkout()
	 * @used-by \Df_YandexCheckout_RedirectController::indexAction()
	 */
	static function restoreQuote() {
		$o = df_checkout_session()->getLastRealOrder(); /** @var O $o */
		if ($o->canCancel()) {
			$o->cancel()->save();
		}
		if ($qid = df_checkout_session()->getData('last_success_quote_id')) {  /** @var int|null $qid */
			$q = \Mage::getModel('sales/quote');	/** @var Q $q */
			$q->load($qid);
			$q->setIsActive(true);
			$q->save();
			// 2019-02-15 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// «When a customer checkouts as a guest, but cancels at ECPay checkout,
			// the contents do not stay in cart».
			// https://github.com/sunpeak-us/ecpay/issues/21
			df_checkout_session()->replaceQuote($q);
		}
		self::unset();
	}

	/**
	 * 2018-11-19
	 * @used-by \Df_YandexCheckout_RedirectController::indexAction()
	 */
	static function set() {df_checkout_session()->setData(self::$K, true);}

	/**
	 * 2018-11-19
	 * @used-by \Df\Payment\Observer::controller_action_predispatch_checkout()
	 */
	static function unset() {df_checkout_session()->unsetData(self::$K);}

	/**
	 * 2018-11-19
	 * @used-by is()
	 * @used-by set()
	 * @used-by unset()
	 * @var string
	 */
	private static $K = 'mage2pro_ecpay_redirected';
}