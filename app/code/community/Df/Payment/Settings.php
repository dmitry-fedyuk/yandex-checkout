<?php
namespace Df\Payment;
use Df\Payment\Method as M;
use Mage_Core_Model_Store as S;
// 2017-02-15
/** @see \Df\YandexCheckout\Settings */
abstract class Settings extends \Df\API\Settings {
	/**
	 * 2017-03-27
	 * @override
	 * @see \Df\Config\Settings::__construct()
	 * @used-by \Df\Payment\Method::s()
	 * @param M $m
	 */
	final function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2017-03-27
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @return M
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2016-08-25
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	protected function prefix() {return dfc($this, function() {return 'payment/' . dfpm_code_short($this->_m);});}

	/**
	 * 2017-03-27
	 * @override
	 * @see \Df\Config\Settings::scopeDefault()
	 * @used-by \Df\Config\Settings::scope()
	 * @return int|S|null|string
	 */
	protected function scopeDefault() {return $this->_m->getStore();}

	/**
	 * 2017-03-27
	 * @used-by __construct()
	 * @used-by m()
	 * @used-by prefix()
	 * @used-by scopeDefault()
	 * @var M
	 */
	private $_m;
}