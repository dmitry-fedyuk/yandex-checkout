<?php
/**
 * 2019-10-29
 * A block class should be inherited from Mage_Core_Block_Abstract, otherwise Magento fails.
 */
final class LesMills_YandexCheckout_Block_Form extends Mage_Core_Block_Abstract {
	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {return __METHOD__;}
}