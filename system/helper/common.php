<?php
function cnWeek($date,$arr=array('周日','下周一','周二','周三','周四','周五','周六'))
{
	return $arr[date('w',strtotime($date))];
}
if (! function_exists ( 'array_column' )) {
	function array_column($input, $columnKey, $indexKey = NULL) {
		$columnKeyIsNumber = (is_numeric ( $columnKey )) ? TRUE : FALSE;
		$indexKeyIsNull = (is_null ( $indexKey )) ? TRUE : FALSE;
		$indexKeyIsNumber = (is_numeric ( $indexKey )) ? TRUE : FALSE;
		$result = array ();
		foreach ( ( array ) $input as $key => $row ) {
			if ($columnKeyIsNumber) {
				$tmp = array_slice ( $row, $columnKey, 1 );
				$tmp = (is_array ( $tmp ) && ! empty ( $tmp )) ? current ( $tmp ) : NULL;
			} else {
				$tmp = isset ( $row [$columnKey] ) ? $row [$columnKey] : NULL;
			}
			if (! $indexKeyIsNull) {
				if ($indexKeyIsNumber) {
					$key = array_slice ( $row, $indexKey, 1 );
					$key = (is_array ( $key ) && ! empty ( $key )) ? current ( $key ) : NULL;
					$key = is_null ( $key ) ? 0 : $key;
				} else {
					$key = isset ( $row [$indexKey] ) ? $row [$indexKey] : 0;
				}
			}
			$result [$key] = $tmp;
		}
		return $result;
	}
}

function getRegistry() {
    global $registry;

    return $registry;
}

function getConfig($key = '') {

    $registry = getRegistry();

    $config = $registry->get('config');


    if ($key) {
        return $config->get($key);
    } else {
        return $config;
    }
}

function generateSlno($total, $index, $page, $limit) {
    return $total - ($page - 1) * $limit - $index;
}

function getShortDate($datetime) {
    $registry = getRegistry();

    $language = $registry->get('language');

    return date($language->get('date_format_short'), strtotime($datetime));
}

function getDatetime($datetime) {
    $registry = getRegistry();

    $language = $registry->get('language');

    return date($language->get('date_format_short'), strtotime($datetime)) . ' ' . date($language->get('time_format_short'), strtotime($datetime));
}


function getModulePositions() {
    $positions = array();

    $language = getRegistry()->get('language');

//	$positions[] = array(
//		'position' => 'header',
//		'title'    => $language->get('text_position_header'),
//	);

    $positions[] = array(
        'position' => 'column_left',
        'title' => $language->get('text_position_column_left'),
    );

    $positions[] = array(
        'position' => 'column_right',
        'title' => $language->get('text_position_column_right'),
    );

    $positions[] = array(
        'position' => 'content_top',
        'title' => $language->get('text_position_content_top'),
    );
    $positions[] = array(
        'position' => 'content_bottom',
        'title' => $language->get('text_position_content_bottom'),
    );

    return $positions;
}

function getLayoutId() {
    $load = getRegistry()->get('load');
    $request = getRegistry()->get('request');
    $config = getRegistry()->get('config');

    $load->model('design/layout');
    $load->model('catalog/category');
    $load->model('catalog/product');
    $load->model('catalog/information');

    if (isset($request->get['route'])) {
        $route = $request->get['route'];
    } else {
        $route = 'common/home';
    }

    $layout_id = 0;

    if (substr($route, 0, 16) == 'product/category' && isset($request->get['path'])) {
        $path = explode('_', (string)$request->get['path']);

        $layout_id = getRegistry()->get('model_catalog_category')->getCategoryLayoutId(end($path));
    }

    if (substr($route, 0, 15) == 'product/product' && isset($request->get['product_id'])) {
        $layout_id = getRegistry()->get('model_catalog_product')->getProductLayoutId($request->get['product_id']);
    }

    if (substr($route, 0, 23) == 'information/information' && isset($request->get['information_id'])) {
        $layout_id = getRegistry()->get('model_catalog_information')->getInformationLayoutId($request->get['information_id']);
    }

    if (!$layout_id) {
        $layout_id = getRegistry()->get('model_design_layout')->getLayout($route);
    }

    if (!$layout_id) {
        $layout_id = $config->get('config_layout_id');
    }

    return $layout_id;
}


function getWebsiteName() {
    global $registry;
    $config = $registry->get('config');

    return $config->get('config_title');
}

function getCategoryName($category_id) {
    $name = '';

    $registry = getRegistry();

    $registry->get('load')->model('catalog/category');

    $result_info = $registry->get('model_catalog_category')->getCategory($category_id);

    if ($result_info) {
        $name = $result_info['name'];
    }

    return $name;
}

function getChildCategories($category_id, $recursion = TRUE) {
    $registry = getRegistry();

    $registry->get('load')->model('catalog/category');

    return $registry->get('model_catalog_category')->getChildCategories($category_id, $recursion);
}

