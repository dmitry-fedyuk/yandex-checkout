<?php
namespace Df\Directory\Model;
use Df\Directory\Model\ResourceModel\Country\Collection;
// 2016-05-20
final class Country extends \Mage_Directory_Model_Country {
	/**
	 * 2016-05-20
	 * Не получается сделать этот метод виртуальным,
	 * потому что тогда getIso2Code() будет обращаться к полю iso_2_code.
	 * @return string|null
	 */
	function getIso2Code() {return $this['iso2_code'];}

	/**
	 * 2016-05-20
	 * Не получается сделать этот метод виртуальным,
	 * потому что тогда getIso3Code() будет обращаться к полю iso_3_code.
	 * @return string|null
	 */
	function getIso3Code() {return $this['iso3_code'];}

	/**
	 * 2016-05-20
	 * @return Collection
	 */
	static function c() {return new Collection;}
	/** @return Collection */
	static function cs() {return Collection::s();}
}

