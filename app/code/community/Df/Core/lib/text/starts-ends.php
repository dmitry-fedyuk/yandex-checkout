<?php
/**
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * @see df_starts_with()
 * @used-by df_append()
 * @used-by df_referer_ends_with()
 * @used-by mnr_recurring_is()
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function df_ends_with($haystack, $needle) {return /** @var int $l */
	(0 === ($l = mb_strlen($needle))) || ($needle === mb_substr($haystack, -$l))
;}

/**
 * Утверждают, что код ниже работает быстрее, чем return 0 === mb_strpos($haystack, $needle);
 * http://stackoverflow.com/a/10473026
 * http://stackoverflow.com/a/834355
 * @see df_ends_with()
 * @used-by df_action_prefix()
 * @used-by df_check_https()
 * @used-by df_check_json_complex()
 * @used-by df_check_url_absolute()
 * @used-by df_check_xml()
 * @used-by df_handle_prefix()
 * @used-by df_log_l()
 * @used-by df_modules_p()
 * @used-by df_package()
 * @used-by df_path_is_internal()
 * @used-by df_prepend()
 * @used-by df_zf_http_last_req()
 * @used-by \Df\Core\Helper\Text::isRegex()
 * @used-by \Df\Framework\Request::extraKeysRaw()
 * @used-by \Df\Qa\Message\Failure::states()
 * @used-by \Df\Zf\Validate\StringT\IntT::isValid()
 * @param string $haystack
 * @param string|string[] $needle
 * @return bool
 */
function df_starts_with($haystack, $needle) {/** @var bool $r */
	if (!is_array($needle)) {
		$r = $needle === mb_substr($haystack, 0, mb_strlen($needle));
	}
	else {
		$r = false;
		foreach ($needle as $n) { /** @var string $n */
			if (df_starts_with($haystack, $n)) {
				$r = true;
				break;
			}
		}
	}
	return $r;
}