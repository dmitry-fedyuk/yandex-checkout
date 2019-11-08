<?php
namespace Df\API;
use Df\API\Exception as E;
use Df\API\Exception\HTTP as eHTTP;
use Df\API\Response\Validator;
use Df\Config\Settings\IProxy;
use Df\Core\Exception as DFE;
use Df\Zf\Http\Client\Adapter\Proxy as aProxy;
use Mage_Core_Model_Store as Store;
use Zend\Filter\FilterChain;
use Zend\Filter\FilterInterface as IFilter;
use Zend_Http_Client as C;
use Zend_Http_Client_Adapter_Socket as aSocket;
/**
 * 2017-07-05
 */
abstract class Client {
	/**
	 * 2017-07-05
	 * @used-by __construct()
	 * @used-by url()
	 * @return string
	 */
	abstract protected function urlBase();

	/**
	 * 2017-07-02
	 * @used-by \Df\API\Facade::p()
	 * @param string $path
	 * @param string|array(string => mixed) $p [optional]
	 * @param string|null $method [optional]
	 * @param array(string => mixed) $zfConfig [optional]
	 * @param Store $s [optional]
	 * @throws DFE
	 */
	final function __construct($path, $p = [], $method = null, array $zfConfig = [], Store $s = null) {
		$this->_path = $path;
		$this->_store = df_store($s);
		$this->_c = $this->setup($zfConfig + $this->zfConfig());
		$this->_method = $method = $method ?: C::GET;
		$this->_c->setMethod($this->_method);
		$this->_filtersReq = new FilterChain;
		$this->_filtersResAV = new FilterChain;
		$this->_filtersResBV = new FilterChain;
		$this->_construct();
		$p += $this->commonParams($path);
		C::GET === $method ? $this->_c->setParameterGet($p) : (
			is_array($p = $this->_filtersReq->filter($p)) ? $this->_c->setParameterPost($p) :
				$this->_c->setRawData($p)
		);
		if (!$this->destructive()) {
			/**
			 * 2017-07-06
			 * @uses urlBase() is important here, because the rest cache key parameters can be the same
			 * for multiple APIs (e.g. for Zoho Books and Zoho Inventory).
			 * 2017-07-07
			 * @uses headers() is important here, because the response can depend on the HTTP headers
			 * (it is true for Microsoft Dynamics 365 and Zoho APIs,
			 * because the authentication token is passed through the headers).
			 */
			$this->_ckey = df_hash_a([$this->urlBase(), $path, $method, $p, $this->headers()]);
		}
	}

	/**
	 * 2019-04-24
	 * @used-by _p()
	 * @param bool|null|string $v [optional]
	 * @return $this|bool
	 */
	final function logging($v = DF_N) {return df_prop($this, $v, false);}

	/**
	 * 2017-06-30
	 * @used-by \Df\API\Facade::p()
	 * @throws DFE
	 * @return mixed|null
	 */
	final function p() {
		$tag = df_cts($this, '_'); /** @var string $tag */
		if ($d = $this->destructive()) { /** @var bool $d */
			df_cache_clean_tag($tag);
		}
		return $d ? $this->_p() : df_cache_get_simple($this->_ckey, function() {return $this->_p();}, [$tag]);
	}

	/**
	 * 2019-01-12 It is used by the Inkifi_Mediaclip module.
	 * @used-by \Df\API\Facade::p()
	 */
	final function silent() {$this->_silent = true;}

	/**
	 * 2017-07-06
	 * @used-by __construct()
	 */
	protected function _construct() {}

	/**
	 * 2017-07-06
	 * @used-by resJson()
	 * @param callable|IFilter $f
	 * @param int $p
	 */
	final protected function addFilterResBV($f, $p = FilterChain::DEFAULT_PRIORITY) {
		$this->_filtersResBV->attach($f, $p);
	}

	/**
	 * 2017-07-08
	 * @used-by __construct()
	 * @param string $path
	 * @return array(string => mixed)
	 */
	protected function commonParams($path) {return [];}

	/**
	 * 2017-07-05
	 * @used-by __construct()
	 * @used-by _p()
	 * @return array(string => string)
	 */
	protected function headers() {return [];}

	/**
	 * 2017-08-10
	 * @return string
	 */
	final protected function method() {return $this->_method;}

	/**
	 * 2017-12-02
	 * @return string
	 */
	final protected function path() {return $this->_path;}

	/**
	 * 2019-01-14
	 * @used-by setup()
	 * @return IProxy|null
	 */
	protected function proxy() {return null;}

	/**
	 * 2017-07-13
	 */
	final protected function reqJson() {$this->addFilterReq('df_json_encode');}

	/**
	 * 2018-12-18
	 * @param string $tag
	 * @param array(string => mixed) $p [optional]
	 */
	final protected function reqXml($tag, array $p = []) {$this->addFilterReq(
		function(array $a) use($tag, $p) {return df_xml_g($tag, $a, $p);}
	);}

