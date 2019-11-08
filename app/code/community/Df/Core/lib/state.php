<?php
use Mage_Core_Model_Store as Store;

/**
 * @used-by df_action_name()
 * @return Mage_Core_Controller_Varien_Action|null
 */
function df_controller() {return df_state()->getController();}

/**
 * 2016-03-09
 * I have ported it from my «Russian Magento» product for Magento 1.x: http://magento-forum.ru
 * @uses df_store_url_web() returns an empty string
 * if the store's root URL is absent in the Magento database.
 * 2017-03-15
 * It returns null only if the both conditions are true:
 * 1) Magento runs from the command line (by Cron or in console).
 * 2) The store's root URL is absent in the Magento database.
 * @used-by df_sentry()
 * @used-by dfe_modules_log()
 * @used-by dfp_refund()
 * @param int|string|null|bool|Store $s [optional]
 * @param bool $www [optional]
 * @return string|null
 */
function df_domain_current($s = null, $www = false) {return dfcf(function($s = null, $www = false) {return
	!($base = df_store_url_web($s)) || !($r = df_domain($base, false)) ? null : (
		$www ? $r : df_trim_text_left($r, 'www.')
	)
;}, func_get_args());}

/**
 * https://mage2.ru/t/94
 * https://mage2.pro/t/59
 * @return bool
 */
function df_is_ajax() {static $r; return !is_null($r) ? $r : $r = df_request_o()->isXmlHttpRequest();}

/**
 * 2015-12-09
 * https://mage2.pro/t/299
 * @return bool
 */
function df_is_dev() {return Mage::getIsDeveloperMode();}

/**
 * 2016-05-15 http://stackoverflow.com/a/2053295
 * 2017-06-09 It intentionally returns false in the CLI mode.
 * @return bool
 */
function df_is_localhost() {return in_array(dfa($_SERVER, 'REMOTE_ADDR', []), ['127.0.0.1', '::1']);}

/**
 * 2016-12-22
 * @return bool
 */
function df_is_windows() {return dfcf(function() {return 'WIN' === strtoupper(substr(PHP_OS, 0, 3));});}

/**
 * 2016-06-25
 * @used-by df_sentry()
 */
function df_magento_version() {return dfcf(function() {return Mage::getVersion();});}

/**
 * 2017-04-17
 * @return bool
 */
function df_my() {return isset($_SERVER['DF_DEVELOPER']);}

/**
 * 2017-06-09 «dfediuk» is the CLI user name on my localhost. 
 * @used-by df_webhook()
 * @return bool
 */
function df_my_local() {return dfcf(function() {return
	df_my() && (df_is_localhost() || 'dfediuk' === dfa($_SERVER, 'USERNAME'))
;});}

/**
 * @used-by df_controller()
 * @return \Df\Core\State
 */
function df_state() {static $r; return $r ?: $r = \Df\Core\State::s();}