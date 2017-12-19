<?php

final class EnumConsulationTypes{
	const PRODUCT=2;
	
	const STORAGE=3; 
	
	const PAYMENT=4;  
	
	const INVOICE=5;   
	
	const SPECIAL=6;
	
	
	public static function getConsulationType($type_id){
		switch($type_id){
			case EnumConsulationTypes::PRODUCT: return "商品咨询 ";break;
			case EnumConsulationTypes::STORAGE: return "库存及配送  ";break;
			case EnumConsulationTypes::PAYMENT: return "支付问题";break;
			case EnumConsulationTypes::INVOICE: return "发票及保修";break;
			case EnumConsulationTypes::SPECIAL: return "促销及赠品";break;
			default:return "N/A";break;
		}
	}
	
	public static function getConsulationTypes(){
		$results=array();
		
		$results[]=array(
			'value' => EnumConsulationTypes::PRODUCT,
			'name' => EnumConsulationTypes::getConsulationType(EnumConsulationTypes::PRODUCT)
		);
		
		$results[]=array(
			'value' => EnumConsulationTypes::STORAGE,
			'name' => EnumConsulationTypes::getConsulationType(EnumConsulationTypes::STORAGE)
		);
		
		$results[]=array(
			'value' => EnumConsulationTypes::PAYMENT,
			'name' => EnumConsulationTypes::getConsulationType(EnumConsulationTypes::PAYMENT)
		);
		
		$results[]=array(
			'value' => EnumConsulationTypes::INVOICE,
			'name' => EnumConsulationTypes::getConsulationType(EnumConsulationTypes::INVOICE)
		);
		
		$results[]=array(
			'value' => EnumConsulationTypes::SPECIAL,
			'name' => EnumConsulationTypes::getConsulationType(EnumConsulationTypes::SPECIAL)
		);
		
		
		return $results;
	}
}
final class EnumProdType{

	CONST T_Entity = 0;
	CONST T_Coupon = 1;
	CONST T_Card = 2;

	public static function getProdType($tag_id){
		switch($tag_id){
			case EnumProdType::T_Entity: return "菜品 ";break;
			case EnumProdType::T_Coupon: return "优惠劵";break;
			case EnumProdType::T_Card: return "储值卡";break;
			default:return "N/A";break;
		}
	}

	public static function getProdTypes(){
		$results=array();

		$results[]=array(
				'value' =>EnumProdType::T_Entity,
				'name' => EnumProdType::getProdType(EnumProdType::T_Entity)
		);

		$results[]=array(
				'value' => EnumProdType::T_Coupon,
				'name' => EnumProdType::getProdType(EnumProdType::T_Coupon)
		);

		$results[]=array(
				'value' => EnumProdType::T_Card,
				'name' => EnumProdType::getProdType(EnumProdType::T_Card)
		);

		return $results;
	}
}
final class EnumProductTags{
	CONST P_NEW = 1;
	
	CONST P_Special = 2;
	
	CONST P_Discount = 3;
	
	public static function getProductTag($tag_id){
		switch($tag_id){
			case EnumProductTags::P_NEW: return "新品 ";break;
			case EnumProductTags::P_Special: return "促销";break;
			case EnumProductTags::P_Discount: return "折扣";break;
			default:return "N/A";break;
		}
	}
	
	public static function getProductTags(){
		$results=array();
		
		$results[]=array(
			'value' => EnumProductTags::P_NEW,
			'name' => EnumProductTags::getProductTag(EnumProductTags::P_NEW)
		);
		
		$results[]=array(
			'value' => EnumProductTags::P_Special,
			'name' => EnumProductTags::getProductTag(EnumProductTags::P_Special)
		);
		
		$results[]=array(
			'value' => EnumProductTags::P_Discount,
			'name' => EnumProductTags::getProductTag(EnumProductTags::P_Discount)
		);
		
		return $results;
	}
}

final class EnumOrderStatus{
	CONST Cancel = 7;
	
