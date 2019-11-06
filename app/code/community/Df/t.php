<?php
$file = str_replace('\\', '/', __FILE__);/** @var string $file */
$base = substr($file, 0, strpos($file, 'app/code/community')); /** @var string $base */
/** @noinspection PhpIncludeInspection */
require_once "{$base}/app/Mage.php";
Mage::setIsDeveloperMode(true);
Mage::app('default');
// 2016-11-07
// Если система содержит несколько витрин, и ни одна из них не «default»,
// то надо указать витрину, которая будет использоваться PHPUnit, в настройках среды разработки.
// В IntelliJ IDEA это делается в графе «Environment variables» на экране настроек PHPUnit.
Mage::dispatchEvent(@$_SERVER['DF_PHPUNIT_STORE'] ?: 'default');
ob_start(); /** https://stackoverflow.com/a/4059399 */