<?php
/**
 * 2015-08-16
 * https://mage2.ru/t/95
 * https://mage2.pro/t/60
 * @param string $eventName
 * @param array(string => mixed) $data
 */
function df_dispatch($eventName, array $data = []) {Mage::dispatchEvent($eventName, $data);}