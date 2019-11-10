<?php
namespace Df\Payment;
use Df\Payment\Redirector as R;
use Varien_Event_Observer as Ob;
use Mage_Core_Controller_Varien_Action as A;
// 2019-11-10
final class Observer {
	/**
	 * Если покупатель перешёл сюда с сайта платёжной системы, а не со страницы нашего магазина,
	 * то нужно не перенаправлять покупателя назад на сайт платёжной системы,
	 * а позволить покупателю оплатить заказ другим способом.
	 *
	 * Покупатель мог перейти сюда с сайта платёжной системы, нажав кнопку «Назад» в браузере,
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
	 * Видимо, Firefox и Chrome так делают по той причине, что посетитель со страницы checkout/onepage
	 * перенаправляется через страницу df_payment/redirect на страницу платёжной системы
	 * автоматически, скриптом, без участия покупателя.
	 *
	 * Поэтому мы делаем обработку в двух точках:
	 * @see \Df_Payment_RedirectController::indexAction()
	 * @see \Df_Checkout_Observer::controller_action_predispatch_checkout()
	 * @used-by \Mage_Core_Model_App::_callObserverMethod()
	 * @param Ob $o
	 */
	function controller_action_predispatch_checkout(Ob $o) {
		try {
			if (R::is()) {
				$c = $o['controller_action']; /** @var \Mage_Core_Controller_Varien_Action $c */
				if ('checkout_onepage_success' === $c->getFullActionName()) {
					/**
					 * Здесь необходимость вызова @uses \Df\Payment\Redirector::unset() не вызывает сомнений,
					 * потому что @see \Df\Payment\Observer::controller_action_predispatch_checkout()
					 * обрабатывает именно сессию покупателя, а не запрос платёжной системы.
					 */
					R::unset();
				}
				else {
					R::restoreQuote();
					// 2019-02-15
					// Without this code block a guest customer will be returned
					// to the `checkout/onepage` page instead of the `checkout/cart` page.
					if ('checkout_cart_index' !== $c->getFullActionName()) {
						$c->getResponse()->setRedirect(\Mage::getUrl('checkout/cart'));
						$c->setFlag('', A::FLAG_NO_DISPATCH, true);
					}
				}
			}
		}
		catch (\Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}