	CONST UnPayment= 16;
	
	CONST Payment= 2;
	
	CONST Complete= 5;
	
	CONST Shipping= 3;
	
	CONST Shipped= 17;
	
	CONST Refunding= 13;
	
	CONST Refunded= 11;
	
	public static function getOrderStatusTitle($order_status_id){
		switch($order_status_id){
			case EnumOrderStatus::UnPayment: return "未付款订单";break;
			case EnumOrderStatus::Payment: return "已付款订单";break;
			case EnumOrderStatus::Complete: return "已完成订单";break;
			case EnumOrderStatus::Cancel: return "已取消订单";break;
			default:return "进行中";break;
		}
	}
}

// 自提点状态
final class EnumPointStatus{

    CONST ONLINE  = 1;

    CONST TESTING = 2;

    CONST OFFLINE = 0;
    
    public static function getOptions(){
        $results=array();
    
    
        $results[]=array(
            'value' => EnumPointStatus::ONLINE,
            'name' => EnumPointStatus::getPointStatusTitle(EnumPointStatus::ONLINE)
        );
    
        $results[]=array(
            'value' => EnumPointStatus::TESTING,
            'name' => EnumPointStatus::getPointStatusTitle(EnumPointStatus::TESTING)
        );

        $results[]=array(
        		'value' => EnumPointStatus::OFFLINE,
        		'name' => EnumPointStatus::getPointStatusTitle(EnumPointStatus::OFFLINE)
        );
    
        return $results;
    }

    public static function getPointStatusTitle($point_status){
        switch($point_status){
            case EnumPointStatus::OFFLINE: return "离线 ";break;
            case EnumPointStatus::ONLINE:  return "在线";break;
            case EnumPointStatus::TESTING: return "在线测试";break;
            default:return "Unknown";break;
        }
    }
}


final class EnumOrderRefundStatus{

	const PENDING = 'PENDING';  //待处理等待客服审核
	
	const PHASE1_PASSED = 'PHASE1_PASSED';  // 客服审核通过
	
	const PHASE1_REFUSED= 'PHASE1_REFUSED';  // 客服拒绝

	const PHASE2_PASSED='PHASE2_PASSED'; // 主管审核通过

	const PHASE2_REFUSED='PHASE2_REFUSED'; //主管拒绝

	const PAYING='PAYING';  //退款提交中等待平台反馈

	const DONE  = 'DONE';   // 退管已完成
	
	const FAIL  = 'FAIL';   // 退管已完成
	
	const ERROR  = 'ERROR';   // 退款取消


