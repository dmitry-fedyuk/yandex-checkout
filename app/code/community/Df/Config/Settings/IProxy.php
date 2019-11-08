<?php
namespace Df\Config\Settings;
/**
 * 2019-01-14
 * @used-by \Df\API\Client::proxy()
 */
interface IProxy {
	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @return string
	 */
	function host();

	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @return string
	 */
	function password();

	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @return string
	 */
	function port();

	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @return string
	 */
	function username();
}