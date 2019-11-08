<?php
namespace Df\API;
/**
 * 2017-07-13
 */
class Document implements \ArrayAccess {
	/**
	 * 2017-07-13
	 * @used-by ikf_api_oi()
	 * @used-by \Df\API\Facade::p()
	 * @param array(string => mixed) $a [optional]
	 */
	function __construct(array $a = []) {$this->_a = $a;}

	/**
	 * 2017-07-13
	 * @used-by \Df\API\Operation::a()
	 * @used-by \Df\API\Operation::req()
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function a($k = null, $d = null) {return dfak($this->_a, $k, $d);}

	/**
	 * 2017-07-13
	 * @used-by \Df\API\Operation::j()
	 * @return string
	 */
	function j() {return df_json_encode($this->_a);}

	/**
	 * 2017-07-13
	 * «This method is executed when using isset() or empty() on objects implementing ArrayAccess.
	 * When using empty() ArrayAccess::offsetGet() will be called and checked if empty
	 * only if ArrayAccess::offsetExists() returns TRUE».
	 * http://php.net/manual/arrayaccess.offsetexists.php
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @used-by df_prop()
	 * @param string $k
	 * @return bool
	 */
	function offsetExists($k) {return !is_null(dfa_deep($this->_a, $k));}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @used-by df_prop()
	 * @param string $k
	 * @return array(string => mixed)|mixed|null
	 */
	function offsetGet($k) {return dfa_deep($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @used-by df_prop()
	 * @param string $k
	 * @param mixed $v
	 */
	function offsetSet($k, $v) {dfa_deep_set($this->_a, $k, $v);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	function offsetUnset($k) {dfa_deep_unset($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @used-by __construct()
	 * @used-by a()
	 * @var array(string => mixed)
	 */
	private $_a;
}