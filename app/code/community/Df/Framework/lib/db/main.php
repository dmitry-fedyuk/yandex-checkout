<?php
use Varien_Db_Select as Select;

/**
 * 2016-12-01
 * @used-by df_customer_att_pos_after()
 * @used-by df_customer_is_new()
 * @used-by df_fetch_all()
 * @used-by df_fetch_col()
 * @used-by df_fetch_col_max()
 * @used-by df_fetch_one()
 * @used-by df_trans_by_payment()
 * @param string|array(string => string) $t
 * @param string|string[] $cols [optional]
 * Если надо выбрать только одно поле, то можно передавать не массив, а строку:
 * @see \Zend_Db_Select::_tableCols()
 *		if (!is_array($cols)) {
 *			$cols = array($cols);
 *		}
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Db/Select.php#L929-L931
 * @param string|null $schema [optional]
 * @return Select|\Zend_Db_Select    
 * Результатом всегда является @see Select,
 * а @see \Zend_Db_Select добавил лишь для удобства навигации в среде разработки:
 * @see Select уточняет многие свои методы посредством PHPDoc в шапке,
 * и утрачивается возможность удобного перехода в среде разработки к реализации этих методов. 
 */
function df_db_from($t, $cols = '*', $schema = null) {return df_select()->from(
	is_array($t) ? $t : df_table($t), $cols, $schema
);}

/**
 * 2016-12-23
 * @used-by df_sentry_m()
 * http://stackoverflow.com/a/10414925
 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Backup/Model/ResourceModel/Helper.php#L178
 * @return string
 */
function df_db_version() {return dfcf(function() {return
	df_conn()->fetchRow("SHOW VARIABLES LIKE 'version'")['Value']
;});}

/**
 * 2015-08-23
 * Метод @see Varien_Db_Adapter_Pdo_Mysql::getPrimaryKeyName() возвращает не название колонки,
 * а слово «PRIMARY», поэтому он нам не подходит.
 * 2019-01-12 It is never used.
 * @param string $t
 * @return string|null
 */
function df_primary_key($t) {return dfcf(function($t) {return
	df_first(df_eta(dfa_deep(df_conn()->getIndexList($t), 'PRIMARY/COLUMNS_LIST')))
;}, func_get_args());}