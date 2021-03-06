<?php
use Df\Payment\Redirector as R;
use Df\YandexCheckout\Method as M;
/**
 * 2019-11-10
 * 2019-11-12
 * Magento 1 does not support namespaces for controller class names:
 * @see \Mage_Core_Controller_Varien_Router_Standard::getControllerClassName()
 */
class Df_YandexCheckout_RedirectController extends Mage_Core_Controller_Front_Action {
	/**
	 * 2019-11-10
	 * @return void
	 */
	function indexAction() {
		$urlCheckout = 'checkout/onepage'; /** @var string $urlCheckout */
		try {
			/**
			 * Если покупатель перешёл сюда с сайта платёжной системы,
			 * а не со страницы нашего магазина,
			 * то нужно не перенаправлять покупателя назад на сайт платёжной системы,
			 * а позволить покупателю оплатить заказ другим способом.
			 *
			 * Покупатель мог перейти сюда с сайта платёжной системы,
			 * нажав кнопку «Назад» в браузере,
			 * или же нажав специализированную кнопку отмены операции на сайте платёжной системы
			 * (например, на платёжной странице LiqPay кнопка «В магазин»
			 * работает как javascript:history.back()).
			 *
			 * Обратите внимание, что последние версии браузеров Firefox и Chrome
			 * при нажатии посетителем браузерной кнопки «Назад»
			 * перенаправляют посетилеля не на страницу df_payment/redirect,
			 * а сразу на страницу checkout/onepage.
			 *
			 * Впервые заметил такое поведение 17 сентября 2013 года в Forefox 23.0.1 и Chrome 29,
			 * причём Internet Explorer 10 в тот же день вёл себя по-прежнему.
			 *
			 * Видимо, Firefox и Chrome так делают по той причине,
			 * что посетитель со страницы checkout/onepage
			 * перенаправляется через страницу df_payment/redirect на страницу платёжной системы
			 * автоматически, скриптом, без участия покупателя.
			 *
			 * Поэтому мы делаем обработку в двух точках:
			 * @see \Df_Payment_RedirectController::indexAction
			 * @see \Df_Checkout_Observer::controller_action_predispatch_checkout
			 */
			if (R::is()) {
				R::restoreQuote();
				$this->_redirect($urlCheckout);
			}
			else {
				R::set();
				$m = df_checkout_session()->getLastRealOrder()->getPayment()->getMethodInstance(); /** @var M $m */
				$this->getResponse()->setRedirect($m->redirectUrl());
			}
		}
		catch (Exception $e) {
			/**
			 * Обратите внимание,
			 * что при возвращении на страницу RM_URL_CHECKOUT
			 * диагностическое сообщение надо добавлять в df_session_core(),
			 * а не в df_checkout_session(),
			 * потому что сообщения сессии checkout
			 * не отображаются в стандартной теме на странице checkout/onepage
			 */
			df_exception_to_session($e);
			df_log($e);
			R::restoreQuote();
			$this->_redirect($urlCheckout);
		}
	}
}