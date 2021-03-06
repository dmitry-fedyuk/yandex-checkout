<?php
namespace Df\Zf\Validate\StringT;
class NotEmpty extends \Df\Zf\Validate\Type {
	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param mixed $value
	 * @return bool
	 */
	function isValid($value) {
		$this->prepareValidation($value);
		/**
		 * 2015-02-16
		 * Раньше здесь стояло is_string($value) && ('' !== strval($value))
		 * Однако интерпретатор PHP способен неявно и вполне однозначно
		 * (без двусмысленностей, как, скажем, с вещественными числами)
		 * конвертировать целые числа в строки,
		 * поэтому пусть целые числа всегда проходят валидацию как непустые строки.
		 *
		 * 2016-07-01
		 * Добавил «|| $value instanceof Phrase»
		 */
		return is_int($value) || (is_string($value) && '' !== strval($value));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'непустую строку';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'непустой строки';}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}