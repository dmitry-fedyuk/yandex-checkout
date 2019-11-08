<?php
use Mage_Directory_Model_Country as Country;

/**               
 * 2017-09-03  
 * @used-by df_lang_ru()
 * @used-by df_lang_zh()
 * @param string|null $locale [optional]
 * @return string
 */
function df_lang($locale = null) {return substr(df_locale($locale), 0, 2);}

/**            
 * 2017-04-15 
 * @used-by df_lang_ru_en()
 * @param mixed[] ...$args 
 * @return bool
 */
function df_lang_ru(...$args) {return df_b($args, 'ru' === df_lang());}

/**               
 * 2017-09-03
 * @return string
 */
function df_lang_ru_en() {return df_lang_ru('ru', 'en');}

/**
 * 2018-04-21
 * @used-by df_lang_zh_en()
 * @param mixed[] ...$args
 * @return bool
 */
function df_lang_zh(...$args) {return df_b($args, 'zh' === df_lang());}

/**
 * 2018-04-24
 * @return string
 */
function df_lang_zh_en() {return df_lang_zh('zh', 'en');}

/**
 * @param Mage_Core_Model_Locale|Zend_Locale|string|null $locale [optional]
 * @return string
 * @throws \Df\Core\Exception
 */
function df_locale($locale = null) {
	/** @var string $result */
	if (!$locale) {
		$result = Mage::app()->getLocale()->getLocaleCode();
	}
	else if (is_string($locale)) {
		$result = $locale;
	}
	else if ($locale instanceof Mage_Core_Model_Locale) {
		$result = $locale->getLocaleCode();
	}
	else if ($locale instanceof Zend_Locale) {
		/** По примеру @see Mage_Core_Model_Locale::getLocale() */
		$result = $locale->__toString();
	}
	else {
		df_error(
			'Функция df_locale получила аргумент недопустимого типа «{type}».'
			."\nАргумент функции df_locale должен иметь один из следующих типов: {allowedTypes}."
			,array(
				'{type}' => df_string_debug($locale)
				,'{allowedTypes}' => 'строка, null, Mage_Core_Model_Locale, Zend_Locale'
			)
		);
	}
	return $result;
}

/**
 * 2017-01-29
 * «US» => «en_US», «SE» => «sv_SE».
 * Some contries has multiple locales (e.g., Finland has the «fi_FI» and «sv_FI» locales).
 * The function returns the default locale: «FI» => «fi_FI».
 * @used-by df_currency_by_country_c()
 * @param string|Country $c
 * @return string
 */
function df_locale_by_country($c) {return \Zend_Locale::getLocaleToTerritory(df_country_code($c));}