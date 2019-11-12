<?php
use Df\Payment\Redirector as R;
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
			$o->sendNewOrderEmail();
			$this->getResponse()->setRedirect(Mage::getUrl('checkout/onepage/success'));
		}
		else {
			R::restoreQuote();
			$this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
		}
	}
}