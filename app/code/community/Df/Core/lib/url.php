<?php
use Exception as E;
use Mage_Core_Model_Store as Store;

/**
 * @param array(string => mixed) $params [optional]
 * @return array(string => mixed)
 */
function df_adjust_route_params(array $params = []) {return ['_nosid' => true] + $params;}

/**
 * 2016-07-12
 * @used-by df_webhook()
 * @param string $u
 * @param string|E $msg [optional]
 * @return string
 * @throws E
 */
function df_assert_https($u, $msg = null) {return df_check_https_strict($u) ? $u : df_error(
	$msg ?: "The URL «{$u}» is invalid, because the system expects an URL which starts with «https://»."
);}

/**
 * 2016-07-16
 * @param string $u
 * @return bool
 */
function df_check_https($u) {return df_starts_with(strtolower($u), 'https');}

/**
 * 2016-05-30
 * http://framework.zend.com/manual/1.12/en/zend.uri.chapter.html#zend.uri.instance-methods.getscheme
 * @uses \Zend_Uri::getScheme() always returns a lowercased value:
 * @see \Zend_Uri::factory()
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Uri.php#L100
 * $scheme = strtolower($uri[0]);
 * @param string $u
 * @return bool
 */
function df_check_https_strict($u) {return 'https' === df_zuri($u)->getScheme();}

/**
 * http://stackoverflow.com/a/15011528
 * http://www.php.net/manual/en/function.filter-var.php
 * Обратите внимание, что
 * filter_var('/C/A/CA559AWLE574_1.jpg', FILTER_VALIDATE_URL) вернёт false
 * @param $s $string
 * @return bool
 */
function df_check_url($s) {return false !== filter_var($s, FILTER_VALIDATE_URL);}

/**
 * 2017-10-16
 * @used-by df_asset_create()
 * @used-by df_js()
 * @param string $u
 * @return bool
 */
function df_check_url_absolute($u) {return df_starts_with($u, ['http', '//']);}

/**
 * http://mage2.ru/t/37
 * @return string
 */
function df_current_url() {return df_url_h()->getCurrentUrl();}

/**
 * 2017-05-12
 * @used-by df_domain_current()
 * @used-by ikf_pw_carrier()
 * @used-by Dfe_PortalStripe::view/frontend/templates/page/customers.phtml
 * @param string $u
 * @param bool $www [optional]
 * @param bool $throw [optional]
 * @return string|null
 * @throws \Zend_Uri_Exception
 */
function df_domain($u, $www = false, $throw = true) {return
	!($r = df_zuri($u, $throw)->getHost()) ? null : ($www ? $r : df_trim_text_left($r, 'www.'))
;}

/**
 * 2015-11-28
 * 2019-08-25
 * You can pass query parameters as `df_url($path, ['_query' => [...]])`
 * https://magento.stackexchange.com/a/201787
 * https://github.com/inkifi/map/blob/0.0.4/view/frontend/templates/index/section/2/cities.phtml#L4
 * @used-by df_url_checkout_success()
 * @used-by vendor/wolfautoparts.com/filter/view/frontend/templates/sidebar.phtml
 * @param string|null $path [optional]
 * @param array(string => mixed) $p [optional]
 * @return string
 */
function df_url($path = null, array $p = []) {return df_url_o()->getUrl($path, df_adjust_route_params($p));}

/**
 * 2015-11-28
 * @used-by df_url_backend_ns()
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url_backend($path = null, array $params = []) {return df_url_trim_index(df_url_backend_o()->getUrl(
	$path, df_adjust_route_params($params)
));}

/**
 * 2016-08-24
 * @used-by df_customer_backend_url()
 * @used-by df_order_backend_url()
 * @used-by dfe_modules_log()
 * @used-by df_cm_backend_url()
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url_backend_ns($path = null, array $params = []) {return df_url_backend(
	$path, ['_nosecret' => true] + $params
);}

/** @return \Mage_Adminhtml_Model_Url */
function df_url_backend_o() {return Mage::getModel('adminhtml/url');}

/**
 * 2016-05-31
 * @param string $u
 * @return string
 */
function df_url_base($u) {return df_first(df_url_bp($u));}

/**
 * 2017-02-13
 * «https://mage2.pro/sandbox/dfe-paymill» => [«https://mage2.pro»,  «sandbox/dfe-paymill»]
 * @used-by df_url_base()
 * @used-by df_url_path()
 * @used-by df_url_trim_index()
 * @param string $u
 * @return string[]
 */