function get_article_category_name($article_category_id) {
    $registrty = getRegistry();

    $registrty->get('load')->model('catalog/articlecate');

    $category_info = $registrty->get('model_catalog_articlecate')->getArticleCategory($article_category_id);

    if ($category_info) {
        return $category_info['name'];
    }

    return '';
}

function getFilterArticles($filter) {
    $registrty = getRegistry();

    $registrty->get('load')->model('catalog/article');

    $language = $registrty->get('language');

    $results = $registrty->get('model_catalog_article')->getArticles($filter);

    $url = '';

    if (isset($filter['article_category_id']) && $filter['article_category_id']) {
        $url .= "&article_category_id=" . $filter['article_category_id'];
    }

    if (isset($filter['width'])) {
        $width = $filter['width'];
    } else {
        $width = 0;
    }

    if (isset($filter['height'])) {
        $height = $filter['height'];
    } else {
        $height = 0;
    }

    $articles = array();

    foreach ($results as $result) {
        $articles[] = array(
            'article_id' => $result['article_id'],
            'title' => $result['name'],
            'thumb' => resizeThumbImage($result['image'], $width, $height, TRUE),
            'summary' => $result['summary'],
            'description' => html_entity_decode($result['description']),

            'date_added' => date($language->get('date_format_short'), strtotime($result['date_added'])),
            'href' => $registrty->get('url')->link('information/article', 'article_id=' . $result['article_id'] . $url)
        );

    }

    return $articles;
}

function getLatestProducts() {
    $registrty = getRegistry();

    $registrty->get('load')->model('catalog/product');

    $data = array(
        'sort' => 'p.date_added',
        'order' => 'DESC',
        'start' => 0,
        'limit' => 8
    );

    $products = array();

    $results = $registrty->get('model_catalog_product')->getProducts($data);

    foreach ($results as $result) {
        $products[] = array(
            'name' => $result['name'],
            'price' => $result['price'],
            'special' => $result['special'],
            'thumb' => resizeThumbImage($result['image'], 140, 140, TRUE),
            'href' => $registrty->get('url')->link('product/product', 'product_id=' . $result['product_id']),
        );
    }

    return $products;
}

function getSpecialProducts() {
    $registrty = getRegistry();

    $registrty->get('load')->model('catalog/product');

    $data = array(
        'sort' => 'p.date_added',
        'order' => 'DESC',
        'start' => 0,
        'limit' => 4
    );

    $products = array();

    $results = $registrty->get('model_catalog_product')->getProducts($data);

    foreach ($results as $result) {
        $products[] = array(
            'name' => $result['name'],
            'price' => $result['price'],
            'special' => $result['special'],
            'thumb' => resizeThumbImage($result['image'], 140, 140, TRUE),
            'href' => $registrty->get('url')->link('product/product', 'product_id=' . $result['product_id']),
        );
    }

    return $products;
}


function getHotsellProducts() {
    $registrty = getRegistry();

    $registrty->get('load')->model('catalog/product');

    $data = array(
        'sort' => 'p.date_added',
        'order' => 'DESC',
        'start' => 0,
        'limit' => 4
    );

    $products = array();

    $results = $registrty->get('model_catalog_product')->getProducts($data);

    foreach ($results as $result) {
        $products[] = array(
            'name' => $result['name'],
            'price' => $result['price'],
            'special' => $result['special'],
            'thumb' => resizeThumbImage($result['image'], 140, 140, TRUE),
            'href' => $registrty->get('url')->link('product/product', 'product_id=' . $result['product_id']),
        );
    }

    return $products;
}

function getCategoryLatestProducts($category_id, $limit = 8) {
    $registrty = getRegistry();

    $registrty->get('load')->model('catalog/product');

    $sort = NULL;

    $order = 'DESC';

    $data = array(
        'filter_category_id' => $category_id,
        'filter_sub_category' => TRUE,
        'order' => $order,
        'start' => 0,
        'limit' => $limit
    );

    $products = array();

    $results = $registrty->get('model_catalog_product')->getProducts($data);

    foreach ($results as $result) {
        $products[] = array(
            'name' => $result['name'],
            'price' => $result['price'],
            'special' => $result['special'],
            'thumb' => resizeThumbImage($result['image'], 140, 140, TRUE),
            'href' => $registrty->get('url')->link('product/product', 'product_id=' . $result['product_id']),
        );
    }

    return $products;

}


function getGroupInformations($group_id) {
    $config = getConfig();
    $db = getRegistry()->get('db');
    $url = getRegistry()->get('url');

    $sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN  " . DB_PREFIX . "information_description id ON (i.information_id=id.information_id) WHERE group_id=" . (int)$group_id . " AND language_id=" . (int)$config->get('config_language_id') . " AND status=1 ORDER BY i.sort_order ASC";

    $query = $db->query($sql);

    $informations = array();

    foreach ($query->rows as $result) {
        $informations[] = array(
            'name' => $result['title'],
            'href' => $url->link('information/information&information_id=' . $result['information_id'])
        );
    }

    return $informations;
}

