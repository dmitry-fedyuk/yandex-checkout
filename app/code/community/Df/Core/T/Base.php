<?php
/**
 * 2019-11-06
 * @see Df_YandexCheckout_T_Case1
 */
abstract class Df_Core_T_Base extends \PHPUnit\Framework\TestCase {
	/**
	 * 2019-11-06
	 * @override
	 * @see \PHPUnit\Framework\TestCase::setUp()
	 */
	protected function setUp() {
		if (!self::$r) {
			self::$r = true;
			Df_Core_Boot::p();
		}
	}

	/**
	 * 2016-11-30
	 * https://3v4l.org/Ns95Q
	 * @var bool
	 */
	private static $r;
}