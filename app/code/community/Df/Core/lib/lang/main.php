<?php
use Closure as F;

/**
 * 2015-12-25
 * Этот загадочный метод призван заменить код вида:
 * is_array($arguments) ? $arguments : func_get_args()
 * Теперь можно писать так: df_args(func_get_args())
 * @used-by dfa_combine_self()
 * @param mixed[] $a
 * @return mixed[]
 */
function df_args(array $a) {return !$a || !is_array($a[0]) ? $a : $a[0];}

/**
 * 2017-02-07
 * @param mixed[] $args
 * $args — массив либо пустой, либо из 2 элементов с целочисленными индексами 0 и 1.
 * Если массив $args пуст, то функция возвращает $r.
 * Если массив $args непуст, то функция возвращает:
 * 		$args[0] при истинности $r
 *		$args[1] при ложности $r
 * @param bool $r
 * @return mixed
 */
function df_b(array $args, $r) {return !$args ? $r : $args[intval(!$r)];}

/**
 * 2017-04-26
 * @used-by df_ci_get()
 * @used-by df_oi_add()
 * @used-by df_oi_get()
 * @used-by df_primary_key()
 * @used-by df_trd()    
 * @used-by ikf_oi_pid()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Xml\Parser\Collection::findByNameAll()
 * @used-by \Df\Xml\X::importString()
 * @used-by \Df\Zf\Validate\ArrayT::filter()
 * @param mixed|null $v
 * @return mixed[]
 */
function df_eta($v) {
	if (!is_array($v)) {
		df_assert(empty($v));
		$v = [];
	}
	return $v;
}

/**
 * 2016-02-09
 * Осуществляет ленивое ветвление только для первой ветки.
 * @param bool $cond
 * @param mixed|callable $onTrue
 * @param mixed|null $onFalse [optional]
 * @return mixed
 */
function df_if1($cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : $onFalse;}

/**
 * 2016-02-09
 * Осуществляет ленивое ветвление только для второй ветки.
 * @param bool $cond
 * @param mixed $onTrue
 * @param mixed|null|callable $onFalse [optional]
 * @return mixed
 */
function df_if2($cond, $onTrue, $onFalse = null) {return $cond ? $onTrue : df_call_if($onFalse);}

/**
 * Осуществляет ленивое ветвление.
 * @param bool $cond
 * @param mixed|callable $onTrue
 * @param mixed|null|callable $onFalse [optional]
 * @return mixed
 */
function df_if($cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : df_call_if($onFalse);}

/**
 * @used-by \Df\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * @param mixed $v
 * @return mixed
 */
function df_nop($v) {return $v;}

/**
 * 2017-04-15
 * @used-by df_cms_block_content()
 * @used-by df_currency_convert_safe()
 * @used-by df_customer()
 * @used-by df_intl_dic_read()
 * @used-by df_layout_update()
 * @used-by df_magento_version_remote()
 * @used-by df_phone()
 * @used-by dfp_refund()
 * @param F $try
 * @param F|bool|mixed $onError [optional]
 * @return mixed
 * @throws \Exception
 */
function df_try(F $try, $onError = null) {
	try {return $try();}
	catch(\Exception $e) {return $onError instanceof F ? $onError($e) : (
		true === $onError ? df_error($e) : $onError
	);}
}