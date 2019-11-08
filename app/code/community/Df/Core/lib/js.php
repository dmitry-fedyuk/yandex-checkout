<?php
/**
 * 2015-02-17
 * Экранирует строку для вставки её в код на JavaScript.
 * @uses json_encode() рекомендуют
 * как самый правильный способ вставки строки из PHP в JavaScript:
 * http://stackoverflow.com/a/169035
 * Заменяем символ одинарной кавычки его кодом Unicode,
 * чтобы результат метода можно было вставлять внутрь обрамленной одиночными кавычками строки,
 * например:
	var $name = '<?= df_ejs($name); ?>';
 * @used-by df_admin_button_location()
 * @used-by df_js_data()
 * @used-by Df_Admin_Config_DynamicTable_Column::renderTemplate()
 * @used-by app/design/adminhtml/rm/default/template/df/admin/column/select.phtml
 * @used-by app/design/adminhtml/rm/default/template/df/admin/field/button.phtml
 * @used-by app/design/frontend/rm/default/template/df/checkout/onepage/shipping_method/available/js.phtml
 * @param string $text
 * @return string
 */
function df_ejs($text) {return str_replace("'", '\u0027', df_trim(json_encode($text), '"'));}

/**
 * 2015-10-26 https://mage2.pro/t/145
 * 2016-11-28
 * Пример: https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Theme/view/frontend/templates/js/cookie.phtml#L16-L26
 * Такой синтаксис, в отличие от @see df_widget(),
 * не позволяет нам иметь в JavaScript объект-элемент DOM вторым параметром:
 * https://github.com/magento/magento2/blob/2.1.2/lib/web/mage/apply/main.js#L69-L70
 * 2017-04-21
 * Эта функция не привязывает код JavaScript браузерного компонента ни к какому элементу HTML.
 * Если Вам нужна такая привязка, то используйте альтернативную функцию @see df_widget()
 *
 * @used-by df_fe_init()
 * @used-by df_js_c()
 * @param string|object|null $m
 * $m could be:
 * 1) A module name: «A_B».
 * 2) A class name: «A\B\C».
 * 3) An object. It is reduced to case 2 via @see get_class()
 * 4) 2017-10-16: `null`, if $script is an absolute URL.
 * @param string|null $s [optional]
 * @param array(string => mixed) $p [optional]
 * @return string
 */
function df_js($m, $s = null, array $p = []) {$s = $s ?: 'main'; return df_js_x(
	'*', df_check_url_absolute($s) ? null : $m, $s, $p
);}

/**
 * 2019-08-26
 * @used-by vendor/inkifi/map/view/frontend/templates/create.phtml
 * 1) An usage example:
 * https://github.com/inkifi/map/blob/0.0.5/view/frontend/templates/create.phtml#L11
 * https://github.com/inkifi/map/blob/0.0.5/view/frontend/web/js/create.js
 * 2) Another example: https://github.com/inkifi/map/blob/0.0.6/view/frontend/templates/create.phtml#L1-L2
 * @see df_js_x()
 * @see df_widget()
 * @param string $s
 * @param array(string => mixed) $p [optional]
 * @return string
 */
function df_js_c($s, array $p = []) {return df_js(null, 'Magento_Ui/js/core/app', ['components' => [
	$s => ['component' => $s] + $p
]]);}

/**
 * 2018-05-21
 * @used-by vendor/inkifi/map/view/frontend/templates/create.phtml
 * @param string|string[] $n
 * @param mixed $v
 * @return string
 */
function df_js_data($n, $v) {return df_tag('script', ['type' => 'text/javascript'], sprintf(
	"window.%s = %s;", df_cc('.', $n), df_ejs($v)
));}

/**
 * 2017-04-21
 * Эта функция обладает 2-мя преимуществами перед @see df_js_inline_url():
 * 1) Скрипт кэшируется посредством RequireJS.
 * Это важно в том случае, когда скрипт загружается не только в сценарии этой функции,
 * но и из другого скрипта JavaScript: применение RequireJS позволяет нам не загружать скрипт повторно.
 * 2) Загрузка скрипта не блокирует рисование страницы браузером
 * (аналогично для этого можно было бы использовать атрибут async тега script).
 * @param string $n
 * @return string
 */
function df_js_inline_r($n) {return df_tag('script', ['type' => 'text/javascript'], "require(['$n']);");}

/**
 * 2019-06-01
 * @used-by df_js()
 * @used-by vendor/kingpalm/adult/view/frontend/templates/popup.phtml
 * @see df_widget()
 * @param string $selector
 * @param string|object|null $m
 * $m could be:
 * 1) A module name: «A_B».
 * 2) A class name: «A\B\C».
 * 3) An object. It is reduced to case 2 via @see get_class()
 * 4) null
 * @param string|null $s [optional]
 * @param array(string => mixed) $p [optional]
 * @return string
 */
function df_js_x($selector, $m, $s = null, array $p = []) {return df_tag(
	'script', ['type' => 'text/x-magento-init'], df_json_encode([$selector => [
		df_cc_path(is_null($m) ? null : df_module_name($m), $s ?: 'main') => $p
	]])
);}

/**
 * 2016-11-28
 * Такой синтаксис, в отличие от @see df_js(),
 * позволяет нам иметь в JavaScript объект-элемент DOM вторым параметром:
 * https://github.com/magento/magento2/blob/2.1.2/lib/web/mage/apply/main.js#L69-L70
 * Пример: https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Checkout/view/frontend/templates/cart/minicart.phtml#L30-L38
 * @see json_encode всегда использует двойные кавычки,
 * а @see \Df\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * всегда обрамляет значение в одинарные кавычки,
 * поэтому df_widget() всегда совместима с @see df_tag()
 *
 * 2017-04-21
 * Эта функция предоставляет альтернативный @see df_js() способ
 * инициализации браузерного компонента: параметры инициализации передаются компоненту
 * в значении атрибута «data-mage-init» произвольного тега HTML, например:
 *		<div class="block block-minicart empty"
 *			data-role="dropdownDialog"
 *			data-mage-init='{"dropdownDialog":{
 *				"appendTo":"[data-block=minicart]",
 *				"triggerTarget":".showcart",
 *				"timeout": "2000",
 *				"closeOnMouseLeave": false,
 *				"closeOnEscape": true,
 *				"triggerClass":"active",
 *				"parentClass":"active",
 *				"buttons":[]
 * 			}}'
 * 		>
 *			<div id="minicart-content-wrapper" data-bind="scope: 'minicart_content'">
 *				<!-- ko template: getTemplate() --><!-- /ko -->
 *			</div>
 *			<?= $block->getChildHtml('minicart.addons'); ?>
 *		</div>
 * https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Checkout/view/frontend/templates/cart/minicart.phtml#L30-L38
 * Таким образом код JavaScript браузерного компонента
 * оказывается ассоциированным с неким конкретным элементом HTML:
 * этим данный способ инициализации отличается от способа функции @see df_js(),
 * которая не привязывает код JavaScript браузерного компонента ни к какому элементу HTML.
 * @param string|object $m
 * $m could be:
 * 1) A module name: «A_B».
 * 2) A class name: «A\B\C».
 * 3) An object. It is reduced to case 2 via @see get_class()
 * @param string|null $s [optional]
 * @param array(string => mixed) $p [optional]
 * @return array(string => string)
 */
function df_widget($m, $s = null, array $p = []) {return ['data-mage-init' => df_json_encode([
	df_cc_path(df_module_name($m), $s ?: 'main') => $p
])];}