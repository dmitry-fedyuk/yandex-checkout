<?php
/**
 * 2017-03-16
 * @see df_url_path_contains()
 * @param string $s
 * @return bool
 */
function df_action_has($s) {return df_contains(df_action_name(), $s);}

/**
 * 2016-01-07
 * @see df_url_path_contains()
 * @used-by vendor/wolfautoparts.com/filter/view/frontend/templates/sidebar.phtml
 * @param string[] ...$names
 * @return bool
 */
function df_action_is(...$names) {return ($a = df_action_name()) && in_array($a, dfa_flatten($names));}

/**
 * 2015-03-31
 * @used-by df_action_has()
 * @used-by df_action_is()
 * @used-by df_sentry()
 * @return string|null
 */
function df_action_name() {return !df_controller() ? '' : df_controller()->getFullActionName();}

/**
 * 2017-08-28
 * @used-by df_is_checkout()
 * @used-by df_is_checkout_multishipping()
 * @used-by df_is_system_config()
 * @param string|string[] $p
 * @return bool
 */
function df_action_prefix($p) {return df_starts_with(df_action_name(), $p);}

/**
 * 2019-11-04
 * @param string $s
 * @return bool
 */
function df_referer_ends_with($s) {return df_ends_with(dfa($_SERVER, 'HTTP_REFERER'), $s);}