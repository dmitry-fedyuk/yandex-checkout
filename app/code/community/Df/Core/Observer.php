<?php
namespace Df\Core;
// 2019-11-05
final class Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param \Varien_Event_Observer $o
	 * @return void
	 */
	function controller_action_predispatch(\Varien_Event_Observer $o) {
		// Как ни странно, ядро Magento этого не делает,
		// и поэтому диагностические сообщения валидиторов из Zend Framework оставались непереведёнными.
		// Ставим собаку, потому что иначе при переключении административной части
		// с русскоязычного интерфейса на англоязычный Zend Framework пишет:
		// «Notice: No translation for the language 'en' available.»
		// «Notice: The language 'en_US' has to be added before it can be used.»
		@\Zend_Registry::set('Zend_Translate', \Mage::app()->getTranslator()->getTranslate());
		df_state()->setController($o['controller_action']);
	}

	/**
	 * 2019-11-05
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	function controller_front_init_before() {\Df\Core\Boot::p();}
	
	/**
	 * 2019-11-09
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	function controller_front_send_response_after() {\Df\Core\GlobalSingletonDestructor::s()->process();}	
}