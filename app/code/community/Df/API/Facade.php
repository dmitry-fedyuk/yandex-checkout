<?php
namespace Df\API;
use Df\API\Document as D;
use Df\API\Operation as Op;
use Df\Core\Exception as DFE;
use Mage_Core_Model_Store as Store;
use Mage_Sales_Model_Order as Order;
use Zend_Http_Client as Z;
/**
 * 2017-07-13
 */
abstract class Facade {
	/**
	 * 2019-01-11
	 * @used-by s()
	 * @param Store|string|int|null $s [optional]
	 */
	function __construct($s = null) {$this->_store = df_store($s);}

	/**
	 * 2017-08-07
	 * @return Op
	 */
	final function all() {return $this->p();}

	/**
	 * 2017-07-13
	 * @param array(string => mixed) $a
	 * @return Op
	 * @throws DFE
	 */
	final function create(array $a) {return $this->p($a, Z::POST);}

	/**
	 * 2017-08-08
	 * @param string $id
	 * @return Op
	 */
	final function delete($id) {return $this->p($id);}

	/**
	 * 2017-07-13
	 * @used-by ikf_api_oi()
	 * @param int|string $id
	 * @param string|null $suffix [optional]
	 * @param FacadeOptions|null $opt [optional]
	 * @return Op
	 */
	final function get($id, $suffix = null, FacadeOptions $opt = null) {return $this->p(
		$id, null, $suffix, $opt
	);}

	/**
	 * 2017-09-04 Currently it is never used.
	 * @param int|string|array(string => mixed) $p
	 * @return Op
	 * @throws DFE
	 */
	final function patch($p) {return $this->p($p);}

	/**
	 * 2017-10-08
	 * @param int|string|array(string => mixed) $p
	 * @param string|null $suffix [optional]
	 * @param FacadeOptions|null $opt [optional]
	 * @return Op
	 * @throws DFE
	 */
	final function post($p, $suffix = null, FacadeOptions $opt = null) {return $this->p(
		$p, null, $suffix, $opt
	);}

	/**
	 * 2017-09-03
	 * @param array(string => mixed) $p
	 * @param string|null $suffix [optional]
	 * @return Op
	 * @throws DFE
	 */
	final function put(array $p, $suffix = null) {return $this->p($p, null, $suffix);}

	/**
	 * 2019-03-04
	 * @used-by p()
	 * @param Client $c
	 */
	protected function adjustClient(Client $c) {}

	/**
	 * 2017-07-13
	 * @used-by all()
	 * @used-by create()
	 * @used-by delete()
	 * @used-by get()
	 * @used-by patch()
	 * @used-by put()
	 * @param int|string|array(string => mixed) $p [optional]
	 * @param string|null $method [optional]
	 * @param string|null $suffix [optional]
	 * @param FacadeOptions|null $opt [optional]
	 * @return Op
	 * @throws DFE
	 */
	final protected function p($p = [], $method = null, $suffix = null, FacadeOptions $opt = null) {
		$opt = $opt ?: $this->opts();
		$methodF = strtoupper(df_caller_ff()); /** @var string $method */
		$method = $method ?: (in_array($methodF, [Z::POST, Z::PUT, Z::DELETE, Z::PATCH]) ? $methodF : Z::GET);
		/** @var int|string|null $id */
		list($id, $p) = is_array($p) ? [null, $p] : [$p, []];
		/** @uses \Df\API\Client::__construct() */
		$client = df_newa(df_con($this, 'API\\Client'), Client::class,
			$this->path($id, $suffix), $p, $method, $this->zfConfig()
			,(is_null($id) ? null : $this->storeByP($id)) ?: $this->_store
		); /** @var Client $client */
		$this->adjustClient($client);
		/**
		 * 2019-01-12 It is used by the Inkifi_Mediaclip module.
		 * 2019-04-05
		 * A silent request is not logged. @see \Df\API\Client::_p():
		 *	if (!$this->_silent) {
		 *		df_log_l($m, $ex);
		 *		df_sentry($m, $short, ['extra' => ['Request' => $req, 'Response' => $long]]);
		 *	}
		 * https://github.com/mage2pro/core/blob/4.2.8/API/Client.php#L358-L361
		 */
		if ($opt->silent()) {
			$client->silent();
		}
		/**
		 * 2017-08-08
		 * We use @uses df_eta() to handle the HTTP 204 («No Content») null response
		 * (e.g., on a @see Z::DELETE request).
		 * 2017-12-03
		 * The previous code was:
		 * 		return new O(new D($id ? $p : df_clean(['id' => $id, 'p' => $p])), new D(df_eta($client->p())));
		 * https://github.com/mage2pro/core/blob/3.3.40/API/Facade.php#L123
		 * It was introduced at 2017-09-03 in the 2.11.10 version by the following commit:
		 * https://github.com/mage2pro/core/commit/31063704
		 * I think, $id instead of !$id was just a bug.
		 * Prior the 2.11.10 version, the code was:
		 * 		return new O(new D($p ?: df_clean(['id' => $id])), new D(df_eta($client->p())));
		 * https://github.com/mage2pro/core/blob/2.11.9/API/Facade.php#L68
		 */
		/** @noinspection PhpParamsInspection */  // 2019-04-05 For `df_newa()`
		return new Op(
			new D(!$id ? $p : df_clean(['id' => $id, 'p' => $p]))
			/**
			 * 2018-08-11
			 * Some API's can return not a complex value (which is convertable to an array),
			 * but a simple textual value.
			 * So, now I handle this possibility.
			 */
			,df_newa($opt->resC(), D::class,
				is_array($res = $client->p()) ? df_eta($res) : df_array($res) /** @var mixed $res */
			)
		);
	}

	/**
	 * 2017-12-03
	 * @used-by p()
	 * @param int|string|null $id
	 * @param string|null $suffix
	 * @return string
	 */
	protected function path($id, $suffix) {return df_cc_path(
		$this->prefix(), strtolower(df_class_l($this)) . 's', urlencode($id), $suffix
	);}

	/**
	 * 2017-08-07
	 * @used-by path()
	 * @return string
	 */
	protected function prefix() {return '';}

	/**
	 * 2019-02-26
	 * @used-by p()
	 * @param int|string|array(string => mixed)|array(int|string, array(int|string => mixed)) $p
	 * @return Store|null
	 */
	protected function storeByP($p) {return null;}

	/**
	 * 2017-10-19
	 * 2018-11-11
	 * Now we have also @see \Df\API\Client::zfConfig()
	 * *) Use \Df\API\Client::zfConfig()
	 * if you need to provide a common configuration for all API requests.
	 * *) Use \Df\API\Facade::zfConfig()
	 * if you need to provide a custom configuration for an API request group.
	 * @used-by p()
	 * @return array(string => mixed)
	 */
	protected function zfConfig() {return [];}

	/**
	 * 2019-04-05
	 * @used-by p()
	 * @return FacadeOptions
	 */
	final protected function opts() {return FacadeOptions::i();}

	/**
	 * 2019-01-11
	 * @used-by __construct()
	 * @used-by p()
	 * @var Store
	 */
	private $_store;

	/**
	 * 2017-07-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by ikf_api_oi()
	 * @param Store|Order $s [optional]
	 * @return self
	 */
	static function s($s = null) {return dfcf(
		function($c, Store $s) {return new $c($s);}, [static::class, df_store($s)]
	);}
}