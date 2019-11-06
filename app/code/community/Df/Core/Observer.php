<?php
namespace Df\Core;
// 2019-11-05
final class Observer {
	/**
	 * 2019-11-05
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	function controller_front_init_before() {\Df\Core\Boot::p();}
}