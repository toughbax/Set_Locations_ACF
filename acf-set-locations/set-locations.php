<?php

class acf_field_set_locations extends acf_field
{
	
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'set_locations';
		$this->label = __("Set Locations",'acf');
		$this->category = __("Content",'acf');
		// $this->defaults = array(
		// 	'save_format'	=>	'object',
		// 	'preview_size'	=>	'thumbnail',
		// 	'library'		=>	'all'
		// );
		// $this->l10n = array(
		// 	'select'		=>	__("Select Image",'acf'),
		// 	'edit'			=>	__("Edit Image",'acf'),
		// 	'update'		=>	__("Update Image",'acf'),
		// 	'uploadedTo'	=>	__("uploaded to this post",'acf'),
		// );



		
		
		// do not delete!
    	parent::__construct();


    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.1.1'
		);

	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// register acf scripts
		wp_register_script( 'acf-input-set-locations', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version']);
		wp_register_script( 'acf-ajax-set-locations', $this->settings['dir'] . 'js/ajax.js', array('acf-input'), $this->settings['version']);
		wp_register_style( 'acf-input-set-locations', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] ); 
		
		
		// scripts
		wp_enqueue_script(array(
			'acf-input-set-locations',	
			'acf-ajax-set-locations',	
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-set-locations',	
		));
		
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		// vars
		$o = array(
			'class'		=>	'',
			'url'		=>	'',
		);
		
		if( $field['value'] && is_numeric($field['value']) )
		{
			$url = wp_get_attachment_image_src($field['value'], $field['preview_size']);
			
			$o['class'] = 'active';
			$o['url'] = $url[0];
		}
		
		?>

		<?php
			$nonce = wp_create_nonce("set_locations");
		?>
		<!-- Home Intro -->
		<div id="location-map" class="intro-section">
			
			<div class="map-wrap">
				<div id="map-locations">
					<?php $location_terms = get_terms('location-types'); ?>
					<?php foreach($location_terms as $term): ?>
						<?php $term_count = 1; ?>
						<?php 
						$args = array(
							'post_type' => 'locations', 
							'posts_per_page' => -1,
							'orderby' => 'menu_order',
							'order' => 'ASC',
							'tax_query' => array(
								array(
									'taxonomy' => 'location-types',
									'field' => 'slug',
									'terms' => $term->slug,
									'operator' => 'IN'
								)
							)

						); 
						$location_count = 0;
						$locations = new WP_Query($args); 
						?>
						<?php while ( $locations->have_posts() ) : $locations->the_post(); ?>

							<?php $post_id = $locations->posts[$location_count++]->ID; ?>
						
							<?php $left_position =  get_post_meta( $post_id, "_left_value", true); ?>
							<?php $top_position =  get_post_meta( $post_id, "_top_value", true); ?>

							<?php $position_class = ""; 

								if( $left_position > 84 ){
									$position_class .= "far-left ";
								}
								if( $top_position < 15 ){
									$position_class .= "far-top ";
								}

							?>
								<!-- <h1>left: <?php echo $left_position; ?></h1>
								<h1>top: <?php echo $top_position; ?></h1> -->
							<div class="beta-single-location single-location <?php echo $position_class; echo $term->slug; ?> count-<?php echo $term_count; ?>"
								data-left-value="<?php echo $left_position ?>" data-top-value="<?php echo $top_position ?>" data-location-id="<?php echo $post_id; ?>"
								style="<?php if($left_position){echo "left:$left_position%;position:absolute;";} if($top_position){echo "top:$top_position%;position:absolute;";} ?>"
							>
								<span class="location-count <?php echo $term->slug; ?>"><?php echo $term_count++; ?></span>
								<div class="roll-over <?php echo $term->slug; ?> <?php if(get_field('display_thumbnails') == TRUE ){ echo "has-thumb"; } ?>">
									<?php if(get_field('display_thumbnails') == TRUE ): ?>
										<?php the_post_thumbnail('thumbnail'); ?>
									<?php endif; ?>
									<h3><?php the_title(); ?></h3>
								</div>
							</div>				
						<?php endwhile; // end of the loop. ?>
					<?php endforeach; wp_reset_query();?>
				</div>
				
				<img src="" id="fs_image_map" style="max-width:100%;" class="image-map" alt="Location Map">
				
				<script>
					jQuery(document).ready(function($){
						var $imagesrc = $('#acf-local_map .acf-image-image').attr('src');
						
						$('#fs_image_map').attr('src',$imagesrc);
					});
				</script>


			</div>
			<div class="row">
				<div class="large-12 columns">								
					<div>
						<a 
							href="#" 
							class="button" 
							id="save-map"
							class="save-map"
							data-nonce="<?php echo $nonce  ?>"
							data-admin-url='<?php echo admin_url( "admin-ajax.php" ) ?>'
						>
							Save Map
						</a>
						<a 
							href="#" 
							class="button" 
							id="reset-map"
							class="reset-map"
							data-nonce="<?php echo $nonce  ?>"
							data-admin-url='<?php echo admin_url( "admin-ajax.php" ) ?>'
						>
							Reset Map
						</a>
					</div>
				</div>
			</div>
		</div>



		<?php
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		
		
	}
	

   		

	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{

	}
	
	
}

new acf_field_set_locations();

?>