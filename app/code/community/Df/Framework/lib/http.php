<?php

/**
 * 2015-01-28
 * По примеру @see df_handle_entry_point_exception()
 * добавил условие @uses Mage::getIsDeveloperMode()
 * потому что Magento выводит диагностические сообщения на экран
 * только при соблюдении этого условия.
 * 2016-07-31
 * К сожалению, мы не можем указывать кодировку в обработчике,
 * установленном @see set_exception_handler(),
 * потому что @see set_exception_handler() в Magento работать не будет
 * из-за глобального try..catch в методе @see Mage::run()
 * @used-by df_error()
 * @used-by df_error_html()
 */
function df_header_utf() {headers_sent() ?: header('Content-Type: text/html; charset=UTF-8');}

/**
 * 2017-02-26      
 * @used-by df_response_sign()
 * @param array(string => string) $a [optional]
 * @return array(string => string)
 */
function df_headers(array $a = []) {return dfa_key_transform($a + [
	'Author' => 'Dmitry Fedyuk', 'EMail' => 'admin@mage2.pro', 'Website' => 'https://mage2.pro'
], function($k) {return "X-Mage2.PRO-{$k}";});}

/**
 * 2019-11-08
 * @used-by df_visitor_ip()
 * @return Mage_Core_Helper_Http
 */
function df_http_h() {return Mage::helper('core/http');}

/**
 * 2015-11-27
 * Note 1.
 * Google API в случае сбоя возвращает корректный JSON, но с кодом HTTP 403,
 * что приводит к тому, что @see file_get_contents() не просто возвращает JSON,
 * а создаёт при этом warning.
 * Чтобы при коде 403 warning не создавался, использую ключ «ignore_errors»:
 * http://php.net/manual/en/context.http.php#context.http.ignore-errors
 * http://stackoverflow.com/a/21976746
 * Note 2.
 * Обратите внимание, что для использования @uses file_get_contents
 * с адресами https требуется расширение php_openssl интерпретатора PHP,
 * однако оно является системным требованием Magento 2:
 * http://devdocs.magento.com/guides/v2.0/install-gde/system-requirements.html#required-php-extensions
 * Поэтому мы вправе использовать здесь @uses file_get_contents
 * Note 3.
 * The function returns the read data or FALSE on failure.
 * http://php.net/manual/function.file-get-contents.php
 *
 * 2016-05-31
 * Стандартное время ожидание ответа сервера задаётся опцией default_socket_timeout:
 * http://php.net/manual/en/filesystem.configuration.php#ini.default-socket-timeout
 * Её значение по-умолчанию равно 60 секундам.
 * Конечно, при оформлении заказа негоже заставлять покупателя ждать 60 секунд
 * только ради узнавания его страны вызовом @see df_visitor()
 * Поэтому добавил возможность задавать нестандартное время ожидания ответа сервера:
 * http://stackoverflow.com/a/10236480
 * https://amitabhkant.com/2011/08/21/using-timeouts-with-file_get_contents-in-php/
 *
 * @used-by df_http_json()
 *
 * @param $urlBase
 * @param array(string => string) $params [optional]
 * @param int|null $timeout [optional]
 * @return string|bool
 */
function df_http_get($urlBase, array $params = [], $timeout = null) {
	$url = !$params ? $urlBase : $urlBase . '?' . http_build_query($params); /** @var string $url */
	/**
	 * 2016-05-31
	 * @uses file_get_contents() может возбудить Warning:
	 * «failed to open stream: A connection attempt failed
	 * because the connected party did not properly respond after a period of time,
	 * or established connection failed because connected host has failed to respond.»
	 */
	return @file_get_contents($url, null, stream_context_create(['http' => df_clean([
		'ignore_errors' => true, 'timeout' => $timeout
	])]));
}

/**
 * 2016-04-13
 * @see df_request_body_json()
 * @used-by \Df\Core\Visitor::responseA()
 * @param string $urlBase
 * @param array(string => string) $params [optional]
 * @param int|null $timeout [optional]
 * @return array(string => mixed)
 */
function df_http_json($urlBase, array $params = [], $timeout = null) {return
	/** @var string|bool $json */ /** @var bool|array|null $r */
	false === ($json = df_http_get($urlBase, $params, $timeout))
	|| !is_array($r = df_json_decode($json))
	? [] : $r
;}

/**
 * 2018-11-23 https://stackoverflow.com/a/53446950
 * @return bool
 */
function df_is_google_page_speed() {return df_request_ua('Chrome-Lighthouse');}

/**
 * @used-by df_scope()
 * @used-by df_store()
 * @used-by \Df\Framework\Request::clean()
 * @used-by \Df\Framework\Request::extra()
 * @used-by \Df\Framework\Request::extraKeysRaw()
 * @param string|string[]|null $k [optional]
 * @param string|null|callable $d [optional]
 * @return string|array(string => string)
 */
function df_request($k = null, $d = null) {$o = df_request_o(); return is_null($k) ? $o->getParams() : (
	is_array($k) ? dfa($o->getParams(), $k) : df_if1(is_null($r = $o->getParam($k)) || '' === $r, $d, $r)
);}

/**              
 * 2017-03-09
 * @used-by df_request_body_json()
 * @return string|false
 */
function df_request_body() {return file_get_contents('php://input');}

/**
 * 2017-03-09
 * @see df_http_json()
 * @return string
 */
function df_request_body_json() {return !($j = df_request_body()) ? [] : df_json_decode($j);}

/**
 * 2016-12-25
 * The @uses \Zend_Http_Request::getHeader() method is insensitive to the argument's letter case:
 * @see \Zend_Http_Request::createKey()
 * https://github.com/zendframework/zendframework/blob/release-2.4.6/library/Zend/Http/Headers.php#L462-L471
 * @used-by df_request_header()
 * @used-by df_request_ua()
 * @param string $k
 * @return string|false
 */
function df_request_header($k) {return df_request_o()->getHeader($k);}

/**
 * 2015-08-14
 * https://github.com/magento/magento2/issues/1675
 * @used-by df_action_name()
 * @used-by df_is_ajax()
 * @used-by df_request()
 * @used-by df_request_header()
 * @return \Mage_Core_Controller_Request_Http
 */
function df_request_o() {return Mage::app()->getRequest();}

/**
 * 2016-12-25
 * 2017-02-18
 * Модуль Checkout.com раньше использовал dfa($_SERVER, 'HTTP_USER_AGENT')   
 * @used-by df_is_google_page_speed()
 * @param string $s [optional]
 * @return string|bool
 */
function df_request_ua($s = '') {
	$r = df_request_header('user-agent'); /** @var string $r */
	return '' === $s ? $r : df_contains($r, $s);
}