function getGroupInformationsByCode($code) {
    $config = getConfig();
    $db = getRegistry()->get('db');

    $sql = "SELECT * FROM " . DB_PREFIX . "information_group WHERE code='" . $db->escape($code) . "' LIMIT 1";

    $query = $db->query($sql);

    if ($query->row) {
        return getGroupInformations($query->row['information_group_id']);
    } else {
        return array();
    }
}

function get_information_group_name($code) {
    $config = getConfig();
    $db = getRegistry()->get('db');

    $sql = "SELECT * FROM " . DB_PREFIX . "information_group ig LEFT JOIN  " . DB_PREFIX . "information_group_description igd ON (ig.information_group_id=igd.information_group_id) WHERE code='" . $db->escape($code) . "' AND language_id=" . (int)$config->get('config_language_id') . " LIMIT 1";

    $query = $db->query($sql);

    if ($query->row) {
        return $query->row['name'];
    } else {
        return '';
    }
}

function getSubstr($str, $len = 32, $dot = true) {
    $i = 0;
    $l = 0;
    $c = 0;
    $a = array();
    while ($l < $len) {
        $t = substr($str, $i, 1);
        if (ord($t) >= 224) {
            $c = 3;
            $t = substr($str, $i, $c);
            $l += 2;
        } elseif (ord($t) >= 192) {
            $c = 2;
            $t = substr($str, $i, $c);
            $l += 2;
        } else {
            $c = 1;
            $l++;
        }
        // $t = substr($str, $i, $c);
        $i += $c;
        if ($l > $len) break;
        $a[] = $t;
    }
    $re = implode('', $a);
    if (substr($str, $i, 1) !== false) {
        array_pop($a);
        ($c == 1) and array_pop($a);
        $re = implode('', $a);
        $dot and $re .= '...';
    }
    return $re;
}

function getInvoiceTypeDetail($inovice_type) {
    switch ($inovice_type) {
        case 1:
            return "普通发票";
            break;
        default:
            return "N/A";
            break;
    }
}

function getInvoiceHeadDetail($inovice_head) {
    switch ($inovice_head) {
        case 1:
            return "个人";
            break;
        case 2:
            return "单位";
            break;
        default:
            return "N/A";
            break;
    }
}

function getInvoiceContentDetail($inovice_content) {
    switch ($inovice_content) {
        case 1:
            return "明细";
            break;
        case 2:
            return "办公用品（附购物清单）";
            break;
        case 3:
            return "电脑配件";
            break;
        case 4:
            return "耗材";
            break;
        default:
            return "N/A";
            break;
    }
}
//已废弃20150402
function getCurrentSupplyPeriod(){
	
    $db = getRegistry()->get('db');

    $now = new DateTime();
    $nowStr=$now->format('Y-m-d H:i:s');
    $sql = "select * from ts_supply_period p where p.start_date<='{$nowStr}' and p.end_date>='{$nowStr}' limit 1";
    $query = $db->query($sql);
    return $query->row;
}
//已废弃20150402
function getTakeTimeOptions() {
    $options = array();
    
    $config = getConfig();
    $db = getRegistry()->get('db');
    
//	$supply_period_id = 8;
//    $sql = "SELECT * FROM " . DB_PREFIX . "supply_period where id=" . (int)$supply_period_id . " LIMIT 1";

    $supply_period = getCurrentSupplyPeriod();
    if($supply_period){
    	$p_start_date = date('Y-m-d', strtotime($supply_period['p_start_date']));
    	$p_end_date = date('Y-m-d', strtotime($supply_period['p_end_date']));
    	$now = date('Y-m-d',time());
    	if($p_start_date<$now){
    		$diff =  ceil(abs(strtotime($p_end_date) - strtotime($now))/86400);
    	}else{
    		$diff = ceil(abs(strtotime($p_end_date) - strtotime($p_start_date))/86400);
    	}
    	
    	for ($i=1;$i<$diff;$i++){
    		$options[] = date('Y-m-d', strtotime('+'.$i.' day'));
    	}
    	
    }
    
	/*
    $options[] = date('Y-m-d', strtotime('+1 day'));
    $options[] = date('Y-m-d', strtotime('+2 day'));
    $options[] = date('Y-m-d', strtotime('+3 day'));
    $options[] = date('Y-m-d', strtotime('+4 day'));
    $options[] = date('Y-m-d', strtotime('+5 day'));
    $options[] = date('Y-m-d', strtotime('+6 day'));
    $options[] = date('Y-m-d', strtotime('+7 day'));
    */
    
    

    return $options;

}


