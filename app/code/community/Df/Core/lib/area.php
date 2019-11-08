<?php
/**
 * Раньше я использовал Mage::app()->getStore()->isAdmin(),
 * однако метод ядра @see Mage_Core_Model_Store::isAdmin()
 * проверяет, является ли магазин административным,
 * более наивным способом: сравнивая идентификатор магазина с нулем
 * (подразумевая, что 0 — идентификатор административного магазина).
 * Как оказалось, у некоторых клиентов идентификатор административного магазина
 * не равен нулю (видимо, что-то не то делали с базой данных).
 * Поэтому используем более надёжную проверку — кода магазина.
 *
 * 2015-02-04
 * Раньше реализация метода была такой:
 *		function df_is_admin($store = null) {
 *			static $cachedResult;
 *			$forCurrentStore = is_null($store);
 *			if ($forCurrentStore && isset($cachedResult)) {
 *				$result = $cachedResult;
 *			}
 *			else {
 *				$result = ('admin' === df_store($store)->getCode());
 *				if ($forCurrentStore) {
 *					$cachedResult = $result;
 *				}
 *			}
 *			return $result;
 *		}
 * Однако мы не вправе кэшировать результат работы функции:
 * ведь текущий магазин может меняться. Поэтому убрал кэширование.
 *
 * @param Mage_Core_Model_Store|int|string|bool|null $store
 * @return bool
 */
function df_is_backend($store = null) {return 'admin' === Mage::app()->getStore($store)->getCode();}