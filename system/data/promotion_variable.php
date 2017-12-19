<?php

final class EnumConsulationRules{
	
	const ZERO_BUY = 'ZERO_BUY';
	
	const GIFT = 'GIFT';
	
	public static function getZeroBuy()
	{
		return EnumConsulationRules::ZERO_BUY;
	}
	
	public static function getGift()
	{
		return EnumConsulationRules::GIFT;
	}
	
	
	public static function getRules()
	{
		$registry=getRegistry();
		$language=$registry->get('language');
		return array(EnumConsulationRules::ZERO_BUY=>$language->get('entry_zero_buy'),EnumConsulationRules::GIFT=>$language->get('entry_gift'));
	}
}

