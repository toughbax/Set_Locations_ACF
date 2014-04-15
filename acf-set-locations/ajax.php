<?php

// ADD TO LIST AJAX
add_action("wp_ajax_fs_acf_reset_locations", "fs_acf_reset_locations");
add_action("wp_ajax_nopriv_fs_acf_reset_locations", "my_must_login");

function fs_acf_reset_locations() {

	if ( !wp_verify_nonce( $_REQUEST['nonce'], "set_locations")) {
		exit("No naughty business please");
	}   

	$args = array(
		'post_type' => 'locations', 
		'posts_per_page' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC'
	); 
	$locations = new WP_Query($args); 
	foreach($locations->posts as $post):
		// print_r("test");
		delete_post_meta( $post->ID, '_top_value');
		delete_post_meta( $post->ID, '_left_value');

	endforeach;

	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$result = json_encode($result);
		echo $result;
	}else {
		header("Location: ".$_SERVER["HTTP_REFERER"]);
	}

	die();

}



add_action("wp_ajax_fs_acf_set_locations", "fs_acf_set_locations");
add_action("wp_ajax_nopriv_fs_acf_set_locations", "my_must_login");

function fs_acf_set_locations() {

	if ( !wp_verify_nonce( $_REQUEST['nonce'], "set_locations")) {
		exit("No naughty business please");
	}   

	$values = $_POST['values'];
	print_r($values);

	foreach ($values as $value){
		if($value['top_value']){
			$metadata = update_post_meta( $value['id'], '_top_value', $value['top_value']);
		}
		if($value['left_value']){
			$metadata = update_post_meta( $value['id'], '_left_value', $value['left_value']);
		}




		$metadata = get_post_meta( $value['id'], '_top_value',  true);
		print_r($metadata);
		$metadata = get_post_meta( $value['id'], '_left_value', true);
		print_r($metadata);


	}
	

	

	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$result = json_encode($result);
		echo $result;
	}else {
		header("Location: ".$_SERVER["HTTP_REFERER"]);
	}

	die();

}
