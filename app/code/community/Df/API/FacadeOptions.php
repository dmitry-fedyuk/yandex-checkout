<?php
namespace Df\API;
use Df\API\Document as D;
// 2019-04-05
final class FacadeOptions {
	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::p()
	 * @param string|null|string $v [optional]
	 * @return string|$this
	 */
	function resC($v = DF_N) {return df_prop($this, $v, D::class);}

	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::p()
	 * @param bool|null|string $v [optional]
	 * @return bool|$this
	 */
	function silent($v = DF_N) {return df_prop($this, $v);}

	/**
	 * 2019-04-05
	 * @used-by i()
	 */
	private function __construct() {}

	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::opts()
	 * @return FacadeOptions
	 */
	static function i() {return new self;}
}