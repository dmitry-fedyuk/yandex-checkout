<?php
/**
 * 2015-08-14
 * https://mage2.pro/t/57
 * https://mage2.ru/t/92
 * 2016-11-17
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 * @used-by df_test_file()
 * @used-by \Df\Core\O::modulePath()
 * @param string|object $m
 * @param string $type [optional]
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_dir($m, $type = '') {return \Mage::getConfig()->getModuleDir($type, df_module_name($m));}

/**
 * 2017-09-01
 * @used-by df_module_csv2()
 * @used-by df_module_json()
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 * @param string|object $m
 * @param string $name
 * @param string $ext
 * @param bool $req
 * @param \Closure $parser
 * @return array(string => mixed)
 */
function df_module_file($m, $name, $ext, $req, \Closure $parser) {return dfcf(
	function($m, $name, $ext, $req, $parser) {return
		file_exists($f = df_module_path_etc($m, "$name.$ext")) ? $parser($f) :
			(!$req ? [] : df_error('The required file «%1» is absent.', $f))
	;}, func_get_args()
);}

/**
 * 2017-01-27
 * @see df_module_csv2()    
 * @used-by df_currency_nums()
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 * @param string|object $m
 * @param string $name
 * @param bool $req [optional]
 * @return array(string => mixed)
 */
function df_module_json($m, $name, $req = true) {return df_module_file($m, $name, 'json', $req,
	function($f) {return df_json_decode(file_get_contents($f));}
);}

/**
 * «Dfe\AllPay\W\Handler» => «Dfe_AllPay»
 *
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 *
 * 2016-10-26
 * Функция успешно работает с короткими именами классов:
 * «A\B\C» => «A_B»
 * «A_B» => «A_B»
 * «A» => A»
 * https://3v4l.org/Jstvc
 *
 * 2017-01-27
 * Так как «A_B» => «A_B», то функция успешно работает с именем модуля:
 * она просто возвращает его без изменений.
 * Таким образом, функция допускает на входе:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 *
 * @used-by df_asset_name()
 * @used-by df_block_output()
 * @used-by df_con()
 * @used-by df_fe_init()
 * @used-by df_js()
 * @used-by df_js_x()
 * @used-by df_module_dir()
 * @used-by df_module_name_c()
 * @used-by df_package()
 * @used-by df_route()
 * @used-by df_sentry_module()
 * @used-by df_widget()
 * @param string|object|null $c [optional]
 * @param string $del [optional]
 * @return string
 */
function df_module_name($c = null, $del = '_') {return dfcf(function($c, $del) {return
	implode($del, array_slice(df_explode_class($c), 0, 2))
;}, [$c ? df_cts($c) : 'Df\Core', $del]);}

/**
 * 2017-01-04
 * $c could be:
 * 1) A module name. E.g.: «A_B».
 * 2) A class name. E.g.: «A\B\C».
 * 3) An object. It will be treated as case 2 after @see get_class()
 * @used-by dfs()
 * @used-by dfs_con()
 * @used-by \Df\Framework\Action::module()
 * @param string|object|null $c [optional]
 * @return string
 */
function df_module_name_c($c = null) {return df_module_name($c, '\\');}

/**
 * 2016-08-28 «Dfe\AllPay\W\Handler» => «AllPay»
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string
 */
function df_module_name_short($c) {return dfcf(function($c) {return df_explode_class($c)[1];}, [df_cts($c)]);}

/**
 * 2016-02-16
 * «Dfe\CheckoutCom\Method» => «dfe_checkout_com»
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * 2017-10-03
 * $c could be:
 * 1) A module name. E.g.: «A_B».
 * 2) A class name. E.g.: «A\B\C».
 * 3) An object. It will be treated as case 2 after @see get_class() 
 * @used-by \Df\Core\Exception::reportNamePrefix()
 * @param string|object $c
 * @param string $del [optional]
 * @return string
 */
function df_module_name_lc($c, $del = '_') {return implode($del, df_explode_class_lc_camel(df_module_name_c($c)));}

/**
 * 2015-11-15
 * 2016-11-17
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 *
 * @param string|object $m
 * @param string $localPath [optional]
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_path($m, $localPath = '') {return df_cc_path(df_module_dir($m), $localPath);}

/**
 * 2016-07-19
 * 2016-11-17
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 *
 * @used-by df_module_file()

 * @param string|object $m
 * @param string $localPath [optional]
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_path_etc($m, $localPath = '') {return df_cc_path(df_module_dir($m, 'etc'), $localPath);}