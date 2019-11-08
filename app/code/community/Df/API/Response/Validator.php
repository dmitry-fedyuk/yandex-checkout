<?php
namespace Df\API\Response;
use Df\API\Exception;
/**
 * 2017-07-05
 */
abstract class Validator extends Exception {
	/**
	 * 2017-07-06
	 * @used-by \Df\API\Client::_p()
	 * @return bool
	 */
	abstract function valid();

	/**
	 * 2017-07-06
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @used-by \Df\API\Client::p()
	 * @param mixed $r
	 */
	final function __construct($r) {$this->_r = $r;}

	/**
	 * 2017-12-03
	 * @override
	 * @see \Df\API\Exception::long()
	 * @used-by \Df\API\Client::_p()
	 * @return string
	 */
	function long() {return df_json_encode($this->_r);}

	/**
	 * 2017-07-06
	 * @param string|null $k [optional]
	 * @return mixed
	 */
	final protected function r($k = null) {return is_null($k) ? $this->_r : dfa($this->_r, $k);}

	/**
	 * 2017-07-06
	 * @used-by __construct()
	 * @used-by long()
	 * @used-by r()
	 * @var mixed
	 */
	private $_r;
}