	/**
	 * 2017-07-06
	 */
	final protected function resJson() {$this->addFilterResBV('df_json_decode');}

	/**
	 * 2017-07-05 A descendant class can return null if it does not need to validate the responses.
	 * @used-by p()
	 * @return string
	 */
	protected function responseValidatorC() {return null;}

	/**
	 * 2019-04-04
	 * @param string $k
	 */
	final protected function resPath($k) {$this->addFilterResAV(function(array $a) use($k) {return
		dfa_deep($a, $k, $a)
	;});}

	/**
	 * 2017-10-08
	 * Some APIs return their results with a non-important root tag, which is uses only as a syntax sugar.
	 * Look at the Square Connect API v2, for example: https://docs.connect.squareup.com/api/connect/v2
	 * A response to `GET /v2/locations` looks like:
	 *		{"locations": [{<...>}, {<...>}, {<...>}]}
	 * [Square] An example of a response to `GET /v2/locations`: https://mage2.pro/t/4647
	 * The root `locations` tag is just a syntax sugar, so it is convenient to strip it.
	 * @uses df_first()
	 */
	final protected function resStripRoot() {$this->addFilterResAV('df_first');}

	/**
	 * 2019-01-11
	 * @return Store
	 */
	final protected function store() {return $this->_store;}

	/**
	 * 2017-12-02
	 * 2018-11-11
	 * $this->_path can be empty, and we do not want an ending slash in this case,
	 * what is why we use @uses df_cc_path().
	 * @used-by _p()
	 * @return string
	 */
	protected function url() {return df_cc_path($this->urlBase(), $this->_path);}

	/**
	 * 2018-11-11
	 * @used-by setup()
	 */
	protected function verifyCertificate() {return true;}

	/**
	 * 2018-11-11
	 * We have also @see \Df\API\Facade::zfConfig()
	 * *) Use \Df\API\Client::zfConfig()
	 * if you need to provide a common configuration for all API requests.
	 * *) Use \Df\API\Facade::zfConfig()
	 * if you need to provide a custom configuration for an API request group.
	 * @used-by __construct()
	 * @return array(string => mixed)
	 */
	protected function zfConfig() {return [];}

	/**
	 * 2017-08-10
	 * @used-by p()
	 * @throws DFE
	 * @return mixed|null
	 */
	private function _p() {
		$c = $this->_c; /** @var C $c */
		$c->setHeaders($this->headers());
		$c->setUri($this->url());
		try {
			$res = $c->request(); /** @var \Zend_Http_Response $res */
			if (!($resBody = $res->getBody()) && $res->isError()) { /** @var string $resBody */
				throw new eHTTP($res);
			}
			else {
				/** @var mixed|null $r */
				// 2017-08-08
				// «No Content»
				// «The server has successfully fulfilled the request
				// and that there is no additional content to send in the response payload body»
				// https://httpstatuses.com/204
				if (!$resBody && 204 === $res->getStatus()) {
					$r = null;
				}
				else {
					$r = $this->_filtersResBV->filter($resBody);
					if ($validatorC = $this->responseValidatorC() /** @var string $validatorC */) {
						$validator = new $validatorC($r); /** @var Validator $validator */
						// 2019-01-12
						// I have added `$res->isError() ||` today
						// because a 4xx or a 5xx HTTP code clearly indicates an error.
						// I have use this feature in the Inkifi_Mediaclip module.
						if ($res->isError() || !$validator->valid()) {
							throw $validator;
						}
					}
					$r = $this->_filtersResAV->filter($r);
				}
			}
			if ($this->logging()) {
				$req = df_zf_http_last_req($c); /** @var string $req */
				/** @var string $m */ /** @var string $title */
				$title = df_api_name($m = df_module_name($this));
				$path = df_url_path($this->url()); /** @var string $path */
				df_log_l($m,
					(!$path ? 'A' : "A `{$path}`") . " {$title} API request has succeeded\n"
					. "Request:\n$req\nResponse:\n"
					. (!df_check_json($resBody) ? $resBody : df_json_prettify($resBody))
				);
			}
		}
		catch (\Exception $e) {
			/** @var string $long */ /** @var string $short */
			list($long, $short) = $e instanceof E ? [$e->long(), $e->short()] : [null, df_ets($e)];
			$req = df_zf_http_last_req($c); /** @var string $req */
			$title = df_api_name($m = df_module_name($this)); /** @var string $m */ /** @var string $title */
			$path = df_url_path($this->url()); /** @var string $path */
			$ex = df_error_create(
				(!$path ? 'A' : "A `{$path}`")
				. " {$title} API request has failed"
				. ($short ? ": «{$short}»" : ' without error messages') . ".\n"
				. ($long === $short ? "Request:\n$req" : df_format_kv([
					'The full error description' => $long, 'Request' => $req
				]))
			); /** @var DFE $ex */
			if (!$this->_silent) {
				df_log_l($m, $ex);
				df_sentry($m, $short, ['extra' => ['Request' => $req, 'Response' => $long]]);
			}
			throw $ex;
		}
		return $r;
	}

