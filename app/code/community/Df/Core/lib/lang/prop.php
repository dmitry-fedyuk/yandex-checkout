<?php
/**
 * 2019-09-08
 * @used-by df_n_get()
 * @used-by df_n_set()
 * @used-by \Df\API\Client::logging()
 * @used-by \Df\API\FacadeOptions::resC()
 * @used-by \Df\API\FacadeOptions::silent()
 */
const DF_N = 'df-null';

/**
 * @used-by \Df\Qa\State::functionA()
 * @used-by \Df\Qa\State::method()
 * @used-by \Df\Xml\Parser\Entity::child()
 * @used-by \Df\Xml\Parser\Entity::descendS()
 * @used-by \Df\Xml\Parser\Entity::getAttributeInternal()
 * @used-by \Df\Xml\Parser\Entity::leaf()
 * @param mixed|string $v
 * @return mixed|null
 */
function df_n_get($v) {return DF_N === $v ? null : $v;}

/**
 * @used-by \Df\Qa\State::functionA()
 * @used-by \Df\Qa\State::method()
 * @used-by \Df\Xml\Parser\Entity::child()
 * @used-by \Df\Xml\Parser\Entity::descendS()
 * @used-by \Df\Xml\Parser\Entity::getAttributeInternal()
 * @used-by \Df\Xml\Parser\Entity::leaf()
 * @param mixed|null $v
 * @return mixed|string
 */
function df_n_set($v) {return is_null($v) ? DF_N : $v;}

/**
 * 2019-04-05
 * 2019-09-08 Now it supports static properties.
 * @used-by \Df\API\Client::logging()
 * @used-by \Df\API\FacadeOptions::resC()
 * @used-by \Df\API\FacadeOptions::silent()
 * @param object|null|\ArrayAccess $o
 * @param mixed|string $v
 * @param string|mixed|null $d [optional]
 * @param string|null $type [optional]
 * @return mixed|object|\ArrayAccess|null
 */
function df_prop($o, $v, $d = null, $type = null) {/** @var object|mixed|null $r */
	/**
	 * 2019-09-08
	 * 1) My 1st solution was comparing $v with `null`,
	 * but it is wrong because it fails for a code like `$object->property(null)`.
	 * 2) My 2nd solution was using @see func_num_args():
	 * «How to tell if optional parameter in PHP method/function was set or not?»
	 * https://stackoverflow.com/a/3471863
	 * It is wrong because the $v argument is alwaus passed to df_prop()
	 */
	$isGet = DF_N === $v; /** @vae bool $isGet */
	if ('int' === $d) {
		$type = $d; $d = null;
	}
	/** @var string $k */
	if (is_null($o)) { // 2019-09-08 A static call.
		$k = df_caller_m();
		static $s; /** @var array(string => mixed) $s */
		if ($isGet) {
			$r = dfa($s, $k, $d);
		}
		else {
			$s[$k] = $v;
			$r = null;
		}
	}
	else {
		$k = df_caller_f();
		if ($o instanceof \ArrayAccess) {
			if ($isGet) {
				$r = !$o->offsetExists($k) ? $d : $o->offsetGet($k);
			}
			else {
				$o->offsetSet($k, $v);
				$r = $o;
			}
		}
		else {
			$a = '_' . __FUNCTION__; /** @var string $a */
			if (!isset($o->$a)) {
				$o->$a = [];
			}
			if ($isGet) {
				$r = dfa($o->$a, $k, $d);
			}
			else {
				($o->$a)[$k] = $v;
				$r = $o;
			}
		}
	}
	return $isGet && 'int' === $type ? intval($r) : $r;
}