	public static function getOrderRefundAllStatus($skey=''){
		$results=array();

		$results [EnumOrderRefundStatus::PENDING] = array (
				'value' => EnumOrderRefundStatus::PENDING,
				'name' => EnumOrderRefundStatus::getOrderRefundStatus ( EnumOrderRefundStatus::PENDING ),
				'children' => array (
						array (
								'value' => EnumOrderRefundStatus::PHASE1_PASSED,
								'name' => EnumOrderRefundStatus::getOrderRefundStatus ( EnumOrderRefundStatus::PHASE1_PASSED ) 
						)
				) 
		);
		$results [EnumOrderRefundStatus::PHASE1_PASSED] = array (
				'value' => EnumOrderRefundStatus::PHASE1_PASSED,
				'name' => EnumOrderRefundStatus::getOrderRefundStatus ( EnumOrderRefundStatus::PHASE1_PASSED ),
				'children' => array (
						array (
								'value' => EnumOrderRefundStatus::PHASE2_PASSED,
								'name' => EnumOrderRefundStatus::getOrderRefundStatus ( EnumOrderRefundStatus::PHASE2_PASSED ) 
						) 
				) 
		);
		$results[EnumOrderRefundStatus::PHASE1_REFUSED]=array(
				'value' => EnumOrderRefundStatus::PHASE1_REFUSED,
				'name' => EnumOrderRefundStatus::getOrderRefundStatus(EnumOrderRefundStatus::PHASE1_REFUSED),
				'children' => array (
						array (
								'value' => EnumOrderRefundStatus::PHASE1_PASSED,
								'name' => EnumOrderRefundStatus::getOrderRefundStatus ( EnumOrderRefundStatus::PHASE1_PASSED )
						)
				)
		);
		$results[EnumOrderRefundStatus::PHASE2_PASSED]=array(
				'value' => EnumOrderRefundStatus::PHASE2_PASSED,
				'name' => EnumOrderRefundStatus::getOrderRefundStatus(EnumOrderRefundStatus::PHASE2_PASSED),
		);
		$results[EnumOrderRefundStatus::PHASE2_REFUSED]=array(
				'value' => EnumOrderRefundStatus::PHASE2_REFUSED,
				'name' => EnumOrderRefundStatus::getOrderRefundStatus(EnumOrderRefundStatus::PHASE2_REFUSED),
				'children' => array (
						array (
								'value' => EnumOrderRefundStatus::PHASE2_PASSED,
								'name' => EnumOrderRefundStatus::getOrderRefundStatus ( EnumOrderRefundStatus::PHASE2_PASSED ) 
						)
				) 
		);
		$results[EnumOrderRefundStatus::PAYING]=array(
				'value' => EnumOrderRefundStatus::PAYING,
				'name' => EnumOrderRefundStatus::getOrderRefundStatus(EnumOrderRefundStatus::PAYING),
				'children' => array (
						array (
								'value' => EnumOrderRefundStatus::ERROR,
								'name' => EnumOrderRefundStatus::getOrderRefundStatus ( EnumOrderRefundStatus::ERROR )
						))
		);
		$results[EnumOrderRefundStatus::ERROR]=array(
				'value' => EnumOrderRefundStatus::ERROR,
				'name' => EnumOrderRefundStatus::getOrderRefundStatus(EnumOrderRefundStatus::ERROR),
				'children' => array (
						array (
								'value' => EnumOrderRefundStatus::DONE,
								'name' => EnumOrderRefundStatus::getOrderRefundStatus (EnumOrderRefundStatus::DONE)
						),array (
								'value' => EnumOrderRefundStatus::FAIL,
								'name' => EnumOrderRefundStatus::getOrderRefundStatus (EnumOrderRefundStatus::FAIL)
						))
		);

		$results[EnumOrderRefundStatus::DONE]=array(
				'value' => EnumOrderRefundStatus::DONE,
				'name' => EnumOrderRefundStatus::getOrderRefundStatus(EnumOrderRefundStatus::DONE)
		);$results[EnumOrderRefundStatus::FAIL]=array(
				'value' => EnumOrderRefundStatus::FAIL,
				'name' => EnumOrderRefundStatus::getOrderRefundStatus(EnumOrderRefundStatus::FAIL)
		);
if($skey)
	return $results[$skey];
		else
		return $results;
	}

	public static function getOrderRefundStatus($type){
		switch($type){
			case EnumOrderRefundStatus::PENDING: return "等待审核";break;
			case EnumOrderRefundStatus::PHASE1_PASSED: return "客服审核通过";break;
			case EnumOrderRefundStatus::PHASE1_REFUSED: return "客服拒绝";break;
			case EnumOrderRefundStatus::PHASE2_PASSED: return "主管审核通过";break;
			case EnumOrderRefundStatus::PHASE2_REFUSED: return "主管拒绝";break;
			case EnumOrderRefundStatus::PAYING: return "退款提交";break;
			case EnumOrderRefundStatus::DONE: return "退款完成";break;
			case EnumOrderRefundStatus::FAIL: return "退款失败";break;
			case EnumOrderRefundStatus::ERROR: return "退款异常";break;
			default:return "N/A";break;
		}
	}

}

final class EnumPromotionTypes{
	
	const ZERO_BUY = 'ZERO_BUY';  //零元购
	
