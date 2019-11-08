<?php
/**
 * 2017-07-10
 * @used-by df_api_rr_failed()
 * @used-by \Df\API\Client::p()
 * @param string|object $m
 * @return string
 */
function df_api_name($m) {return is_string($m) && !df_contains($m, '\\', '_', '::') ? $m :
	df_cc_s(df_explode_camel(df_class_second($m)))
;}

/**
 * 2017-07-10
 * @param string|object $m
 * @param mixed $res
 * @param mixed $req [optional]
 * @return string
 */
function df_api_rr_failed($m, $res, $req = null) {
	$m = df_api_name($m);
	return "The $m API request is failed.\n" . df_format_kv(['Response' => $res, 'Request' => $req]);
}