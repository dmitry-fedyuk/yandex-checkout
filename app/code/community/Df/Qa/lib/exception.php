<?php
use Df\Core\Exception as DFE;
use Df\Qa\Message\Failure\Exception as QE;
use Exception as E;
/**
 * 2016-07-18
 * @param E $e
 * @return E
 */
function df_ef(E $e) {while ($e->getPrevious()) {$e = $e->getPrevious();} return $e;}

/**
 * @param E|string $e
 * @return string
 */
function df_ets($e) {return df_adjust_paths_in_message(
	!$e instanceof E ? $e : ($e instanceof DFE ? $e->message() : $e->getMessage())
);}

/**
 * 2016-10-24
 * @param E|string $e
 * @return string
 */
function df_etsd($e) {return df_adjust_paths_in_message(
	!$e instanceof E ? $e : ($e instanceof DFE ? $e->messageD() : $e->getMessage())
);}

/**
 * 2016-07-31
 * @param E $e
 * @return DFE
 */
function df_ewrap($e) {return DFE::wrap($e);}

/**
 * К сожалению, не можем перекрыть Exception::getTraceAsString(),
 * потому что этот метод — финальный
 * @param E $exception
 * @param bool $showCodeContext [optional]
 * @return string
 */
function df_exception_get_trace(E $exception, $showCodeContext = false) {return
	QE::i([QE::P__EXCEPTION => $exception, QE::P__SHOW_CODE_CONTEXT => $showCodeContext])->traceS()
;}