<?php
/**
 * 2019-10-29
 * A block class should be inherited from Mage_Core_Block_Abstract, otherwise Magento fails.
 */
final class Df_YandexCheckout_Block_Form extends Mage_Core_Block_Template {
	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::getTemplate()
	 * @used-by Mage_Core_Block_Template::getTemplateFile()
	 * @return string
	 */
	function getTemplate() {return 'df/yandex_checkout/form.phtml';}
}