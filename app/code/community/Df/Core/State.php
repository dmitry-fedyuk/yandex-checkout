<?php
namespace Df\Core;
use Mage_Core_Model_Store as Store;
final class State {
	/**
	 * @used-by df_controller()
	 * @return \Mage_Core_Controller_Varien_Action|null
	 */
	function getController() {return $this->_controller;}

	/**
	 * @used-by Df_Core_Observer::controller_action_predispatch()
	 * @param \Mage_Core_Controller_Varien_Action $controller
	 * @return void
	 */
	function setController(\Mage_Core_Controller_Varien_Action $controller) {
		$this->_controller = $controller;
	}
	
	/**     
	 * @used-by \Df\Core\O::cacheKeyPerStore()
	 * @used-by \Df\Core\O::cacheLoad() 
	 * @used-by \Df\Core\O::cacheSave()
	 * @return bool 
	 */
	function storeInitialized() {
		static $r = false; /** @var bool $r */
		if (!$r) {
			try {df_store(); $r = true;}
			catch (\Mage_Core_Model_Store_Exception $e) {}
		}
		return $r;
	}	

	/**
	 * 2015-09-02 Значение по умолчанию null можно не указывать.
	 * @var Store|null
	 */
	private $_controller;
	
	/** @return self */
	public static function s() {static $r; return $r ?: $r = new self;}
}