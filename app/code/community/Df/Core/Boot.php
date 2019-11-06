<?php
namespace Df\Core;
// 2019-11-06
final class Boot {
	/**
	 * 2019-11-05
	 * @used-by Df_Core_Observer::controller_front_init_before()
	 * @used-by Df_Core_T_Base::setUp()
	 * @return void
	 */
	static function p() {
		static $done; /** @var bool $done */
		if (!$done) {
			// 2017-11-13
			// Today I have added the subdirectories support inside the `lib` folders,
			// because some lib/*.php files became too big, and I want to split them.
			$requireFiles = function($libDir) use(&$requireFiles) {
				// 2015-02-06 array_slice removes «.» and «..»: http://php.net/manual/function.scandir.php#107215
				foreach (array_slice(scandir($libDir), 2) as $c) {  /** @var string $resource */
					is_dir($resource = "{$libDir}/{$c}") ? $requireFiles($resource) : require_once "{$libDir}/{$c}";
				}
			};
			$base = BP . '/app/code/community/Df'; /** @var string $base */
			// 2017-06-18 The strange array_diff / array_merge combination makes the Df_Core module to be loaded first.
			foreach (array_merge(['Core'], array_diff(scandir("$base/"), ['Core'])) as $m) {
				// 2016-11-23 It gets rid of the ['..', '.'] and the root files (non-directories).
				if (ctype_upper($m[0]) && is_dir($baseM = $base . $m)) {/** @var string $baseM */
					if (is_dir($libDir = "{$baseM}/lib")) { /** @var string $libDir */
						$requireFiles($libDir);
					}
				}
			}
			$done = true;
		}
	}
}