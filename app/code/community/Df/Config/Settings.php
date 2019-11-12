<?php
namespace Df\Config;
use Mage_Core_Model_Store as S;
/**
 * 2015-11-09
 * @see \Df\API\Settings
 */
abstract class Settings {
	/**
	 * 2015-11-09
	 * 2016-11-24 Отныне значение должно быть без слеша на конце.
	 * @used-by \Df\Config\Settings::v()
	 * @see \Df\Payment\Settings::prefix()
	 * @return string
	 */
	abstract protected function prefix();

	/**
	 * 2015-11-09
	 * @used-by \Df\API\Settings::test()
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @param bool $d [optional]
	 * @return int
	 */
	final function b($k = null, $s = null, $d = false) {return df_bool($this->v($k ?: df_caller_f(), $s, $d));}

	/**
	 * 2016-03-09 Может возвращать строку или false.
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @return string|false
	 */
	final function bv($k= null, $s = null) {return $this->v($k ?: df_caller_f(), $s) ?: false;}

	/**
	 * 2016-03-14
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @return string[]
	 */
	final function csv($k = null, $s = null) {return df_csv_parse($this->v($k ?: df_caller_f(), $s));}

	/**
	 * 2016-08-04
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	function enable($s = null) {return $this->b(null, $s);}

	/**
	 * 2015-11-09
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @return int
	 */
	final function i($k = null, $s = null) {return df_int($this->v($k ?: df_caller_f(), $s));}

	/**
	 * 2015-12-26
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @return int
	 */
	final function nat($k = null, $s = null) {return df_nat($this->v($k ?: df_caller_f(), $s));}

	/**
	 * 2015-12-26
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @return int
	 */
	final function nat0($k = null, $s = null) {return df_nat0($this->v($k ?: df_caller_f(), $s));}

	/**
	 * 2015-12-07
	 * I have corrected the method, so it now returns null for an empty value
	 * (avoids to decrypt a null-value or an empty string).
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @param mixed|callable $d [optional]
	 * 2017-02-08
	 * Параметр $d нужен обязательно, потому что этот метод с этим параметром вызывается из
	 * @used-by \Df\Payment\Settings::testableGeneric()
	 * @return string|null
	 */
	final function p($k = null, $s = null, $d = null) {
		$r = $this->v($k ?: df_caller_f(), $s); /** @var string|mixed $r */
		return df_if2($r, df_encryptor()->decrypt($r), $d);
	}

	/**
	 * 2016-03-08
	 * @used-by v()
	 * @param null|string|int|S|array(string, int) $s [optional]
	 * @return null|string|int|S|array(string, int)
	 */
	final function scope($s = null) {return !is_null($s) ? $s : (
		df_is_backend() && df_is_system_config() ? df_store() : $this->scopeDefault()
	);}

	/**
	 * @used-by b()
	 * @used-by bv()
	 * @used-by csv()
	 * @used-by i()
	 * @used-by json()
	 * @used-by nat()
	 * @used-by nat0()
	 * @used-by nwb()
	 * @used-by nwbn()
	 * @used-by p()
	 * @used-by \Df\API\Settings::probablyTestable()
	 * @used-by \Df\Payment\Charge::description()
	 * @used-by \Df\Payment\ConfigProvider::configOptions()
	 * @used-by \Df\Payment\Method::s()
	 * @used-by \Df\Payment\Settings::applicableForQuoteByMinMaxTotal()
	 * @used-by \Df\Payment\Settings::description()
	 * @used-by \Df\Payment\Settings::messageFailure()
	 * @param string|null $k [optional]
	 * @param null|string|int|S|array(string, int) $s [optional]
	 * @param mixed|callable $d [optional]
	 * @return array|string|null|mixed
	 */
	final function v($k = null, $s = null, $d = null) {return df_cfg(
		$this->prefix() . '/' . self::phpNameToKey($k ?: df_caller_f()), $this->scope($s), $d
	);}

	/**
	 * 2016-07-31
	 * 2016-08-04
	 * Ошибочно писать здесь self::s($class)
	 * потому что класс ребёнка не обязательно должен быть наследником класса родителя:
	 * ему достаточно быть наследником @see \Df\Config\Settings
	 * @param string $c
	 * @return Settings
	 */
	final protected function child($c) {return self::s($this->_scope, $c);}

	/**
	 * 2017-03-27
	 * @used-by scope()
	 * @see \Df\Payment\Settings::scopeDefault()
	 * @return int|S|null|string
	 */
	protected function scopeDefault() {return $this->_scope;}

	/**
	 * 2019-01-12
	 * @used-by s()
	 * @see \Df\Config\Settings\Configurable::__construct()
	 * @see \Df\Payment\Settings::__construct()
	 * @param int|S|null|string $s
	 */
	private function __construct($s = null) {$this->_scope = $s;}

	/**
	 * 2015-12-16
	 * @param string|null $k [optional]
	 * @param null|string|int|S $s [optional]
	 * @return mixed[]
	 */
	private function json($k = null, $s = null) {return df_eta(@df_json_decode($this->v(
		$k ?: df_caller_f(), $s
	)));}

	/**
	 * 2019-01-11
	 * @used-by child()
	 * @used-by scopeDefault()
	 * @var int|S|null|string
	 */
	private $_scope;

	/**
	 * 2016-08-04
	 * 2016-11-25
	 * Замечание №1.
	 * Отныне метод возвращает класс не обязательно из базовой папки (например, \Df\Sso\Settings),
	 * а из папки с тем же окончанием, что и у вызываемого класса.
	 * Например, \Df\Sso\Settings\Button::convention() будет искать класс в папке Settings\Button
	 * модуля, к которому относится класс $c.
	 * Замечание №2.
	 * Используем 2 уровня кэширования, и оба они важны:
	 * 1) Кэширование self::s() приводит к тому, что вызов s() непосредственно для класса
	 * возвращает тот же объект, что и вызов convention(). Это очень важно.
	 * 2) Кэширование dfcf() позволяет нам не рассчитывать df_con_heir()
	 * при каждом вызове convention().
	 * 2017-03-27 Заменил @see df_con_heir() на df_con_hier()
	 * @used-by dfs()
	 * @param object|string $c
	 * @return self
	 */
	final static function convention($c) {return dfcf(function($c, $def) {return self::s(null, df_con_hier(
		$c, $def
	));}, [df_cts($c), static::class]);}

	/**
	 * 2016-07-12 http://php.net/manual/function.get-called-class.php#115790
	 * 2017-01-24
	 * Скопировал сюда метод @see \Df\Core\O::s(), чтобы избавиться от такого громоздкого
	 * (и, как я теперь считаю — неудачного) родителя.
	 * @used-by child()
	 * @used-by convention()
	 * @param S|int|null $s [optional]
	 * @param string $c [optional]
	 * @return self
	 */
	static function s($s = null, $c = null) {return dfcf(
		function($s, $c) {return new $c($s);}, [df_store($s), $c ?: static::class]
	);}

	/**
	 * 2016-12-24
	 * Теперь ключи могут начинаться с цифры (например: «3DS»).
	 * Методы PHP для таких ключей будут содержать приставку «_».
	 * Например, ключам «test3DS» и «live3DS» соответствует метод
	 * @used-by v()
	 * @used-by \Df\API\Settings::testableGeneric()
	 * @param string $name
	 * @return string
	 */
	final protected static function phpNameToKey($name) {return df_trim_left($name, '_');}
}