	const TOTAL_DONATION = 'TOTAL_DONATION';  // 满额赠送
	
	const REGISTER_DONATION= 'REGISTER_DONATION';  // 注册赠送
	
	const COMPONENT_PRICE='COMPONENT_PRICE'; //加价购
	
	const COMPONENT_DISCOUNT='COMPONENT_PRICE'; //搭配购
	
	const TOTAL_DISCOUNT='TOTAL_DISCOUNT';  //满额减
	
	const EXCHANGE_BUY  = 'EXCHANGE_BUY';   // 换购
	
	const PROMOTION_NORMAL = 'PROMOTION_NORMAL'; //促销特价
	
	const PROMOTION_SPECIAL = 'PROMOTION_SPECIAL'; //会员特价
	const PROMOTION_RUSH = 'PROMOTION_RUSH'; //抢购
	
	const PLATFORM_SPECIAL = 'PLATFORM_SPECIAL'; //平台特价
	
	public static function getPromotionTypes(){
		$results=array();
		/*
		$results[]=array(
				'value' => EnumPromotionTypes::PROMOTION_NORMAL,
				'name' => EnumPromotionTypes::getPromotionType(EnumPromotionTypes::PROMOTION_NORMAL)
		);
		
		$results[]=array(
			'value' => EnumPromotionTypes::ZERO_BUY,
			'name' => EnumPromotionTypes::getPromotionType(EnumPromotionTypes::ZERO_BUY)
		);
		
		$results[]=array(
			'value' => EnumPromotionTypes::TOTAL_DONATION,
			'name' => EnumPromotionTypes::getPromotionType(EnumPromotionTypes::TOTAL_DONATION)
		);
		
		$results[]=array(
		    'value' => EnumPromotionTypes::EXCHANGE_BUY,
		    'name' => EnumPromotionTypes::getPromotionType(EnumPromotionTypes::EXCHANGE_BUY)
		);
		*/
		$results[]=array(
			'value' => EnumPromotionTypes::PROMOTION_SPECIAL,
			'name' => EnumPromotionTypes::getPromotionType(EnumPromotionTypes::PROMOTION_SPECIAL)
		);
		$results[]=array(
				'value' => EnumPromotionTypes::PROMOTION_RUSH,
				'name' => EnumPromotionTypes::getPromotionType(EnumPromotionTypes::PROMOTION_RUSH)
		);
		
		return $results;
	}
	public static function clearCode($type){
		$type=explode("::", $type);//菜品特价code修正
		return $type[0];
	}
	public static function decodeCode($type){
		$code=explode("::", $type);//菜品特价code修正
		$code['code']=$code[0];
		$code['pid']=$code[1];
		return $code;
	}
	public static function encodeCode($code,$promotion){

		return $code;
	}
	public static function getPromotionType($type){
		$type=explode("::", $type)[0];//菜品特价code修正
		
		switch($type){
			case EnumPromotionTypes::ZERO_BUY: return "0元抢购 ";break;
			case EnumPromotionTypes::TOTAL_DONATION: return "满额赠送 ";break;
			case EnumPromotionTypes::REGISTER_DONATION: return "首次购赠 ";break;
			case EnumPromotionTypes::EXCHANGE_BUY: return "换购优惠 ";break;
			case EnumPromotionTypes::PROMOTION_NORMAL: return "促销活动";break;
			case EnumPromotionTypes::PROMOTION_SPECIAL: return "会员特价";break;
			case EnumPromotionTypes::PROMOTION_RUSH: return "抢购特价";break;
			case EnumPromotionTypes::PLATFORM_SPECIAL: return "平台特价";break;
			
			default:return "N/A";break;
		}
	}

	public static function getRules()
	{
		$registry=getRegistry();
		$language=$registry->get('language');
		return array(EnumConsulationRules::ZERO_BUY=>$language->get('entry_zero_buy'),EnumConsulationRules::GIFT=>$language->get('entry_gift'));
	}
}

