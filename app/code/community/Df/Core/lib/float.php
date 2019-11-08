<?php
/**
 * 2016-10-23
 * Используемой реализации, видимо, идентична такая: sprintf('%.2F', df_float($value))
 * В то же время реализация sprintf('%.2f', df_float($value)) вовсе не идентична используемой,
 * потому что она использует десятичный разделитель текущей локали: для России — запятую.
 * http://php.net/manual/en/function.sprintf.php
 * 3 => 3.00
 * 3.333 => 3.33
 * 3.300 => 3.30
 * https://3v4l.org/AUTCA
 * @used-by dff_2f()
 * @used-by dff_2i()
 * @param float $v
 * @param int $prec [optional]
 * @return string
 */
function dff_2($v, $prec = 2) {return number_format($v, $prec, '.', '');}

/**
 * 2016-09-08
 * @param float|int|string $v
 * @return float
 */
function dff_2f($v) {return floatval(dff_2(floatval($v)));}

/**
 * 2016-10-23 Для нецелых чисел работает как @see dff_2(), а для целых — отбрасывает десятичную часть.
 * @param int|float $v
 * @param int $prec [optional]
 * @return string
 * 3 => 3
 * 3.333 => 3.33
 * 3.300 => 3.30
 * https://3v4l.org/AUTCA
 */
function dff_2i($v, $prec = 2) {return is_int($v) ? (string)$v : dff_2($v, $prec);}

/**
 * 2015-04-09 Форматирует вещественное число с отсечением незначащих нулей после запятой.
 * 2016-10-23
 * 3 => 3
 * 3.333 => 3.333
 * 3.300 => 3.3
 * @param float|int $v
 * @return string
 */
function dff_chop0($v) {
	$f = df_float($v); /** @var float $f */
	$intPart = (int)$f; /** @var int $intPart */
	// намеренно используем «==»
	return $f == $intPart ? (string)$intPart : rtrim(sprintf('%f', $f), '0');
}

/**
 * 2017-09-29
 * 2017-11-03
 * I now provide the $deviation argument to @uses dff_eq0() to fix the issue:
 * «Unable to generate tax data for Yandex.Kassa.
 * The order's grand total is 3000.00. The calculated grand total from tax data is 2999.80.»
 * https://github.com/mage2pro/yandex-kassa/issues/2
 * I use deviation of 0.1% of $a.
 * @param float|string|int $a
 * @param float|string|int $b
 * @return bool
 */
function dff_eq($a, $b) {return dff_eq0(floatval($a) - floatval($b), .001 * $a);}

/**      
 * 2016-09-08
 * 2017-11-03
 * I have added the $deviation argument to fix the issue:
 * «Unable to generate tax data for Yandex.Kassa.
 * The order's grand total is 3000.00. The calculated grand total from tax data is 2999.80.»
 * https://github.com/mage2pro/yandex-kassa/issues/2
 * By default, 0.1% deviation is allowed.
 * @used-by dff_eq()
 * @used-by dfp_refund()
 * @param float $a
 * @param float $deviation [optional]
 * @return bool
 */
function dff_eq0($a, $deviation = .001) {return abs($a) < $deviation;}