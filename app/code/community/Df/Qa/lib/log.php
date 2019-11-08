<?php
use Df\Qa\Message\Failure\Exception as QE;
use Exception as E;
use Varien_Object as _DO;

/**
 * @param int $levelsToSkip
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt() и т.п.)
 */
function df_bt($levelsToSkip = 0) {
	/** @var array $bt */
	$bt = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), $levelsToSkip);
	/** @var array $compactBT */
	$compactBT = [];
	/** @var int $traceLength */
	$traceLength = count($bt);
	/**
	 * 2015-07-23
	 * 1) Удаляем часть файлового пути до корневой папки Magento.
	 * 2) Заменяем разделитель папок на унифицированный.
	 */
	/** @var string $bp */
	$bp = BP . DS;
	/** @var bool $nonStandardDS */
	$nonStandardDS = DS !== '/';
	for ($traceIndex = 0; $traceIndex < $traceLength; $traceIndex++) {
		/** @var array $currentState */
		$currentState = dfa($bt, $traceIndex);
		/** @var array(string => string) $nextState */
		$nextState = dfa($bt, 1 + $traceIndex, []);
		/** @var string $file */
		$file = str_replace($bp, '', dfa($currentState, 'file'));
		if ($nonStandardDS) {
			$file = df_path_n($file);
		}
		$compactBT[]= [
			'File' => $file
			,'Line' => dfa($currentState, 'line')
			,'Caller' => !$nextState ? '' : df_cc_method($nextState)
			,'Callee' => !$currentState ? '' : df_cc_method($currentState)
		];
	}
	df_report('bt-{date}-{time}.log', print_r($compactBT, true));
}

/**
 * @param _DO|mixed[]|mixed|E $v
 * @param string|object|null $m [optional]
 */
function df_log($v, $m = null) {df_log_l($m, $v); df_sentry($m, $v);}

/**
 * 2017-01-11
 * @used-by df_log()
 * @param E $e
 */
function df_log_e($e) {QE::i([QE::P__EXCEPTION => $e, QE::P__SHOW_CODE_CONTEXT => true])->log();}

/**
 * 2017-01-11
 * @used-by df_log()
 * @used-by dfp_report()
 * @param string|object $caller
 * @param string|mixed[]|E $data
 * @param string|null $suffix [optional]
 */
function df_log_l($caller, $data, $suffix = null) {
	if ($data instanceof E) {
		df_log_e($data);
	}
	else {
		$code = df_package_name_l($caller); /** @var $code $method */
		$data = is_string($data) ? $data : df_json_encode($data);
		$ext = df_starts_with($data, '{') ?  'json' : 'log'; /** @var string $ext */
		df_report(df_ccc('--', "mage2.pro/$code-{date}--{time}", $suffix) .  ".$ext", $data);
	}
}

/**
 * 2017-04-03
 * 2018-07-06 The `$append` parameter has been added.
 * @used-by df_bt()
 * @used-by df_log_l()
 * @used-by \Df\Core\Text\Regex::throwInternalError()
 * @used-by \Df\Core\Text\Regex::throwNotMatch()
 * @used-by \Df\Qa\Message::log()
 * @param string $f
 * @param string $m
 * @param bool $append [optional]
 */
function df_report($f, $m, $append = false) {
	if ('' !== $m) {
		df_param_s($m, 1);
		$f = df_file_ext_def($f, 'log');
		$p = BP . '/var/log'; /** @var string $p */
		df_file_write($append ? "$p/$f" : df_file_name($p, $f), $m, $append);
	}
}