final class EnumOrderSourceFrom{
	CONST DESKTOP = 1;	
	CONST TABLET= 2;
	CONST CLIENT= 0;	
	CONST MOBILE= 3;
	CONST ADMIN = 999;
	CONST TP    = 1000;

    public static function getOptions(){
        $results=array();

        $results[]=array(
            'value' => EnumOrderSourceFrom::DESKTOP,
            'name' => EnumOrderSourceFrom::getOptionValue(EnumOrderSourceFrom::DESKTOP)
        );

        $results[]=array(
            'value' => EnumOrderSourceFrom::TABLET,
            'name' => EnumOrderSourceFrom::getOptionValue(EnumOrderSourceFrom::TABLET)
        );
        
        $results[]=array(
        		'value' => EnumOrderSourceFrom::CLIENT,
        		'name' => EnumOrderSourceFrom::getOptionValue(EnumOrderSourceFrom::CLIENT)
        );

        $results[]=array(
            'value' => EnumOrderSourceFrom::MOBILE,
            'name' => EnumOrderSourceFrom::getOptionValue(EnumOrderSourceFrom::MOBILE)
        );

        $results[]=array(
            'value' => EnumOrderSourceFrom::ADMIN,
            'name' => EnumOrderSourceFrom::getOptionValue(EnumOrderSourceFrom::ADMIN)
        );
        $results[]=array(
        		'value' => EnumOrderSourceFrom::TP,
        		'name' => EnumOrderSourceFrom::getOptionValue(EnumOrderSourceFrom::TP)
        );

        return $results;
    }

    public static function getOptionValue($val){
        switch($val){
            case EnumOrderSourceFrom::DESKTOP: return "桌面";break;
            case EnumOrderSourceFrom::TABLET: return "平板";break;
            case EnumOrderSourceFrom::CLIENT: return "冷柜";break;
            case EnumOrderSourceFrom::MOBILE: return "手机";break;
            case EnumOrderSourceFrom::ADMIN: return "后台";break;
            case EnumOrderSourceFrom::TP: return "第三方";break;
            default:return "N/A";break;
        }
    }
}

/**
 * 管理权限状态
 * @author cww2000
 *
 */    
final class EnumAuthority{
    CONST NONE   = 0;  // 无
    CONST SEARCH = 1;  // 查
    CONST ADD    = 2;  // 增
    CONST MODIFY = 3;  // 改
    CONST DELETE = 4;  // 删

    public static function getOptions(){
        $results=array();

        $results[]=array(
            'value' => EnumAuthority::NONE,
            'name' => EnumAuthority::getOptionValue(EnumAuthority::NONE)
        );

        $results[]=array(
            'value' => EnumAuthority::SEARCH,
            'name' => EnumAuthority::getOptionValue(EnumAuthority::SEARCH)
        );

        $results[]=array(
            'value' => EnumAuthority::ADD,
            'name' => EnumAuthority::getOptionValue(EnumAuthority::ADD)
        );

        $results[]=array(
            'value' => EnumAuthority::MODIFY,
            'name' => EnumAuthority::getOptionValue(EnumAuthority::MODIFY)
        );

        $results[]=array(
            'value' => EnumAuthority::DELETE,
            'name' => EnumAuthority::getOptionValue(EnumAuthority::DELETE)
        );

        return $results;
    }

    public static function getOptionValue($val){
        switch($val){
            case EnumAuthority::DELETE: return "删除";break;
            case EnumAuthority::MODIFY: return "修改";break;
            case EnumAuthority::ADD: return "增加";break;
            case EnumAuthority::SEARCH: return "查询";break;
            case EnumAuthority::NONE: return "无";break;
            default:return "N/A";break;
        }
    }
}

/**
 * 操作方式
 * @author cww2000
 *
 */
final class EnumOperation{
    CONST INSERT   = 0;  // 追加
    CONST EDIT     = 1;  // 编辑
}