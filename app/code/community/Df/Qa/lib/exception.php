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
 * К сожалению, не можем перекрыть Exception::getTraceAsString(), потому что этот метод — финальный.
 * @used-by df_exception_to_session()
 * @param E $exception
 * @param bool $showCodeContext [optional]
 * @return string
 */
function df_exception_get_trace(E $exception, $showCodeContext = false) {return
	QE::i([QE::P__EXCEPTION => $exception, QE::P__SHOW_CODE_CONTEXT => $showCodeContext])->traceS()
;}

/**
 * Обработка исключительных ситуаций в точках сочленения моих модулей и ядра
 * 		($rethrow === true) => перевозбудить исключительную ситуацию
 * 		($rethrow === false) => не перевозбуждать исключительную ситуацию
 * 		($rethrow === null) =>  перевозбудить исключительную ситуацию, если включен режим разработчика
 * @used-by \Df\Payment\Observer::controller_action_predispatch_checkout()
 * @param E $e
 * @param bool|null $rethrow
 * @param bool|null $sendContentTypeHeader
 * @throws E
 * @return void
 */
function df_handle_entry_point_exception(E $e, $rethrow = null, $sendContentTypeHeader = true) {
	/**
	 * Надо учесть, что исключительная ситуация могла произойти при асинхронном запросе,
	 * и в такой ситуации echo() неэффективно.
	 */
	df_log($e);
	/**
	 * В режиме разработчика
	 * по умолчанию выводим диагностическое сообщение на экран
	 * (но это можно отключить посредством $rethrow = false).
	 *
	 * При отключенном режиме разработчика
	 * по умолчанию не выводим диагностическое сообщение на экран
	 * (но это можно отключить посредством $rethrow = true).
	 */
	if (Mage::getIsDeveloperMode() && false !== $rethrow || true === $rethrow) {
		/**
		 * Чтобы кириллица отображалась в верной кодировке —
		 * пробуем отослать браузеру заголовок Content-Type.
		 *
		 * Обратите внимание, что такой подход не всегда корректен:
		 * ведь нашу исключительную ситуацию может поймать и обработать
		 * ядро Magento или какой-нибудь сторонний модуль, и они затем могут
		 * захотеть вернуть браузеру документ другого типа (не text/html).
		 * Однако, по-правильному они должны при этом сами установить свой Content-type.
		 */
		if (!headers_sent() && $sendContentTypeHeader) {
			header('Content-Type: text/html; charset=UTF-8');
		}
		throw $e;
	}
}