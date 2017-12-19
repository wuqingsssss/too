<?php
final class Cart {
	protected $registry;
	private $periodscount = 0;
	public $periods = array ();
	public $period = array ();
	public $sequence;
	public function __construct($registry) {
		$this->registry = $registry;

		$this->setPeriods ();
		
		if (isset ( $this->session->data ['sequence'] )&&$this->session->data ['sequence']!==false) {
			$this->setPeriod ($this->session->data ['sequence'] ); // 取得当前菜品周期
		} else {
			$this->setPeriod ( key ( $this->periods ) );
		}
		if (! isset ( $this->session->data ['cart'] ) || ! is_array ( $this->session->data ['cart'] )) {
			$this->session->data ['cart'] = array ();
			$this->session->data ['cart']['expires']=strtotime(date("Y/m/d",time()+3600*23));
		}
	}
	public function __get($key) {
		return $this->registry->get ( $key );
	}
	public function __set($key, $value) {
		$this->registry->set ( $key, $value );
	}
	public function setPeriods() {
		$cache='SupplyPeriods' ;
		if ($this->mem) {
			
			$memNameSpace=$this->mem->get_namespace('SupplyPeriods');
			$supplyperiods = $this->mem->get ($memNameSpace.'.'.DIR_DIR.'.'. $cache );
			$this->log_db->debug ( 'memcache:get:SupplyPeriods'.$cache );
		} else {
			$supplyperiods = $this->cache->get ( $cache );
			$this->log_db->debug ( 'cache:get:SupplyPeriods'.$cache );
		}
		if (! $supplyperiods) {
			$this->load->model ( 'catalog/supply_period' );
			$searcher = array (
					'filter_show_date' => date ( 'Y-m-d H:i:s', time () ),
					'start' => 0,
					'limit' => getShowLimit () 
			);
			
			$supplyperiods = $this->model_catalog_supply_period->getSupplyPeriods ( $searcher );
			
			if ($this->mem) {
				$memNameSpace=$this->mem->get_namespace('SupplyPeriods');
				$this->mem->set ($memNameSpace.'.'.DIR_DIR.'.'. $cache , $supplyperiods, 0, 1800 );
				$this->log_db->debug ( 'memcache:set:SupplyPeriods'.$cache );
			} else {
				
				$this->cache->set ( $cache , $supplyperiods, strtotime ( date ( "Y/m/d", time () + 3600 * 24 ) ) );
				$this->log_db->debug ( 'cache:set:SupplyPeriods'.$cache );
			}
		}
		
		$this->periods = $supplyperiods;
		
		return $this->periods;
	}
	public function getPeriods() {
		return $this->periods;
	}
	public function setPeriod($sequence) {
		if ($sequence !== false && array_key_exists ( $sequence, $this->periods )) {
			$this->session->data ['sequence'] = $sequence;
			$this->sequence = $sequence;
			$this->period = $this->periods [$sequence];
		} else {
			if (! $this->periods) {
				$this->session->data ['sequence'] = false;
				$this->sequence = false;
				$this->period = array ();
				$this->periods = array ();
			}
		}
		return $this->period;
	}
	public function getPeriod() {
		return $this->period;
	}

	public function getAdditionalDate() {
		if (isset ( $this->session->data ['cart'] [$this->sequence] ['additional'] ) && $this->session->data ['cart'] [$this->sequence] ['additional'] != ''&&date ( "Y-m-d", strtotime ( $this->session->data ['cart'] [$this->sequence] ['additional'] ) ) >= (date("Y-m-d",time()+86400)))
			return $this->session->data ['cart'] [$this->sequence] ['additional'];
		else
			return false;
	}
	
	/**
	 * 获得拼团附加信息
	 * @return boolean
	 */
	public function getAdditionalDate4Group() {
	    if (isset ( $this->session->data ['cart'] ['groupbuy'] ['additional'] ) && $this->session->data ['cart'] ['groupbuy'] ['additional'] != ''&&date ( "Y-m-d", strtotime ($this->session->data ['cart'] ['groupbuy'] ['additional'] ) ) >= (date("Y-m-d",time()+86400)))
	        return $this->session->data ['cart'] ['groupbuy'] ['additional'];
	    else
	        return false;
	}
	
