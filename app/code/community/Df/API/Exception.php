<?php
namespace Df\API;
/**
 * 2017-07-09
 * Unfortunately, PHP allows to throw only the @see \Exception descendants.
 * @see \Df\API\Exception\HTTP
 * @see \Df\API\Response\Validator
 */
abstract class Exception extends \Df\Core\Exception {
	/**
	 * 2017-07-09
	 * @used-by short()
	 * @used-by \Df\API\Client::_p()
	 * @see \Df\API\Exception\HTTP::long()
	 * @see \Df\API\Response\Validator::long()
	 * @return string
	 */
	abstract function long();

	/**
	 * 2017-07-09
	 * @used-by \Df\API\Client::_p()
	 * @return string
	 */
	function short() {return $this->long();}
}