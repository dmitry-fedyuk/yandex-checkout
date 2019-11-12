<?php
use Exception as E;
use Mage_Adminhtml_Model_Session as SB;
use Mage_Core_Exception as ME;
use Mage_Core_Model_Session as S;
/**
 * @used-by \Df_YandexCheckout_RedirectController::indexAction()
 * @param E|ME $e
 * @return void
 */
function df_exception_to_session(E $e) {
	$m = nl2br(df_xml_output_html(df_ets($e))); /** @var string $m */
	$isMagentoCoreException = $e instanceof ME; /** @var bool $isMagentoCoreException */
	$needShowStackTrace = df_is_backend() || df_my_local(); /** @var bool $needShowStackTrace */
	if ($m) {
		df_session()->addError($m);
	}
	else if ($isMagentoCoreException && $e->getMessages()) {
		foreach ($e->getMessages() as $subMessage) { /** @var Mage_Core_Model_Message_Abstract $subMessage */
			df_session()->addError($subMessage->getText());
		}
	}
	else if (!$needShowStackTrace) {
		df_session()->addError('Произошёл внутренний сбой.'); // Надо хоть какое-то сообщение показать
	}
	if ($needShowStackTrace) {
		df_session()->addError(nl2br(df_exception_get_trace($e)));
	}
}

/**
 * 2019-11-10
 * @used-by df_exception_to_session()
 * @return S|SB
 */
function df_session() {return dfcf(function() {return Mage::getSingleton(
	(df_is_backend() ? 'adminhtml' : 'core') . '/session'
);});}