	/**
	 * 设置附加信息（取菜日期等）
	 * 
	 * @param unknown $val        	
	 */
	public function setAdditionalDate($val) {
		$this->session->data ['cart'] [$this->sequence] ['additional'] = $val;
	}
	
	
	/**
	 * 设置拼团附加信息（取菜日期等）
	 *
	 * @param unknown $val
	 */
	public function setAdditionalDate4Group($val) {
	    $this->session->data ['cart'] ['groupbuy'] ['additional'] = $val;
	}
	
	
	/**
	 * 获取购物车拼团商品信息
	 *
	 * @return multitype:
	 */
	public function getGoods4Group() {
	    if (isset ( $this->session->data ['cart'] ['groupbuy'] ['goods'] ))
	        return $this->session->data ['cart'] ['groupbuy'] ['goods'];
	    else
	        return false;
	}
	
	
	/**
	 * 获取购物车商品信息
	 * 
	 * @return multitype:
	 */
	public function getGoods() {
		if (isset ( $this->session->data ['cart'] [$this->sequence] ['goods'] ))
			return $this->session->data ['cart'] [$this->sequence] ['goods'];
		else
			return array ();
	}
	
	/**
	 * 获取购物车商品详情
	 * 
	 * @return multitype:multitype:number unknown boolean NULL Ambigous <multitype:, mixed> multitype: multitype:multitype:string unknown NULL multitype:unknown NULL Ambigous <multitype:, mixed> multitype:unknown NULL multitype:multitype:unknown
	 */
	public function getProducts() {
		$product_data = array ();
		
		foreach ( $this->getGoods () as $key => $quantity ) {
			$product=$this->key_decode($key);
			$product_id = $product [0];

			
			$stock = 1;
			
			// Options
			if (isset ( $product [1] ) && $product [1]) {
				$options = $product [1] ;
			} else {
				$options = array ();
			}
			
			if (isset ( $product [2] ) && $product [2]) {
				
				$promotion =  $product [2];
				
			} else {
				$promotion = array ();
			}
			
			// 查询商品信息
			// /$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
			
			$periods = $this->getPeriods ();
			
			$this->load->model ( 'catalog/product' );
			$product_info = $this->model_catalog_product->getProduct ( $product_id, $periods [$this->sequence] ['id'] );
			
			if ($product_info) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;
				
				$option_data = array ();
				
				// 查询附购信息
				/*
				foreach ( $options as $product_option_id => $option_value ) {
					$option_query = $this->db->query ( "SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . ( int ) $product_option_id . "' AND po.product_id = '" . ( int ) $product_id . "' AND od.language_id = '" . ( int ) $this->config->get ( 'config_language_id' ) . "'" );
					
					if ($option_query->num_rows) {
						if ($option_query->row ['type'] == 'select' || $option_query->row ['type'] == 'radio' || $option_query->row ['type'] == 'autocomplete') {
							$option_value_query = $this->db->query ( "SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . ( int ) $option_value . "' AND pov.product_option_id = '" . ( int ) $product_option_id . "' AND ovd.language_id = '" . ( int ) $this->config->get ( 'config_language_id' ) . "'" );
							
							if ($option_value_query->num_rows) {
								if ($option_value_query->row ['price_prefix'] == '+') {
									$option_price += $option_value_query->row ['price'];
								} elseif ($option_value_query->row ['price_prefix'] == '-') {
									$option_price -= $option_value_query->row ['price'];
								}
								
								if ($option_value_query->row ['points_prefix'] == '+') {
									$option_points += $option_value_query->row ['points'];
								} elseif ($option_value_query->row ['points_prefix'] == '-') {
									$option_points -= $option_value_query->row ['points'];
								}
								
								if ($option_value_query->row ['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row ['weight'];
								} elseif ($option_value_query->row ['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row ['weight'];
								}
								
								if ($option_value_query->row ['subtract'] && (! $option_value_query->row ['quantity'] || ($option_value_query->row ['quantity'] < $quantity))) {
									$stock = false;
								}
								
								$option_data [] = array (
										'product_option_id' => $product_option_id,
										'product_option_value_id' => $option_value,
										'option_id' => $option_query->row ['option_id'],
										'option_value_id' => $option_value_query->row ['option_value_id'],
										'name' => $option_query->row ['name'],
										'option_value' => $option_value_query->row ['name'],
										'type' => $option_query->row ['type'],
										'quantity' => $option_value_query->row ['quantity'],
										'subtract' => $option_value_query->row ['subtract'],
										'price' => $option_value_query->row ['price'],
										'price_prefix' => $option_value_query->row ['price_prefix'],
										'points' => $option_value_query->row ['points'],
										'points_prefix' => $option_value_query->row ['points_prefix'],
										'weight' => $option_value_query->row ['weight'],
										'weight_prefix' => $option_value_query->row ['weight_prefix'] 
								);
							}
						} elseif ($option_query->row ['type'] == 'checkbox' && is_array ( $option_value )) {
							foreach ( $option_value as $product_option_value_id ) {
								$option_value_query = $this->db->query ( "SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . ( int ) $product_option_value_id . "' AND pov.product_option_id = '" . ( int ) $product_option_id . "' AND ovd.language_id = '" . ( int ) $this->config->get ( 'config_language_id' ) . "'" );
								
								if ($option_value_query->num_rows) {
									if ($option_value_query->row ['price_prefix'] == '+') {
										$option_price += $option_value_query->row ['price'];
									} elseif ($option_value_query->row ['price_prefix'] == '-') {
										$option_price -= $option_value_query->row ['price'];
									}
									
									if ($option_value_query->row ['points_prefix'] == '+') {
										$option_points += $option_value_query->row ['points'];
									} elseif ($option_value_query->row ['points_prefix'] == '-') {
										$option_points -= $option_value_query->row ['points'];
									}
									
									if ($option_value_query->row ['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row ['weight'];
									} elseif ($option_value_query->row ['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row ['weight'];
									}
									
									if ($option_value_query->row ['subtract'] && (! $option_value_query->row ['quantity'] || ($option_value_query->row ['quantity'] < $quantity))) {
										$stock = false;
									}
									
									$option_data [] = array (
											'product_option_id' => $product_option_id,
											'product_option_value_id' => $product_option_value_id,
											'option_id' => $option_query->row ['option_id'],
											'option_value_id' => $option_value_query->row ['option_value_id'],
											'name' => $option_query->row ['name'],
											'option_value' => $option_value_query->row ['name'],
											'type' => $option_query->row ['type'],
											'quantity' => $option_value_query->row ['quantity'],
											'subtract' => $option_value_query->row ['subtract'],
											'price' => $option_value_query->row ['price'],
											'price_prefix' => $option_value_query->row ['price_prefix'],
											'points' => $option_value_query->row ['points'],
											'points_prefix' => $option_value_query->row ['points_prefix'],
											'weight' => $option_value_query->row ['weight'],
											'weight_prefix' => $option_value_query->row ['weight_prefix'] 
									);
								}
							}
						} elseif ($option_query->row ['type'] == 'text' || $option_query->row ['type'] == 'textarea' || $option_query->row ['type'] == 'file' || $option_query->row ['type'] == 'date' || $option_query->row ['type'] == 'datetime' || $option_query->row ['type'] == 'time') {
							$option_data [] = array (
									'product_option_id' => $product_option_id,
									'product_option_value_id' => '',
									'option_id' => $option_query->row ['option_id'],
									'option_value_id' => '',
									'name' => $option_query->row ['name'],
									'option_value' => $option_value,
									'type' => $option_query->row ['type'],
									'quantity' => '',
									'subtract' => '',
									'price' => '',
									'price_prefix' => '',
									'points' => '',
									'points_prefix' => '',
									'weight' => '',
									'weight_prefix' => '' 
							);
						}
					}
				}*/
				
				if ($this->customer->isLogged ()) {
					$customer_group_id = $this->customer->getCustomerGroupId ();
				} else {
					$customer_group_id = $this->config->get ( 'config_customer_group_id' );
				}
				
				$price = $product_info ['price'];
				
				// Reward Points
				$query = $this->db->query ( "SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . ( int ) $product_id . "' AND customer_group_id = '" . ( int ) $customer_group_id . "'" );
				
				if ($query->num_rows) {
					$reward = $query->row ['points'];
				} else {
					$reward = 0;
				}
				
				// Downloads
				$download_data = array ();
				
				$download_query = $this->db->query ( "SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . ( int ) $product_id . "' AND dd.language_id = '" . ( int ) $this->config->get ( 'config_language_id' ) . "'" );
				
				foreach ( $download_query->rows as $download ) {
					$download_data [] = array (
							'download_id' => $download ['download_id'],
							'name' => $download ['name'],
							'filename' => $download ['filename'],
							'mask' => $download ['mask'],
							'remaining' => $download ['remaining'] 
					);
				}
				if($product_info ['available']!='1')
				{
					$stock=$product_info ['available'];
				}				
				// Stock
				elseif($product_info ['subtract']&&(! $product_info ['quantity'] || ($product_info ['quantity'] < $quantity))) {
					$stock = 3;
				}
				
				
				
				if ($option_price) {
					$price = $option_price;
				}
				
				$product_data [$key] = array (
						'key' => $key,
						'product_id' => $product_info ['product_id'],
						'name' => $product_info ['name'],
						'model' => $product_info ['sku'],
						'prod_type' => $product_info ['prod_type'],
						'shipping' => $product_info ['shipping'],
						'image' => $product_info ['image'],
						'donation' => $product_info ['donation'],
						'option' => $option_data,
						'download' => $download_data,
						'quantity' => $quantity,
						'minimum' => $product_info ['minimum'],
						'subtract' => $product_info ['subtract'],
						'stock' => $stock,
						'promotion' => $promotion, // 促销信息
						'price' => $price,
						'total' => isset ( $promotion ['promotion_price'] ) ? ( float ) $promotion ['promotion_price'] * $quantity : $price * $quantity,
						'reward' => $reward * $quantity,
						'points' => ($product_info ['points'] + $option_points) * $quantity,
						'tax_class_id' => $product_info ['tax_class_id'],
						'weight' => ($product_info ['weight'] + $option_weight) * $quantity,
						'weight_class_id' => $product_info ['weight_class_id'],
						'length' => $product_info ['length'],
						'width' => $product_info ['width'],
						'height' => $product_info ['height'],
						'additional' => $this->getAdditionalDate (),
						'length_class_id' => $product_info ['length_class_id'],
						'combine' => $product_info ['combine'],
						'packing_type' => $product_info ['packing_type'] 
				);
			} else {
				$this->remove ( $key );
			}
		}
		
		// TODO： 购物车检测，不符合条件的自动移除
		$total = 0;
		
		foreach ( $product_data as $key => $result ) {
			$total += $result ['total'];
		}
		
		if ($total < $this->config->get ( 'config_donation_limit' )) {
			$this->removePromotionProduct ( $product_data, EnumPromotionTypes::TOTAL_DONATION );
		}
		
		if ($total <= 0) {
			$this->removePromotionProduct ( $product_data, EnumPromotionTypes::REGISTER_DONATION );
		}
		//print_r($product_data);
		return $product_data;
	}
	
	/**
	 * 删除促销商品
	 * 
	 * @param unknown $products        	
	 * @param unknown $prom_code        	
	 */
	private function removePromotionProduct(&$products, $prom_code) {
		foreach ( $products as $key => $product ) {
			$product=$this->key_decode($key);
			$product_id = $product [0];
			$stock = true;
			
			// Options
			if (isset ( $product [1] ) && $product [1]) {
				$options = $product [1];
			} else {
				$options = array ();
			}
			
			if (isset ( $product [2] ) && $product [2]) {
				$promotion =$product [2] ;
			} else {
				$promotion = array ();
			}
			
			if (isset ( $promotion ['promotion_code'] ) && $promotion ['promotion_code'] == $prom_code) {
				$this->remove ( $key );
				
				unset ( $products [$key] );
			}
		}
	}
	
	
	public function key_decode($key){ 
		
		$product = explode ( ':', $key );
		$product_id = $product [0];
					
		// Options
		if (isset ( $product [1] ) && $product [1]) {
			$options = unserialize ( base64_decode ( $product [1] ) );
		} else {
			$options = array ();
		}
			
		if (isset ( $product [2] ) && $product [2]) {
			$promotion = unserialize ( base64_decode ( $product [2] ) );
		} else {
			$promotion = array ();
		}
		
		
		return array('product_id'=>$product_id,'options'=>$options,'promotion'=>$promotion,0=>$product_id,1=>$options,2=>$promotion);	
	}
	
	public function key_encode($product_id,$options = array(), $promotion = array()){
		$key = ( int ) $product_id;
		if ($options) {
			$key .= ':' . base64_encode ( serialize ( $options ) );
		} else {
			$key .= ':';
		}
		
		if ($promotion) {
			$key .= ':' . base64_encode ( serialize ( $promotion) );
		}
		return $key;
	}
	
	/**
	 * 追加拼团商品（只能一个）
	 * @param unknown $groupbuy_id
	 */
	public function addGroupbuy($groupbuy_id ){
	    $this->session->data ['cart'] ['groupbuy'] ['goods'] = $groupbuy_id;
	}
	
	/**
	 * 追加商品到购物车
	 * 
	 * @param unknown $product_id
	 *        	产品号
	 * @param number $qty
	 *        	数量
	 * @param unknown $options
	 *        	附购
	 * @param unknown $promotion
	 *        	促销信息（促销名称，促销价,促销限购）
	 */
	public function add($product_id, $qty = 1, $options = array(), $promotion = array()) {
		
		if (( int ) $qty && (( int ) $qty > 0)) {
			$qty=( int ) $qty;
			
		}else 
		{
			return;
		}
		
		$key=$this->key_encode($product_id,$options);

	  if ($promotion) {
	  	
			$key_promotion = $this->key_encode($product_id,$options,$promotion);
			$num=(int)$this->session->data ['cart'] [$this->sequence] ['goods'] [$key_promotion] ;
			
			if($promotion ['limited']){
				$this->load->model ( 'catalog/product' );
				
				
				$cartpnumber=$this->cartPromotionBoughtNumber($promotion['promotion_code'],$key_promotion);
				$productbought=$this->model_catalog_product->productPromotionBoughtNumber($promotion);

				
				if($promotion ['limited']>=($num+$qty+$productbought+$cartpnumber)||(int)$promotion ['limited']<=0){
					$this->session->data ['cart'] [$this->sequence] ['goods'] [$key_promotion]+= $qty;
				}
				else 
				{	
					$this->session->data ['cart'] [$this->sequence] ['goods'] [$key] += $qty;
				}			
			}
			else
			{
				$this->session->data ['cart'] [$this->sequence] ['goods'] [$key_promotion]+= $qty;
				
			}
		}
		else
			{
				$this->session->data ['cart'] [$this->sequence] ['goods'] [$key] += $qty;
			}
		
		$this->setCartToCustomer ();
	}
	public function setCartToCustomer() {
		if ($this->customer->isLogged ()){
			$this->db->query ( "UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape ( isset ( $this->session->data ['cart'] ) ? serialize ( $this->session->data ['cart'] ) : '' ) . "', wishlist = '" . $this->db->escape ( isset ( $this->session->data ['wishlist'] ) ? serialize ( $this->session->data ['wishlist'] ) : '' ) . "', ip = '" . $this->db->escape ( $this->request->server ['REMOTE_ADDR'] ) . "' WHERE customer_id = '" . ( int ) $this->session->data ['customer_id'] . "'" );
		}
	}
	
	/**
	 * 更新数量
	 *
	 * @param unknown $key
	 * @param unknown $qty
	 */
	public function getNum($key) {
		return (int)$this->session->data ['cart'] [$this->sequence] ['goods'] [$key];
	}
	
	/**
	 * 更新数量
	 * 
	 * @param unknown $key        	
	 * @param unknown $qty        	
	 */
	public function update($key, $qty) {
		if (( int ) $qty && (( int ) $qty > 0)) {
			
			$product=$this->key_decode($key);

			if($product['promotion']&&(int)$product['promotion']['limited']>0)
			{
				$this->load->model ( 'catalog/product' );

				$productbought=$this->model_catalog_product->productPromotionBoughtNumber($product['promotion']);
				$cartpnumber=$this->cartPromotionBoughtNumber($product['promotion']['promotion_code'],$key);

					if(($product['promotion']['limited'])<($qty+$productbought+$cartpnumber))
					{ 				
						$qty2=$qty;
						$qty=$product['promotion']['limited']-(int)$productbought-$cartpnumber;//最大可以购买的数量
						$qty2=$qty2-$qty;
					    $key2 = $this->key_encode($product['product_id'],$product['options']);
					    if($qty2>0)
					    $this->session->data ['cart'] [$this->sequence] ['goods'] [$key2] += (int)$qty2;
					}
			
			}

			$this->session->data ['cart'] [$this->sequence] ['goods'] [$key] = ( int ) $qty;
			
			$this->setCartToCustomer ();
		} else {
			$this->remove ( $key );
		}
	}
	
	/**
	 * 数量减-1
	 * 
	 * @param unknown $key        	
	 */
	public function down($key) {
		$qty = $this->session->data ['cart'] [$this->sequence] ['goods'] [$key];
		
		if ($qty > 1) {
			$this->session->data ['cart'] [$this->sequence] ['goods'] [$key] = ( int ) ($qty - 1);
			$this->setCartToCustomer ();
		} else {
			$this->remove ( $key );
		}
	}
	
	/**
	 * 数量+1
	 * 
	 * @param unknown $key        	
	 */
	public function up($key) {
			
		$qty = $this->session->data ['cart'] [$this->sequence] ['goods'] [$key];
		
		$product=$this->key_decode($key);

		if($product['promotion']&&(int)$product['promotion']['limited']>0)			
		{	
				$this->load->model ( 'catalog/product' );
				$cartpnumber=$this->cartPromotionBoughtNumber($product['promotion']['promotion_code']);

				$productbought=$this->model_catalog_product->productPromotionBoughtNumber($product['promotion']);
				if(($product['promotion']['limited'])<($qty+$cartpnumber+$productbought+1))
				{
					if($product['promotion']['limited']<$qty+$cartpnumber+$productbought)
						$this->update($product['promotion']['limited']-$productbought-$cartpnumber);

					$key2 = $this->key_encode($product['product_id'],$product['options']);

					$this->session->data ['cart'] [$this->sequence] ['goods'] [$key2] += 1;
					return;
				}
		}
		 
	
		$this->session->data ['cart'] [$this->sequence] ['goods'] [$key] = $qty + 1;
		
		
		
		$this->setCartToCustomer ();
	}
	
	/**
	 * 删除商品
	 * 
	 * @param unknown $key        	
	 */
	public function remove($key) {
		if (isset ( $this->session->data ['cart'] [$this->sequence] ['goods'] [$key] )) {
			unset ( $this->session->data ['cart'] [$this->sequence] ['goods'] [$key] );
			$this->setCartToCustomer ();
		}
	}
	
	/**
	 * 清空购物车
	 */
	public function clear() {
		unset($this->session->data ['cart']);
		$this->setCartToCustomer ();
	}
	
	/**
	 * 获取配送重量
	 * 
	 * @return number
	 */
	public function getWeight() {
		$weight = 0;
		
		foreach ( $this->getProducts () as $product ) {
			if ($product ['shipping']) {
				$weight += $this->weight->convert ( $product ['weight'], $product ['weight_class_id'], $this->config->get ( 'config_weight_class_id' ) );
			}
		}
		
		return $weight;
	}
	
	/**
	 * 小计
	 * 
	 * @return Ambigous <number, multitype:number>
	 */
	public function getSubTotal() {
		$total ['general'] = 0;
		$total ['promotion'] = 0;
		foreach ( $this->getProducts () as $product ) {
			if ($product ['promotion'] && $product ['promotion'] ['promotion_price']) { // 活动价格总计
				$total ['promotion'] += $product ['total'];
			} else {
				$total ['general'] += $product ['total'];
			}
		}
		$total ['total'] = $total ['promotion'] + $total ['general'];
		
		return $total;
	}
	
	/**
	 * 特权对象小计，汇总已被subtotal集成
	 * 
	 * @return Ambigous <number, multitype:number>
	 *        
	 *         public function getCouponSubTotal() {
	 *         $total = 0;
	 *        
	 *         foreach ($this->getProducts() as $product) {
	 *         if(empty($product['promotion'])){
	 *         $total += $product['total'];
	 *         }
	 *         }
	 *        
	 *         return $total;
	 *         }
	 */
	public function checkIfFreeShipping() {
		$total = 0;
		
		foreach ( $this->getProducts () as $product ) {
			$total += $product ['total'];
		}
		
		return $total;
	}
	public function getTaxes() {
		$taxes = array ();
		
		foreach ( $this->getProducts () as $product ) {
			if ($product ['tax_class_id']) {
				if (! isset ( $taxes [$product ['tax_class_id']] )) {
					$taxes [$product ['tax_class_id']] = $product ['total'] / 100 * $this->tax->getRate ( $product ['tax_class_id'] );
				} else {
					$taxes [$product ['tax_class_id']] += $product ['total'] / 100 * $this->tax->getRate ( $product ['tax_class_id'] );
				}
			}
		}
		
		return $taxes;
	}
	
	/**
	 * 计算带税总金额
	 * 
	 * @return number
	 */
	public function getTotal() {
		$total = 0;
		
		foreach ( $this->getProducts () as $product ) {
			$total += $this->tax->calculate ( $product ['total'], $product ['tax_class_id'], $this->config->get ( 'config_tax' ) );
		}
		
		return $total;
	}
	
	/**
	 * 订单返点
	 * 
	 * @return Ambigous <number, multitype:number>
	 */
	public function getTotalRewardPoints() {
		$total = 0;
		
		foreach ( $this->getProducts () as $product ) {
			$total += $product ['reward'];
		}
		
		return $total;
	}
	
	/**
	 * 获取购物车商品总数
	 * 
	 * @return Ambigous <number, multitype:number>
	 */
	public function countProducts() {
		$product_total = 0;
		
		$products = $this->getProducts ();
		
		foreach ( $products as $product ) {
			$product_total += $product ['quantity'];
		}
		
		return $product_total;
	}
	
	/**
	 * 购物车是否有商品
	 * 
	 * @return number
	 */
	public function hasProducts() {
		if (isset ( $this->session->data ['cart'] [$this->sequence] ['goods'] ))
			return count ( $this->session->data ['cart'] [$this->sequence] ['goods'] );
		else
			return 0;
	}
	
	
	public function hasStock4Group() {
		$stock = true;
	
		foreach ( $this->getProducts4Group () as $product ) {
			if ($product ['stock']!=1) {
				$stock = false;
			}
		}
	
		return $stock;
	}
	public function hasStock() {
		$stock = true;
		
		foreach ( $this->getProducts () as $product ) {
			if ($product ['stock']!=1) {
				$stock = false;
			}
		}
		
		return $stock;
	}
	public function hasShipping() {
		$shipping = false;
		
		foreach ( $this->getProducts () as $product ) {
			if ($product ['shipping']) {
				$shipping = true;
				
				break;
			}
		}
		
		return $shipping;
	}
	
	/**
	 * 拼团需要配送否
	 * @return boolean
	 */
	public function hasShipping4Group() {
	    $shipping = false;
	
	    foreach ( $this->getProducts () as $product ) {
	        if ($product ['shipping']) {
	            $shipping = true;
	
	            break;
	        }
	    }
	
	    return $shipping;
	}
	
	public function hasDownload() {
		$download = false;
		
		foreach ( $this->getProducts () as $product ) {
			if ($product ['download']) {
				$download = true;
				
				break;
			}
		}
		
		return $download;
	}
	/**
	 * 获取购物车指定促销商品KEY
	 *
	 * @param unknown $promotion_code
	 * @return key
	 */
	public function cartPromotionBoughtNumber($promotion_code,$key='') {
		$products = $this->getProducts ();
	
		$result = 0;
	    unset($products[$key]);
		foreach ( $products as $key => $pnum ) {
			$product=$this->key_decode($key);
			$product_id = $product [0];
		
			// Options
			if (isset ( $product [1] ) && $product [1]) {
				$options = $product [1] ;
			} else {
				$options = array ();
			}
				
			if (isset ( $product [2] ) && $product [2]) {
				$promotion =$product [2];
			} else {
				$promotion = array ();
			}

			if (isset ( $promotion ['promotion_code'] ) && $promotion ['promotion_code'] == $promotion_code) {
				$result +=(int)$pnum;
			}
		}
	
		return $result;
	}
	/**
	 * 获取购物车指定促销商品KEY
	 * 
	 * @param unknown $promotion_code        	
	 * @return key
	 */
	public function checkPromotionProductNumber($promotion_code) {
		$products = $this->getProducts ();
		
		$result = 0;
		
		foreach ( $products as $key => $pnum ) {
			$product=$this->key_decode($key);
			$product_id = $product [0];
			
			// Options
			if (isset ( $product [1] ) && $product [1]) {
				$options = $product [1] ;
			} else {
				$options = array ();
			}
			
			if (isset ( $product [2] ) && $product [2]) {
				$promotion =$product [2];
			} else {
				$promotion = array ();
			}
			
			if (isset ( $promotion ['promotion_code'] ) && $promotion ['promotion_code'] == $promotion_code) {
				$result = $key;
			}
		}
		
		return $result;
	}
}
?>