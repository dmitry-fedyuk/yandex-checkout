<?php
use Mage_Core_Model_Store as Store;
/**
 * 2015-10-09
 * https://mage2.pro/t/128
 * https://github.com/magento/magento2/issues/2064
 *                            
 * @used-by df_mail()
 * @param string|string[] $key
 * @param null|string|int|Store $scope [optional]
 * @param mixed|callable $d [optional]
 * @return array|string|null|mixed
 */
function df_cfg($key, $scope = null, $d = null) {
	if (is_array($key)) {
		$key = df_cc_path($key);
	}
	$r = df_store($scope)->getConfig($key); /** @var array|string|null|mixed $result */
	return df_if(df_cfg_empty($r), $d, $r);
}

/**
 * 2016-11-12
 * @used-by df_cfg()
 * @used-by \Df\Config\Settings::vv()
 * @param array|string|null|mixed $v
 * @return bool
 */
function df_cfg_empty($v) {return is_null($v) || '' === $v;}