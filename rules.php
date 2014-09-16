<?php
	require_once( 'conn.php' );
	
	$getrules = mysqli_query( $con, '
		SELECT langs.`lang` AS lang,
			c_names.`name` AS cat_name,
			c_order.`order` AS cat_order,
			r_rules.`rule` AS rule_name,
			r_order.`order` AS rule_order, r_order.`weight` AS rule_weight, r_order.`last_edit` AS rule_edit
		FROM `categories` c_order
			INNER JOIN `categories_text` c_names ON c_order.`id` = c_names.`cat_id`
			INNER JOIN `languages` langs ON c_names.`lang` = langs.`lang`
			LEFT JOIN `rules` r_order ON c_order.`id` = r_order.`cat_id`
			LEFT JOIN `rules_text` r_rules ON r_order.`id` = r_rules.`rule_id`
		WHERE c_names.`lang` = r_rules.`lang`
		ORDER BY c_names.`lang`, c_order.`order`, r_order.`order`
	' );
	
	$rules = Array();
	$rules[ 'languages' ] = Array();
	while( $row = mysqli_fetch_array( $getrules ) ) {
		if( !( in_array( $row[ 'lang' ], $rules[ 'languages' ] ) ) ) array_push( $rules[ 'languages' ], $row[ 'lang' ] );
		
		if( !isset( $rules[ $row[ 'lang' ] ] ) ) $rules[ $row[ 'lang' ] ] = Array();
		if( !isset( $rules[ $row[ 'lang' ] ][ $row[ 'cat_order' ] ] ) ) {
			$rules[ $row[ 'lang' ] ][ $row[ 'cat_order' ] ] = Array();
			$rules[ $row[ 'lang' ] ][ $row[ 'cat_order' ] ][ 'name' ] = utf8_encode( $row[ 'cat_name' ] );
			$rules[ $row[ 'lang' ] ][ $row[ 'cat_order' ] ][ 'rules' ] = Array();
		}
		$rules[ $row[ 'lang' ] ][ $row[ 'cat_order' ] ][ 'rules' ][ $row[ 'rule_order' ] ] = Array();
		$rules[ $row[ 'lang' ] ][ $row[ 'cat_order' ] ][ 'rules' ][ $row[ 'rule_order' ] ][ 'text' ] = utf8_encode( $row[ 'rule_name' ] );
		$rules[ $row[ 'lang' ] ][ $row[ 'cat_order' ] ][ 'rules' ][ $row[ 'rule_order' ] ][ 'weight' ] = utf8_encode( $row[ 'rule_weight' ] );
		$rules[ $row[ 'lang' ] ][ $row[ 'cat_order' ] ][ 'rules' ][ $row[ 'rule_order' ] ][ 'last_edit' ] = utf8_encode( $row[ 'rule_edit' ] );
	}
	
	header('Content-Type: application/json');
	echo json_encode( $rules );
?>