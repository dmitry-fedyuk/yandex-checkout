<?php
use Df\Payment\Redirector as R;
use Df\YandexCheckout\RedirectURL;
use Mage_Sales_Model_Order as O;
// 2019-11-12
class Df_YandexCheckout_CustomerReturnController extends Mage_Core_Controller_Front_Action {
	/**
	 * 2019-11-12
	 * @return void
	 */
	function indexAction() {
		$o = df_checkout_session()->getLastRealOrder(); /** @var O $o */
		if ($o->getId() && !$o->isCanceled()) {
			/**
			 * 2019-01-31 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			 * «Improve ECpay module for Magento 1: send order emails to customers»
			 * https://www.upwork.com/ab/f/contracts/21411797
			 * https://github.com/sunpeak-us/ecpay/issues/20
			 */
			$o->sendNewOrderEmail();
			$this->getResponse()->setRedirect(Mage::getUrl('checkout/onepage/success'));
		}
		else {
			R::restoreQuote();
			$this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
		}
	}
}