function df_url_bp($u) {
	/** @var string $base */ /** @var string $path */
	if (!df_check_url($u)) {
		list($base, $path) = ['', $u];
	}
	else {
		$z = df_zuri($u); /** @var \Zend_Uri_Http $z */
		$base = df_ccc(':', "{$z->getScheme()}://{$z->getHost()}", dftr($z->getPort(), ['80' => '']));
		$path = df_trim_ds($z->getPath());
	}
	return [$base, $path];
}

/**
 * 2015-11-28
 * 2016-12-01 If $path is null, '', or '/', then the function will return the frontend root URL.
 * 2016-12-01 On the frontend side, the @see df_url() behaves identical to df_url_frontend()
 * @used-by df_webhook()
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @param Store|int|string|null $store [optional]
 * @return string
 */
function df_url_frontend($path = null, array $params = [], $store = null) {return df_url_trim_index(
	df_url_frontend_o()->getUrl($path,
		df_adjust_route_params($params) + (is_null($store) ? [] : ['_store' => df_store($store)])
	)
);}

/** @return Mage_Core_Model_Url */
function df_url_frontend_o() {return new Mage_Core_Model_Url;}

/** @return Mage_Core_Helper_Url */
function df_url_h() {return Mage::helper('core/url');}

/** @return Mage_Core_Model_Url|Mage_Adminhtml_Model_Url */
function df_url_o() {return df_is_backend() ? df_url_backend_o() : df_url_frontend_o();}

/**
 * 2019-01-12
 * @used-by \Df\API\Client::_p()
 * @param string $u
 * @return string
 */
function df_url_path($u) {return df_last(df_url_bp($u));}

/**
 * 2018-05-11
 * df_contains(df_url(), $s)) does not work properly for some requests.
 * E.g.: df_url() for the `/us/stores/store/switch/___store/uk` request will return `<website>/us/`
 * @see df_action_has()
 * @see df_action_is()
 * @param string $s
 * @return bool
 */
function df_url_path_contains($s) {return df_contains(dfa($_SERVER, 'REQUEST_URI'), $s);}

/**
 * 2017-01-22
 * @used-by dfp_url_api()
 * @param bool $test
 * @param string $tmpl
 * @param string[] $names
 * @param mixed[] ...$args [optional]
 * @return string
 */
function df_url_staged($test, $tmpl, array $names, ...$args) {
	$r = str_replace('{stage}', $test ? df_first($names) : df_last($names), $tmpl); /** @var string $r */
	/**
	 * 2017-09-10
	 * I have added $args condition here, because the «QIWI Wallet» module does not have args here,
	 * and it has $tmpl like:
	 * https://bill.qiwi.com/order/external/main.action?failUrl=https%3A%2F%2Fmage2.pro%2Fsandbox%2Fdfe-qiwi%2FcustomerReturn%3Ffailure%3D1&iframe=0&pay_source=&shop=488380&successUrl=https%3A%2F%2Fmage2.pro%2Fsandbox%2Fdfe-qiwi%2FcustomerReturn&target=&transaction=ORD-2017%2F09-01090
	 * Such $tmpl will lead @see sprintf() to fail.
	 */
	return !$args ? $r : sprintf($r, ...$args);
}

/**
 * 2017-02-13 Убираем окончания «/», «index/» и «index/index/».
 * @used-by df_url_frontend()
 * @param string $u
 * @return string
 */
function df_url_trim_index($u) {
	list($base, $path) = df_url_bp($u); /** @var string $base */ /** @var string $path */
	$a = df_explode_path($path); /** @var string[] $a */
	$i = count($a) - 1; /** @var int $i */
	while ($a && in_array($a[$i--], ['', 'index'], true)) {array_pop($a);}
	return df_cc_path($base, df_cc_path($a));
}

/**
 * 2016-05-30
 * @used-by df_domain()
 * @param string $u
 * @param bool $throw [optional]
 * @return \Zend_Uri|\Zend_Uri_Http
 * @throws \Zend_Uri_Exception
 */
function df_zuri($u, $throw = true) {
	try {
		/** @var \Zend_Uri_Http $result */
		$result = \Zend_Uri::factory($u);
	}
	catch (\Zend_Uri_Exception $e) {
		if ($throw) {
			throw $e;
		}
		$result = null;
	}
	return $result;
}