	/**
	 * 2017-07-13
	 * @used-by reqJson()
	 * @param callable|IFilter $f
	 * @param int $p
	 */
	private function addFilterReq($f, $p = FilterChain::DEFAULT_PRIORITY) {
		$this->_filtersReq->attach($f, $p);
	}

	/**
	 * 2017-10-08
	 * @used-by resPath()
	 * @used-by resStripRoot()
	 * @param callable|IFilter $f
	 * @param int $p
	 */
	private function addFilterResAV($f, $p = FilterChain::DEFAULT_PRIORITY) {$this->_filtersResAV->attach($f, $p);}

	/**
	 * 2017-10-08
	 * Adds $f at the lowest priority (it will be applied after all other filters).
	 * Currently, it is not used.
	 * @param callable|IFilter $f
	 */
	private function appendFilterResAV($f) {$this->_filtersResAV->attach(
		$f, df_zf_pq_min($this->_filtersResAV->getFilters()) - 1
	);}

	/**
	 * 2017-07-07
	 * Adds $f at the lowest priority (it will be applied after all other filters).
	 * Currently, it is not used.
	 * @param callable|IFilter $f
	 */
	private function appendFilterResBV($f) {$this->_filtersResBV->attach(
		$f, df_zf_pq_min($this->_filtersResBV->getFilters()) - 1
	);}

	/**
	 * 2017-08-10
	 * @used-by __construct()
	 * @used-by p()
	 * @return bool
	 */
	private function destructive() {return C::GET !== $this->_method;}

	/**
	 * 2019-01-14
	 * @used-by __construct()
	 * @param array(string => mixed) $config
	 * @return C
	 */
	private function setup(array $config) {
		$r = new C(null, $config + [
			'timeout' => 120
			/**
			 * 2017-07-16
			 * By default it is «Zend_Http_Client»: @see C::$config
			 * https://github.com/magento/zf1/blob/1.13.1/library/Zend/Http/Client.php#L126
			 */
			,'useragent' => 'Mage2.PRO'
		]); /** @var C $r */
		/** @var aProxy|aSocket $a */
		if (!($p = $this->proxy())) {  /** @var IProxy $p */
			$a = new aSocket;
		}
		else {
			// 2019-01-14
			// https://framework.zend.com/manual/1.12/en/zend.http.client.adapters.html#zend.http.client.adapters.proxy
			$a = new aProxy;
			$r->setConfig([
				'proxy_host' => $p->host()
				,'proxy_pass' => $p->password()
				,'proxy_port' => $p->port()
				,'proxy_user' => $p->username()
			]);
		}
		if ($p || !$this->verifyCertificate()) {
			$ssl = ['allow_self_signed' => true, 'verify_peer' => false]; /** @var array(string => bool) $ssl */
			if ($p) {
				// 2019-01-14 It is needed for my proxy: https://stackoverflow.com/a/32047219
				$ssl['verify_peer_name'] = false;
			}
			$a->setStreamContext(['ssl' => $ssl]);
		}
		$r->setAdapter($a);
		return $r;
	}

	/**
	 * 2017-07-02
	 * @used-by __construct()
	 * @used-by p()
	 * @var C
	 */
	private $_c;

	/**
	 * 2017-07-02
	 * @used-by __construct()
	 * @used-by p()
	 * @var string
	 */
	private $_ckey;

	/**
	 * 2017-07-13
	 * @used-by __construct()
	 * @used-by addFilterReq()
	 * @used-by p()
	 * @var FilterChain
	 */
	private $_filtersReq;

	/**
	 * 2017-10-08 This filter chain is applied to a result after the result validation.
	 * @used-by __construct()
	 * @used-by addFilterResAV()
	 * @used-by appendFilterResAV()
	 * @used-by p()
	 * @var FilterChain
	 */
	private $_filtersResAV;

	/**
	 * 2017-07-06
	 * 2017-10-08 This filter chain is applied to a result before the result validation.
	 * @used-by __construct()
	 * @used-by addFilterResBV()
	 * @used-by appendFilterResBV()
	 * @used-by p()
	 * @var FilterChain
	 */
	private $_filtersResBV;

	/**
	 * 2017-08-10
	 * @used-by __construct()
	 * @used-by destructive()
	 * @used-by method()
	 * @var string
	 */
	private $_method;

	/**
	 * 2017-07-02
	 * @used-by __construct()
	 * @used-by path()
	 * @used-by url()
	 * @var string
	 */
	private $_path;

	/**
	 * 2019-01-12
	 * @used-by _p()
	 * @used-by silent()
	 * @var bool
	 */
	private $_silent = false;

	/**
	 * 2019-01-11
	 * @used-by __construct()
	 * @used-by store()
	 * @var Store
	 */
	private $_store;
}