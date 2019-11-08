<?php
use Df\Core\Helper\Text;
/**
 * 2015-11-22
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote_double($text) {return df_t()->quote($text, Text::QUOTE__DOUBLE);}

/**
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote_russian($text) {return df_t()->quote($text, Text::QUOTE__RUSSIAN);}

/**
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote_single($text) {return df_t()->quote($text, Text::QUOTE__SINGLE);}