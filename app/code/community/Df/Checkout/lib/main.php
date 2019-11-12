<?php
use Mage_Checkout_Model_Session as S;
/**
 * 2019-11-10
 * @used-by df_quote()
 * @used-by \Df\Payment\Redirector::is()
 * @used-by \Df\Payment\Redirector::restoreQuote()
 * @used-by \Df\Payment\Redirector::set()
 * @used-by \Df\Payment\Redirector::unset()
 * @used-by \Df_YandexCheckout_RedirectController::indexAction()
 * @return S
 */
function df_checkout_session() {return \Mage::getSingleton('checkout/session');}