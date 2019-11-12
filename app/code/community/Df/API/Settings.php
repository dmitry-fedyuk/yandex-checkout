<?php
namespace Df\API;
use Df\Core\Exception as DFE;
use Mage_Core_Model_Store as S;
/**
 * 2019-03-13
 * @see \Df\Payment\Settings
 */
abstract class Settings extends \Df\Config\Settings {
	/**
	 * 2019-03-13
	 * @used-by key()
	 * @see \Df\Payment\Settings::titleB()
	 * @return string
	 */
	protected function titleB() {return df_class_second($this);}

	/**
	 * 2017-04-12
	 * @param null|string|int|S $s [optional]
	 * @return string
	 */
	final function merchantID($s = null) {return df_result_sne($this->probablyTestable(null, $s));}

	/**
	 * 2017-02-08
	 * @uses probablyTestableP()
	 * @param null|string|int|S $s [optional]
	 * @param bool $throw [optional]
	 * @return string|null
	 */
	final function privateKey($s = null, $throw = true) {return $this->key(
		'probablyTestableP', 'private', 'secret', $s, $throw
	);}

	/**
	 * 2016-11-12
	 * @uses probablyTestable()
	 * @return string
	 */
	function publicKey() {return $this->key('probablyTestable', 'public', 'publishable');}

	/**
	 * 2016-03-02
	 * @used-by testableGeneric()
	 * @used-by \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\Method::test()
	 * @used-by \Df\Payment\PlaceOrderInternal::message()
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	final function test($s = null) {return $this->b(null, $s);}

	/**
	 * 2016-11-12
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses v()
	 * @return mixed
	 */
	final protected function testable($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), 'v', $s, $d
	);}

	/**
	 * 2016-12-24
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses b()
	 * @return bool
	 */
	final protected function testableB($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), 'b', $s, $d
	);}

	/**
	 * 2016-11-12
	 * 2017-02-08
	 * Используйте этот метод в том случае,
	 * когда значение шифруется как в промышленном, так и в тестовом режимах.
	 * Если значение шифруется только в промышленном режиме, то используйте @see testablePV()
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses \Df\Payment\Settings::p()
	 * @return mixed
	 */
	final protected function testableP($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), 'p', $s, $d
	);}

	/**
	 * 2016-11-12
	 * 2017-02-08
	 * Используйте этот метод в том случае,
	 * когда значение шифруется в промышленном режиме, но не шифруется в тестовом.
	 * Если значение шифруется в обоих режимах, то используйте @see testableP()
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses p()
	 * @return mixed
	 */
	final protected function testablePV($k = null, $s = null, $d = null) {return $this->testableGeneric(
		$k ?: df_caller_f(), ['p', 'v'], $s, $d
	);}

	/**
	 * 2017-02-08
	 * @used-by privateKey()
	 * @used-by publicKey()
	 * @uses testable()
	 * @uses testableP()
	 * @param string $method
	 * @param string $type
	 * @param string $alt
	 * @param null|string|int|S $s [optional]
	 * @param bool $throw [optional]
	 * @return string|null
	 * @throws DFE
	 */
	private function key($method, $type, $alt, $s = null, $throw = true) {return
		$this->$method("{$type}Key", $s, function() use($method, $alt, $s) {return
			$this->$method("{$alt}Key", $s);}
		) ?: ($throw ? df_error("Please set your {$this->titleB()} $type key in the Magento backend.") : null)
	;}

	/**
	 * 2017-04-16
	 * Cначала мы пробуем найти значение с приставкой test/live, а затем без приставки.
	 * https://english.stackexchange.com/a/200637
	 * @used-by merchantID()
	 * @used-by publicKey()
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses v()
	 * @return mixed
	 */
	private function probablyTestable($k = null, $s = null, $d = null) {
		$k = $k ?: df_caller_f();
		return $this->testableGeneric($k, 'v', $s, function() use($k, $s, $d) {return $this->v($k, $s, $d);});
	}

	/**
	 * 2017-10-02
	 * @used-by privateKey()
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @param mixed|callable $d [optional]
	 * @uses v()
	 * @return mixed
	 */
	private function probablyTestableP($k = null, $s = null, $d = null) {
		$k = $k ?: df_caller_f();
		return $this->testableGeneric($k, 'p', $s, function() use($k, $s, $d) {return $this->p($k, $s, $d);});
	}

	/**
	 * 2016-11-12
	 * @used-by probablyTestable()
	 * @used-by testable()
	 * @used-by testableB()
	 * @used-by testableP()
	 * @used-by testablePV()
	 * @uses \Df\Config\Settings::p()
	 * @uses \Df\Config\Settings::v()
	 * @param string|null $k [optional]
	 * @param string|string[] $f [optional]
	 * $f может быть массивом,
	 * и тогда первое значение его — метод для промышленного режима,
	 * а второе значение — метод для тестового режима.
	 * @param null|string|int|S $s [optional]
	 * @param mixed|callable $d [optional]
	 * @return mixed
	 */
	private function testableGeneric($k = null, $f = 'v', $s = null, $d = null) {return call_user_func(
		[$this, is_string($f) ? $f : $f[intval($this->test($s))]]
		,($this->test($s) ? 'test' : 'live') . self::phpNameToKey(ucfirst($k ?: df_caller_f()))
		,$s, $d
	);}
}