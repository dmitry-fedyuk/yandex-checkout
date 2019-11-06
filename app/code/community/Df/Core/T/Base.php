<?php
namespace Df\Core\T;
/**
 * 2019-11-06
 * @see \Df\YandexCheckout\T\Case1
 */
abstract class Base extends \PHPUnit\Framework\TestCase {
	/**
	 * 2019-11-06
	 * @override
	 * @see \PHPUnit\Framework\TestCase::setUp()
	 */
	protected function setUp() {
		if (!self::$r) {
			self::$r = true;
			\Df\Core\Boot::p();
		}
	}

	/**
	 * 2016-11-30
	 * https://3v4l.org/Ns95Q
	 * @var bool
	 */
	private static $r;
}