<?php
use Df\Core\Visitor as V;
use Mage_Sales_Model_Order as O;
/**
 * 2016-05-20
 * @param string|null|O $ip [optional]
 * @return V
 */
function df_visitor($ip = null) {return V::sp(df_is_o($ip) ? $ip->getRemoteIp() : $ip);}

/**
 * @used-by df_sentry_m()
 * @used-by \Df\Core\Visitor::sp()
 * @return string
 */
function df_visitor_ip() {return df_my_local() ? '92.243.166.8' : df_http_h()->getRemoteAddr();}

/**
 * 2017-11-01 It returns a string like «en_US»: https://stackoverflow.com/a/22334417
 * @return string
 */
function df_visitor_locale() {return \Locale::acceptFromHttp(dfa($_SERVER, 'HTTP_ACCEPT_LANGUAGE'));}