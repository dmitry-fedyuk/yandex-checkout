<?php
namespace Df\Core;
// 2017-08-30
interface ICached {
	/**
	 * 2017-08-30
	 * @used-by \Df\Core\RAM::set()
	 * @return string[]
	 */
	function tags();
}