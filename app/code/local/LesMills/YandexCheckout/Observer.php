<?php
// 2019-11-05
final class LesMills_YandexCheckout_Observer {
	/**
	 * 2019-11-05
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	function controller_front_init_before() {
		static $done; /** @var bool $done */
		if (!$done) {
			$requireFiles = function($libDir) use(&$requireFiles) {
				// 2015-02-06 array_slice removes «.» and «..»: http://php.net/manual/function.scandir.php#107215
				foreach (array_slice(scandir($libDir), 2) as $c) {  /** @var string $resource */
					is_dir($resource = "{$libDir}/{$c}") ? $requireFiles($resource) : require_once "{$libDir}/{$c}";
				}
			};
			$requireFiles(BP . '/app/code/local/LesMills/YandexCheckout/lib');
			$done = true;
		}
	}
}