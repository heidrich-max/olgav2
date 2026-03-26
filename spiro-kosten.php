<?php
error_reporting(E_ALL);


$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path.'/wp-config.php';
include_once $path.'/wp-load.php';
include_once $path.'/wp-includes/wp-db.php';
include_once $path.'/wp-includes/pluggable.php';

function my_custom_email_content_type() {
    return 'text/html';
}

  function escape($s) {
      return htmlspecialchars(
          $s,
          ENT_QUOTES | ENT_HTML5 | ENT_DISALLOWED | ENT_SUBSTITUTE,
          'UTF-8'
      );
  }


global $wpdb;
global $wp_query;
global $post;

$fileContents = file_get_contents('https://doorboss.de/wp-content/lists/producer/003/produkt_preis.xml'); 

$xml = simplexml_load_string($fileContents); 


$sku_array = array();
$quantity_array = array();
$price_without_print_array = array();
$price_with_print_array = array();

foreach($xml as $x)
{
	$sku_array[] = $x->Artikelnummer;
	$quantity_array[] = intval($x->Quantity_1);
	$quantity_array[] = intval($x->Quantity_2);
	$quantity_array[] = intval($x->Quantity_3);
	$quantity_array[] = intval($x->Quantity_4);
	$quantity_array[] = intval($x->Quantity_5);
	$quantity_array[] = intval($x->Quantity_6);
	$quantity_array[] = intval($x->Quantity_7);
	$quantity_array[] = intval($x->Quantity_8);
	

	if(!empty(intval($x->Quantity_1)))
	{
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_1)]['OD'] = strval($x->Quantity_1_Price);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_1)]['MD'] = strval($x->Quantity_1_percent);
		
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_1)]['Profit_OD'] = strval($x->Profit_1_Without_Print);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_1)]['Profit_MD'] = strval($x->Profit_1_Print);
	}
	
	if(!empty(intval($x->Quantity_2)))
	{
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_2)]['OD'] = strval($x->Quantity_2_Price);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_2)]['MD'] = strval($x->Quantity_2_Percent);
		
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_2)]['Profit_OD'] = strval($x->Profit_2_Without_Print);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_2)]['Profit_MD'] = strval($x->Profit_2_Print);
	}
	
	if(!empty(intval($x->Quantity_3)))
	{
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_3)]['OD'] = strval($x->Quantity_3_Price);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_3)]['MD'] = strval($x->Quantity_3_Percent);
		
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_3)]['Profit_OD'] = strval($x->Profit_3_Without_Print);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_3)]['Profit_MD'] = strval($x->Profit_3_Print);
	}
	
	if(!empty(intval($x->Quantity_4)))
	{
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_4)]['OD'] = strval($x->Quantity_4_Price);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_4)]['MD'] = strval($x->Quantity_4_Percent);
		
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_4)]['Profit_OD'] = strval($x->Profit_4_Without_Print);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_4)]['Profit_MD'] = strval($x->Profit_4_Print);
	}
	
	if(!empty(intval($x->Quantity_5)))
	{
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_5)]['OD'] = strval($x->Quantity_5_Price);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_5)]['MD'] = strval($x->Quantity_5_Percent);
		
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_5)]['Profit_OD'] = strval($x->Profit_5_Without_Print);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_5)]['Profit_MD'] = strval($x->Profit_5_Print);
	}
	
	if(!empty(intval($x->Quantity_6)))
	{
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_6)]['OD'] = strval($x->Quantity_6_Price);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_6)]['MD'] = strval($x->Quantity_6_Percent);
		
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_6)]['Profit_OD'] = strval($x->Profit_6_Without_Print);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_6)]['Profit_MD'] = strval($x->Profit_6_Print);
	}
	
	if(!empty(intval($x->Quantity_7)))
	{
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_7)]['OD'] = strval($x->Quantity_7_Price);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_7)]['MD'] = strval($x->Quantity_7_Percent);
		
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_7)]['Profit_OD'] = strval($x->Profit_7_Without_Print);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_7)]['Profit_MD'] = strval($x->Profit_7_Print);
	}
	
	if(!empty(intval($x->Quantity_8)))
	{
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_8)]['OD'] = strval($x->Quantity_8_Price);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_8)]['MD'] = strval($x->Quantity_8_Percent);
		
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_8)]['Profit_OD'] = strval($x->Profit_8_Without_Print);
		$price_array[strval($x->Artikelnummer)][intval($x->Quantity_8)]['Profit_MD'] = strval($x->Profit_8_Print);
	}	
}
	

$quantity_array = array_unique($quantity_array);
$quantity_array = array_filter($quantity_array);

asort($quantity_array);
//var_dump($quantity_array);



//var_dump($price_array);


foreach($quantity_array as $qa)
{	
	$quantity_count = $wpdb->get_var("SELECT COUNT(id) as Countid FROM spiro_quantity WHERE quantity = '$qa'");
	
	if($quantity_count == 0)
	{
		$wpdb->insert(
			'spiro_quantity',
			array(
				'quantity' => $qa
			),
			array(
				'%d'
			)
		);
	}
}

foreach($sku_array as $sa)
{	
	$sku_count = $wpdb->get_var("SELECT COUNT(id) as Countid FROM spiro_variations WHERE name = '$sa'");
	
	if($sku_count == 0)
	{
		$wpdb->insert(
			'spiro_variations',
			array(
				'name' => "$sa"
			),
			array(
				'%s'
			)
		);
	}
}

foreach($price_array as $key => $value)
{
	$sku = $key;
	$variation_id = $wpdb->get_var("SELECT id FROM spiro_variations WHERE name = '$sku'");
	$count_variation = $wpdb->get_var("SELECT COUNT(id) FROM spiro_prices WHERE variation_id = '$variation_id'");
	
	
	foreach($value as $val_key => $val_value)
	{
		$quantity = $val_key;
		$quantity_id = $wpdb->get_var("SELECT id FROM spiro_quantity WHERE quantity = '$quantity'");
		$price_od = $val_value['OD'];
		$price_md = $val_value['MD'];
		
		$profit_od = $val_value['Profit_OD'];
		$profit_md = $val_value['Profit_MD'];

		if($count_variation == 0)
		{
			$wpdb->insert(
				'spiro_prices',
				array(
					'variation_id' => "$variation_id",
					'quantity_id' => "$quantity_id",
					'price_od' => "$price_od",
					'price_md' => "$price_md",
					'profit_od' => "$profit_od",
					'profit_md' => "$profit_md"
				),
				array(
					'%d',
					'%d',
					'%f',
					'%f',
					'%f',
					'%f'
				)
			);
		}
		else
		{
			$wpdb->update(
				'spiro_prices',
				array(
					'price_od' => "$price_od",
					'price_md' => "$price_md",
					'profit_od' => "$profit_od",
					'profit_md' => "$profit_md"
				),
				array( 
					'quantity_id' => "$quantity_id",
					'variation_id' => "$variation_id", 
				),
				array(
					'%f',
					'%f',
					'%f',
					'%f'
				),
				array( 
					'%d',
					'%d' 
				)
			);			
		}
	}
	
}

?>