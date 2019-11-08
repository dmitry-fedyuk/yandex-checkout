<?php
use Mage_Core_Helper_Data as H;
use Mage_Core_Model_Store as Store;
use Mage_Directory_Model_Country as Country;
/**
 * 2015-02-04
 * Обратите внимание, что вряд ли мы вправе кэшировать результат при парметре $store = null,
 * ведь текущий магазин может меняться.
 * @param Store|int|string|bool|null $store [optional]
 * @return Store
 * @throws Mage_Core_Model_Store_Exception|Exception
 */
function df_store($store = null) {
	/** @var Store $result */
	$result = $store;
	if (is_null($result)) {
		/** @var Store $coreCurrentStore */
		$coreCurrentStore = Mage::app()->getStore();
		/**
		 * 2015-08-10
		 * Доработал алгоритм.
		 * Сначала мы смотрим, не находимся ли мы в административной части,
		 * и нельзя ли при этом узнать текущий магазин из веб-адреса.
		 * По аналогии с @see Mage_Adminhtml_Block_Catalog_Product_Grid::_getStore()
		 */
		if ('admin' === $coreCurrentStore->getCode()) {
			/** @var int|null $storeIdFromRequest */
			$storeIdFromRequest = df_request('store');
			if ($storeIdFromRequest) {
				$result = Mage::app()->getStore($result);
			}
			/**
			 * 2015-09-20
			 * При единственном магазине
			 * вызываемый ниже метод метод @uses Df_Core_State::getStoreProcessed()
			 * возвратит витрину default, однако при нахождении в административной части
			 * нам нужно вернуть витрину admin.
			 * Например, это нужно, чтобы правильно работала функция @used-by df_is_admin()
			 * Переменная $coreCurrentStore в данной точке содержит витрину admin.
			 *
			 * 2015-11-04
			 * Вообще, напрашивается вопрос: правильно ли,
			 * что при единственном магазине метод @uses Df_Core_State::getStoreProcessed()
			 * возвращает витрину default, а не admin?
			 * Кажется, что неправильно. Возможно, надо поменять.
			 * Но решил это пока не трогать, чтобы не поломать текущее поведение модулей.
			 */
			if (is_null($result) && Mage::app()->isSingleStoreMode()) {
				$result = $coreCurrentStore;
			}
		}
		/**
		 * Теперь смотрим, нельзя ли узнать текущий магазин из веба-адреса в формате РСМ.
		 * Этот формат используют модули 1С:Управление торговлей и Яндекс-Маркет.
		 */
		if (is_null($result)) {
			/**
			 * @uses Df_Core_State::getStoreProcessed()
			 * может вызывать @see df_store() опосредованно: например, через @see df_assert().
			 * Поэтому нам важно отслеживать рекурсию и не зависнуть.
			 */
			/** @var int $recursionLevel */
			static $recursionLevel = 0;
			if (!$recursionLevel) {
				$recursionLevel++;
				try {
					$result = Mage::app()->getStore();
				}
				catch (Exception $e) {
					$recursionLevel--;
					throw $e;
				}
				$recursionLevel--;
			}
		}
		if (is_null($result)) {
			$result = $coreCurrentStore;
		}
	}
	if (!is_object($result)) {
		$result = Mage::app()->getStore($result);
	}
	if (!is_object($result)) {
		/**
		 * 2015-08-14
		 * Такое бывает, например, когда текущий магазин ещё не инициализирован.
		 * @see Mage_Core_Model_App::getStore()
		 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.1/app/code/core/Mage/Core/Model/App.php#L842
		 * @see Mage_Core_Model_App::_currentStore
		 */
		Mage::app()->throwStoreException();
	}
	return $result;
}

/**
 * 2016-01-30
 * @used-by df_sentry()
 * @param null|string|int $store [optional]
 * @return string
 */
function df_store_code($store = null) {return df_store($store)->getCode();}

/**            
 * 2017-01-21
 * «How to get the store's country?» https://mage2.pro/t/2509
 * @param null|string|int|Store $store [optional]
 * @return Country
 */
function df_store_country($store = null) {return df_country(df_store($store)->getConfig(
	H::XML_PATH_MERCHANT_COUNTRY_CODE
));}

/**
 * 2016-01-11
 * @used-by df_category()
 * @used-by df_product()
 * @param int|string|null|bool|Store $store [optional]
 * @return int
 */
function df_store_id($store = null) {return df_store($store)->getId();}

/**
 * 2017-03-15
 * Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by df_store_url_link()
 * @used-by df_store_url_web()
 * @param int|string|null|bool|Store $s
 * @param string $type
 * @return string
 */
function df_store_url($s, $type) {return df_store($s)->getBaseUrl($type);}

/**
 * 2017-03-15
 * Returns an empty string if the store's root URL is absent in the Magento database.
 * @param int|string|null|bool|Store $s [optional]
 * @return string
 */
function df_store_url_link($s = null) {return df_store_url($s, Store::URL_TYPE_LINK);}

/**
 * 2017-03-15
 * Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by df_domain_current()
 * @param int|string|null|bool|Store $s [optional]
 * @return string
 */
function df_store_url_web($s = null) {return df_store_url($s, Store::URL_TYPE_WEB);}