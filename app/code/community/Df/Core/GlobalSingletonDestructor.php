<?php
namespace Df\Core;
/**
 * Этот класс предназначен для деинициализации глобальных объектов-одиночек.
 * Опасно проводить деинициализацию глобальных объектов-одиночек в стандартном деструкторе,
 * потому что к моменту вызова деструктора для данного объекта-одиночки
 * сборщик Zend Engine мог уже уничтожить другие глобальные объекты,
 * требуемые для сохранения кэша.
 */
class GlobalSingletonDestructor {
	/**
	 * @used-by \Df\Core\Observer::controller_front_send_response_after()
	 * @uses \Df\Core\O::_destruct()
	 */
	function process() {df_each($this->_objects, '_destruct');}

	/**
	 * @used-by df_destructable_sg()
	 * @param \Df\Core\O $object
	 */
	function register(\Df\Core\O $object) {$this->_objects[]= $object;}

	/** @var \Df\Core\O[] */
	private $_objects = [];

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}